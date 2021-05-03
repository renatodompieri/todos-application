<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Used to return success response
     * @param array|null $items
     * @return JsonResponse
     */

    public function ok(?array $items = null): JsonResponse
    {
        return response()->json($items)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Used to return success response
     * @param array|null $items
     * @param int $status
     * @return JsonResponse
     */

    public function success(?array $items = null, int $status = 200): JsonResponse
    {
        $data = ['status' => 'success'];

        if ($items instanceof Arrayable) {
            $items = $items->toArray();
        }

        if ($items) {
            foreach ($items as $key => $item) {
                $data[$key] = $item;
            }
        }

        return response()->json($data, $status)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Used to return error response
     * @param array|null $items
     * @param int $status
     * @return JsonResponse
     */

    public function error(?array $items = null, int $status = 422): JsonResponse
    {
        $data = array();

        if ($items) {
            foreach ($items as $key => $item) {
                $data['errors'][$key][] = $item;
            }
        }

        return response()->json($data, $status)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
