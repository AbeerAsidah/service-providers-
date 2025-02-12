<?php

namespace App\Services\Info;

use App\Models\Info;
use App\Models\User;
use App\Constants\Constants;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\UpdateInfoRequest;


class InfoService
{
    public function getAll():mixed
    {
        $local = App::getLocale();
        $isAdmin = Auth::user()?->hasRole(Constants::ADMIN_ROLE);
        $cacheKey = ($isAdmin ? 'admin' : 'app') . 'info.' . ((request()->header('locale') ?? 'all'));
        return Cache::rememberForever($cacheKey, function () use ($local, $isAdmin) {
            $data = Info::get();
            $formattedResponse = [];
            foreach ($data as $d) {
                $value = $d->value;
                if (in_array($d->super_key . '-' . $d->key, Info::$translatableKeys)) {
                    if ($isAdmin) {
                        $value = $d->value;
                    } else {
                        $value = $d->value[$local];
                    }
                }
                if ($d->super_key) {
                    $formattedResponse[$d->super_key][$d->key] = $value;
                } else {
                    $formattedResponse[$d->key] = $value;
                }
            }
            return $formattedResponse;
        });
    }




    public function insertOrUpdateData($data, $update = false): void
    {
        if ($update) {
            foreach ($data as $superKey => $datum) {
                foreach ($datum as $key => $item) {
                    Info::where('super_key', $superKey)
                        ->where('key', $key)
                        ->update(
                            [
                                'value' => is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : $item
                            ]
                        );
                }
            }
        } else {
            $dataToSeed = [];
            foreach ($data as $superKey => $datum) {
                foreach ($datum as $key => $item) {
                    $dataToSeed[] = [
                        'super_key' => $superKey,
                        'key' => $key,
                        'value' => is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : $item,
                    ];
                }
            }
            Info::insert($dataToSeed);
            Cache::flush();
        }
    }

    public function update(UpdateInfoRequest $request)
    {
        $validated = $request->validated();
        $infoData = Info::all()->groupBy('super_key');
        $dataToUpdated = [];
        foreach ($validated as $key => $value) {
            $explodedItem = explode('-', $key);
            if (count($explodedItem) === 3) {//this mean we have translation.
                if (!isset($dataToUpdated[$explodedItem[0]][$explodedItem[1]])) {
                    $decodedOldValue = $infoData->get($explodedItem[0])
                        ->where('key', $explodedItem[1])
                        ->first()->value;
                } else {
                    $decodedOldValue = $dataToUpdated[$explodedItem[0]][$explodedItem[1]];
                }
                
                $decodedOldValue[$explodedItem[2]] = $value;
                $dataToUpdated[$explodedItem[0]][$explodedItem[1]] = $decodedOldValue;
            } elseif (count($explodedItem) === 2) {//this mean we have translation.
                if (in_array($key, Info::$imageKeys)) {
                    $value = $request->file($key)->storePublicly('info/images', 'public');
                }
                $dataToUpdated[$explodedItem[0]][$explodedItem[1]] = $value;
            }
        }
        $this->insertOrUpdateData($dataToUpdated, true);
        return $this->getAll();

    }

}
