<?php

namespace App\Http\Controllers;

use App\Models\KeyValueStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KeyValueStoreController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            if (empty($data)) {
                return response()->json([
                    'message' => 'Request body cannot be empty'
                ], Response::HTTP_BAD_REQUEST);
            }

            $results = DB::transaction(function () use ($data) {
                $results = [];
                $timestamp = time();

                foreach ($data as $key => $value) {
                    $record = KeyValueStore::storeKeyValue($key, $value, $timestamp);

                    $results[] = [
                        'key' => $key,
                        'value' => $value,
                        'stored_at' => $record->getAttribute('stored_at')->toDateTimeString(),
                        'stored_at_utc' => $record->getAttribute('stored_at')->timestamp
                    ];
                }

                return $results;
            });

            return response()->json([
                'message' => 'Data stored successfully',
                'data' => $results
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occured while storing the data',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, string $key): JsonResponse
    {
        try {
            $timestamp = $request->query('timestamp');

            if (is_null($timestamp)) {
                $value = KeyValueStore::getLatestValue($key);
            } else {
                $timestamp = (int) $timestamp;
                $value = KeyValueStore::getValueAtTimestamp($key, $timestamp);
            }

            return response()->json($value);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => 'An error occured while fetching the data',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
