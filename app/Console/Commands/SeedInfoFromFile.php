<?php

namespace App\Console\Commands;

use App\Models\Info;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Services\Admin\Info\InfoService;

class SeedInfoFromFile extends Command
{
    public function __construct(protected InfoService $infoService)
    {
        parent::__construct() ;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'info:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = ' description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Info::truncate();
        $data = require app_path('Constants/SiteInfoArray.php');

        $this->infoService->insertOrUpdateData($data);

    }
}
