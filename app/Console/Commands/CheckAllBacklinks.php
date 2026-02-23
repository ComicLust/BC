<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Http\Controllers\BacklinkController;

class CheckAllBacklinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backlinks:check-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tüm projelerdeki backlinkleri kontrol et';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new BacklinkController();
        foreach (Project::all() as $project) {
            $controller->bulkCheck($project);
        }
        $this->info('Tüm backlinkler kontrol edildi.');
    }
}
