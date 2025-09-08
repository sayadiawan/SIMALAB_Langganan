<?php

namespace Smt\Masterweb\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BpjsPcareClient;
use Illuminate\Http\JsonResponse;
use GuzzleHttp\Exception\RequestException;
use Smt\Masterweb\Http\Controllers\Requests\Pcare\AddPendaftaranRequest;

class PendaftaranController extends Controller
{
    public function store(AddPendaftaranRequest $request, BpjsPcareClient $pcare): JsonResponse
    {
        $payload = $request->validated();

        try {
            $result = $pcare->addPendaftaran($payload);
            return response()->json([
                'ok'   => true,
                'data' => $result,
            ]);
        } catch (RequestException $e) {
            $status = $e->getCode() ?: 500;
            $message = $e->getMessage();
            $body = null;
            
            // Try to get response body if available
            if ($e->hasResponse()) {
                try {
                    $body = json_decode($e->getResponse()->getBody()->getContents(), true);
                } catch (\Exception $jsonException) {
                    $body = $e->getResponse()->getBody()->getContents();
                }
            }
            
            return response()->json([
                'ok'      => false,
                'status'  => $status,
                'message' => $message,
                'body'    => $body,
            ], $status);
        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'status'  => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}