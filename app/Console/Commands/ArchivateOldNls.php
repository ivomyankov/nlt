<?php

namespace App\Console\Commands;

use App\Http\Controllers\ZipController;
use Illuminate\Console\Command;

class ArchivateOldNls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archivate:old-nls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archivates newsletters older than 30 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $old = new ZipController;
        $old->arhivateNewsletter();
        
        return Command::SUCCESS;
    }
}
