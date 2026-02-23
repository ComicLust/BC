<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Backlink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Notifications\BacklinkStatusChanged;
use Illuminate\Support\Facades\Gate;
use DOMDocument;
use DOMXPath;
use App\Models\BacklinkCheckProgress;
use App\Jobs\CheckBacklinksJob;
use App\Exports\BacklinksExport;
use Maatwebsite\Excel\Facades\Excel;

class BacklinkController extends Controller
{
    public function store(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $request->validate([
            'target_urls' => 'required|string',
        ]);

        $urls = array_filter(explode("\n", $request->target_urls));
        $backlinks = [];

        foreach ($urls as $url) {
            $url = trim($url);
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $backlink = $project->backlinks()->create([
                    'source_url' => $project->target_url,
                    'target_url' => $url,
                    'status' => 'pending',
                ]);
                $backlinks[] = $backlink;
            }
        }

        return redirect()->route('projects.show', $project)
            ->with('success', count($backlinks) . ' backlink başarıyla eklendi.');
    }

    public function check(Project $project, Backlink $backlink)
    {
        Gate::authorize('update', $project);

        try {
            // Önce son kontrol zamanını güncelle
            $backlink->update([
                'last_checked_at' => now()
            ]);

            $response = Http::get($backlink->target_url);
            if (!$response->successful()) {
                $details = ['error_reason' => 'HTTP Hatası: ' . $response->status()];
                $backlink->update([
                    'status' => 'broken',
                    'details' => json_encode($details)
                ]);
                return redirect()->back()->with('error', 'Backlink kontrolü başarısız: Sayfa bulunamadı.');
            }

            $html = $response->body();
            $dom = new DOMDocument();
            @$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new DOMXPath($dom);

            // Kaynak URL'nin farklı formatlarını hazırla
            $sourceUrl = $backlink->source_url;
            $sourceUrlVariations = $this->getUrlVariations($sourceUrl);

            // Tüm linkleri bul
            $allLinks = $xpath->query("//a[@href]");
            $foundLinks = [];

            foreach ($allLinks as $link) {
                if (!$link instanceof \DOMElement) continue;

                $href = $link->getAttribute('href');
                foreach ($sourceUrlVariations as $variation) {
                    if (str_contains($href, $variation)) {
                        
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
                $status = 'active';
                
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
                    'status' => $status,
                    'details' => json_encode($details),
                    'anchor_text' => $finalAnchorText,
                    'rel_attribute' => $finalRel,
                    'last_checked_at' => now(),
                ]);

                return redirect()->back()->with('success', 'Backlink kontrolü tamamlandı.');
            }

            $details = ['error_reason' => 'Link sayfada bulunamadı'];
            $backlink->update([
                'status' => 'broken',
                'details' => json_encode($details)
            ]);

            return redirect()->back()->with('error', 'Backlink bulunamadı.');

        } catch (\Exception $e) {
            $details = ['error_reason' => 'Sistem Hatası: ' . $e->getMessage()];
            $backlink->update([
                'status' => 'broken',
                'details' => json_encode($details)
            ]);
            return redirect()->back()->with('error', 'Backlink kontrolü başarısız: ' . $e->getMessage());
        }
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
        $variations = array_unique($variations);
        
        return $variations;
    }

    private function isLinkVisible($link, $xpath)
    {
        try {
            // CSS visibility kontrolü
            $style = $link->getAttribute('style');
            if (str_contains($style, 'display: none') || str_contains($style, 'visibility: hidden')) {
                return false;
            }

            // Parent elementlerin visibility kontrolü
            $parent = $link->parentNode;
            while ($parent && $parent instanceof \DOMElement) {
                $style = $parent->getAttribute('style');
                if (str_contains($style, 'display: none') || str_contains($style, 'visibility: hidden')) {
                    return false;
                }
                $parent = $parent->parentNode;
            }

            return true;
        } catch (\Exception $e) {
            // Hata durumunda varsayılan olarak görünür kabul et
            return true;
        }
    }

    public function index(Project $project)
    {
        Gate::authorize('view', $project);
        
        $backlinks = $project->backlinks()->latest()->get();
        return view('backlinks.index', compact('project', 'backlinks'));
    }

    public function create(Project $project)
    {
        Gate::authorize('update', $project);
        return view('backlinks.create', compact('project'));
    }



    public function edit(Project $project, Backlink $backlink)
    {
        Gate::authorize('update', $project);
        return view('backlinks.edit', compact('project', 'backlink'));
    }

    public function update(Request $request, Project $project, Backlink $backlink)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'target_url' => 'required|url|max:255',
        ]);

        $backlink->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Backlink başarıyla güncellendi.');
    }

    public function destroy(Project $project, Backlink $backlink)
    {
        Gate::authorize('update', $project);
        
        $backlink->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Backlink başarıyla silindi.');
    }

    public function bulkCheck(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $filter = $request->input('filter', 'all');

        $query = $project->backlinks();
        if ($filter === 'active') {
            $query->where('status', 'active');
        } elseif ($filter === 'broken') {
            $query->where('status', 'broken');
        }

        $totalBacklinks = $query->count();

        if ($totalBacklinks === 0) {
            return response()->json([
                'message' => 'Kontrol edilecek link bulunamadı.',
                'status' => 'error'
            ], 422);
        }

        // Yeni progress kaydı oluştur
        $progress = BacklinkCheckProgress::create([
            'project_id' => $project->id,
            'total' => $totalBacklinks,
            'checked' => 0,
            'status' => 'running',
            'started_at' => now(),
        ]);

        // Projenin son kontrol zamanını güncelle
        $project->update([
            'last_checked_at' => now()
        ]);

        // Job'ı kuyruğa at
        CheckBacklinksJob::dispatch($project, $progress, $filter);

        // Hemen yanıt dön
        return response()->json([
            'progress_id' => $progress->id,
            'project_id' => $project->id,
            'message' => 'Toplu kontrol başlatıldı',
        ]);
    }

    public function export(Request $request, Project $project)
    {
        Gate::authorize('view', $project);
        
        $filter = $request->input('filter', 'all');
        $fileName = 'backlinks-' . $project->id . '-' . $filter . '-' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new BacklinksExport($project, $filter), $fileName);
    }

    public function progress(Project $project, BacklinkCheckProgress $progress)
    {
        // Yetki kontrolü
        Gate::authorize('update', $project);
        if ($progress->project_id !== $project->id) {
            abort(404);
        }
        return response()->json([
            'checked' => $progress->checked,
            'total' => $progress->total,
            'status' => $progress->status,
            'started_at' => $progress->started_at ? $progress->started_at->timestamp * 1000 : null,
            'result' => $progress->status === 'finished' ? $progress->result : null,
        ]);
    }
}
