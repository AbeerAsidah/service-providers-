<?php

namespace Database\Seeders;

use App\Models\Info;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SiteInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require app_path('Constants/SiteInfoArray.php') ;
        Cache::forget('info.en');
        Cache::forget('info.ar');
        Cache::forget('info.all');
        Info::truncate();

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
    }
}
