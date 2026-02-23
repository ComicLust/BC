<?php

namespace App\Jobs;

use App\Models\Backlink;
use App\Models\BacklinkCheckProgress;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Log;

class CheckBacklinksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600; // 1 saat

    protected $project;
    protected $progress;
    protected $filter;

    /**
     * Create a new job instance.
     * 
     * @param Project $project
     * @param BacklinkCheckProgress $progress
     * @param string $filter 'all', 'active', 'broken'
     */
    public function __construct(Project $project, BacklinkCheckProgress $progress, $filter = 'all')
    {
        $this->project = $project;
        $this->progress = $progress;
        $this->filter = $filter;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = $this->project->backlinks();

        if ($this->filter === 'active') {
            $query->where('status', 'active');
        } elseif ($this->filter === 'broken') {
            $query->where('status', 'broken');
        }

        $backlinks = $query->latest()->get();
        
        // Progress kaydını toplam sayıya göre güncelle (çünkü filtre uygulandı)
        $this->progress->update([
            'total' => $backlinks->count(),
            'checked' => 0
        ]);

        $results = [];

        foreach ($backlinks as $index => $backlink) {
            try {
                // Her istekte timeout süresini belirle (30 saniye) ve User-Agent ekle
                // 3 kez dene, her deneme arası 100ms bekle
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                ])->timeout(30)->retry(2, 500)->get($backlink->target_url);
                
                if (!$response->successful()) {
                    $this->markAsBroken($backlink, 'HTTP Hatası: ' . $response->status());
                    $results[] = $this->createResult($backlink);
                    $this->updateProgress($index + 1);
                    continue;
                }

                $html = $response->body();
                $found = $this->checkBacklinkInHtml($html, $backlink, $response);

                if ($found) {
                    $results[] = $this->createResult($backlink);
                } else {
                    $this->markAsBroken($backlink, 'Link sayfada bulunamadı');
                    $results[] = $this->createResult($backlink);
                }

            } catch (\Exception $e) {
                // Hata durumunda logla ama durma
                Log::warning("Backlink kontrol hatası ID: {$backlink->id} - {$e->getMessage()}");
                $this->markAsBroken($backlink, 'Sistem Hatası: ' . $e->getMessage());
                $results[] = $this->createResult($backlink);
            }

            // İlerlemeyi güncelle
            $this->updateProgress($index + 1);
        }

        // İşlem tamamlandı
        $this->progress->update([
            'status' => 'finished',
            'result' => $results,
            'finished_at' => now(),
        ]);
    }

    private function updateProgress($checkedCount)
    {
        $this->progress->update([
            'checked' => $checkedCount,
            'last_updated_at' => now(),
        ]);
    }

    private function markAsBroken(Backlink $backlink, $reason = null)
    {
        // Detay bilgisini JSON olarak saklayalım
        $details = $backlink->details ? json_decode($backlink->details, true) : [];
        if (!is_array($details)) $details = [];
        
        $details['error_reason'] = $reason;

        $backlink->update([
            'status' => 'broken',
            'last_checked_at' => now(),
            'details' => json_encode($details)
        ]);
        
        // Modeli yenile ki son veriler gelsin
        $backlink->refresh();
    }

    private function createResult(Backlink $backlink)
    {
        return [
            'id' => $backlink->id,
            'target_url' => $backlink->target_url,
            'status' => $backlink->status,
            'message' => $backlink->status === 'active' ? 'Backlink aktif' : 'Backlink kırık',
        ];
    }

    private function checkBacklinkInHtml($html, Backlink $backlink, $response)
    {
        try {
            $sourceUrl = $backlink->source_url;
            $sourceUrlVariations = $this->getUrlVariations($sourceUrl);

            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            // HTML'i yükle (UTF-8 desteği için hack)
            $loaded = $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();
            
            if ($loaded) {
                $xpath = new DOMXPath($dom);
                $allLinks = $xpath->query("//a[@href]");
                $foundLinks = [];
                
                foreach ($allLinks as $link) {
                    if (!$link instanceof \DOMElement) continue;
                    
                    $href = $link->getAttribute('href');
                    foreach ($sourceUrlVariations as $variation) {
                        if (stripos($href, $variation) !== false) {
                            
                            // Anchor Text
                            $anchorText = trim($link->textContent);
                            if (empty($anchorText)) {
                                // Resim linki olabilir mi?
                                $imgs = $link->getElementsByTagName('img');
                                if ($imgs->length > 0) {
                                    $img = $imgs->item(0);
                                    if ($img instanceof \DOMElement) {
                                        $anchorText = '[Görsel] ' . ($img->getAttribute('alt') ?: 'Alt etiketi yok');
                                    }
                                }
                            }

                            // Rel Attribute
                            $rel = $link->getAttribute('rel') ?: 'dofollow'; // Varsayılan dofollow

                            $foundLinks[] = [
                                'anchor_text' => $anchorText,
                                'rel_attribute' => $rel,
                                'found_url' => $href
                            ];
                            
                            // Bir varyasyon eşleştiyse diğer varyasyonlara bakmaya gerek yok
                            break;
                        }
                    }
                }
                
                if (!empty($foundLinks)) {
                    // Birden fazla link varsa birleştirip gösterelim
                    $uniqueAnchors = array_unique(array_column($foundLinks, 'anchor_text'));
                    $uniqueRels = array_unique(array_column($foundLinks, 'rel_attribute'));
                    
                    $finalAnchorText = implode(', ', $uniqueAnchors);
                    $finalRel = implode(', ', $uniqueRels);

                    $details = [
                        'found_links' => $foundLinks,
                        'count' => count($foundLinks)
                    ];

                    $backlink->update([
                        'status' => 'active',
                        'details' => json_encode($details),
                        'last_checked_at' => now(),
                        'anchor_text' => $finalAnchorText,
                        'rel_attribute' => $finalRel,
                    ]);
                    
                    return true;
                }
            }

        } catch (\Exception $e) {
            Log::error("HTML parse hatası: " . $e->getMessage());
            return false;
        }

        return false;
    }

    private function getUrlVariations($url)
    {
        $variations = [];
        
        // URL'den protokolü kaldır
        $urlWithoutProtocol = preg_replace('#^https?://#', '', $url);
        $variations[] = $urlWithoutProtocol;
        
        // www ile başlayan versiyonu
        if (!str_starts_with($urlWithoutProtocol, 'www.')) {
            $variations[] = 'www.' . $urlWithoutProtocol;
        }
        
        // www olmadan
        $variations[] = preg_replace('#^www\.#', '', $urlWithoutProtocol);
        
        // Tam URL'yi ekle
        $variations[] = $url;
        
        // http ve https versiyonlarını ekle
        $variations[] = 'http://' . $urlWithoutProtocol;
        $variations[] = 'https://' . $urlWithoutProtocol;
        
        // URL'yi parçalara ayır ve her bir parçayı ekle
        $parts = parse_url($url);
        if (isset($parts['host'])) {
            $variations[] = $parts['host'];
            if (isset($parts['path'])) {
                $variations[] = $parts['host'] . $parts['path'];
            }
        }
        
        // Tekrar eden değerleri temizle
        return array_unique($variations);
    }
}
