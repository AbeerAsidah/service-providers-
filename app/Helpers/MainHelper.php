<?php

use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

if (!function_exists('error')) {
    function error(string $message = null, $errors = null,  $code = 401)
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors ?? [$message],
            'code' => $code,
        ], $code);
    }
}
if (!function_exists('success')) {
    function success($data = null, int $code = Response::HTTP_OK, $additionalData = [])
    {
        return response()->json(
            array_merge([
                'data' => $data ?? ['success' => true],
                'code' => $code
            ], $additionalData),
            $code
        );
    }
}
if (!function_exists('throwError')) {
    function throwError($message, $errors = null, int $code = Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        throw new HttpResponseException(response()->json(
            [
                'message' => $message,
                'errors' => $errors ?? [$message],
            ],
            $code
        ));
    }
}


if (!function_exists('paginate')) {
    function paginate(&$data, $paginationLimit = null)
    {
        $paginationLimit = $paginationLimit ?? config('app.pagination_limit');
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginatedStudents = collect($data)->forPage($page, $paginationLimit);

        // Create a LengthAwarePaginator-like structure
        $paginator = new LengthAwarePaginator(
            $paginatedStudents,
            count($data),
            $paginationLimit,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // Convert the paginator to an array with numerically indexed data
        $data = $paginator->toArray();
        $data['data'] = collect($data['data'])->values()->all();

        return $data;
    }
}
if (!function_exists('diffForHumans')) {
    function diffForHumans($time)
    {
        return Carbon::parse($time)->diffForHumans(Carbon::now(), [
            'long' => true,
            'parts' => 2,
            'join' => true,
        ]);
    }
}
