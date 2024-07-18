<?php

namespace App\Console\Commands;

use App\Models\Info;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SeedInfoFromFile extends Command
{
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = require app_path('Constants/SiteInfoArray.php') ;
        
        Cache::forget('info.en');
        Cache::forget('info.ar');
        Cache::forget('info.');
        Cache::forget('info.none');
        
        $this->info('free cache info successfuly') ;
        
        Info::truncate();
        $this->info('truncate info successfuly') ;
        
      
        $i=0 ;
        foreach($data as $key => $value)
        {
            $model = new Info() ;

            $model->super_key = $value['super_key'] ?? null ;
            $model->key = $key ;
            $model->value = [
                        'en' => json_encode($value['value']['en']) ,
                        'ar' => json_encode($value['value']['ar'])  ,
                   ] ;

            $model->save() ;
        }

        $this->info('seed info successfuly') ;
    }
}
