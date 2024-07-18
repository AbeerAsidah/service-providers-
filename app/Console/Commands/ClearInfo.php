<?php

namespace App\Console\Commands;

use App\Models\Info;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'info:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cache::forget('info.en');
        Cache::forget('info.ar');
        Cache::forget('info.all');
    }
}
