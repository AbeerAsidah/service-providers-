<?php

namespace App\Http\Controllers;

use App\Models\Info;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Resources\InfoResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreInfoRequest;
use App\Http\Requests\UpdateInfoRequest;

class InfoController extends Controller
{


    /**
     * @OA\Get(
     *     path="/infos",
     *     summary="Get all info data",
     *     tags={"App , Info"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="query",
     *         description="Locale of the info data (optional)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"none", "ar", "en"})
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     * @OA\Get(
     *     path="/admin/infos",
     *     summary="Get all info data",
     *     tags={"Admin , Info"},
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function index(Request $request)
    {
        return Cache::rememberForever('info.' . $request->header('locale') ?? 'all', function () use ($request) {
            $data = Info::get();
            $formattedResponse = [];
            foreach ($data as $d) {
                if ($d->super_key) {
                    $formattedResponse[$d->super_key][$d->key] = !$request->header('locale') ? $d->getTranslations('value') : json_decode($d->value);
                } else {
                    $formattedResponse[$d->key] = !$request->header('locale') ? $d->getTranslations('value') : json_decode($d->value);
                }
            }
            return $formattedResponse;
        });
    }


    //todo complete it like icr project
    public function update(UpdateInfoRequest $request)
    {
        $data = $request->validated();
        Artisan::call('info:clear');
    }
}
