<?php

namespace App\Console\Commands;

use App\Models\Backlink;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Notifications\BacklinkStatusChanged;

class CheckBacklinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backlinks:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tüm backlinkleri kontrol eder ve durumlarını günceller';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backlinks = Backlink::where('status', '!=', 'broken')->get();

        foreach ($backlinks as $backlink) {
            try {
                $response = Http::get($backlink->url);
                $status = $response->successful() ? 'active' : 'broken';

                if ($status !== $backlink->status) {
                    $backlink->update(['status' => $status]);
                    $backlink->project->user->notify(new BacklinkStatusChanged($backlink));

                    // Bildirim oluştur
                    Notification::create([
                        'user_id' => $backlink->project->user_id,
                        'message' => "Backlink durumu değişti: {$backlink->url} - {$status}",
                        'type' => 'backlink_status',
                        'data' => [
                            'backlink_id' => $backlink->id,
                            'project_id' => $backlink->project_id,
                            'old_status' => $backlink->status,
                            'new_status' => $status,
                        ],
                    ]);
                }
            } catch (\Exception $e) {
                $this->error("Backlink kontrolü başarısız: {$backlink->url} - {$e->getMessage()}");
            }
        }

        $this->info('Backlink kontrolü tamamlandı.');
    }
}
