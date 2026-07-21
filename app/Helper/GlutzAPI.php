<?php

namespace App\Helper;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Support\Facades\Http;

class GlutzAPI
{
    public static function getCredentials(): mixed
    {
        return base64_encode(
            config('services.glutz.username') . ':' . config('services.glutz.password')
        );
    }

    public static function ApiRequest(string $method, array $params = []): mixed
    {
        $method = 'eAccess.' . $method;
        $credentials = self::getCredentials();
        $response = Http::withHeader('Authorization', 'Basic ' . $credentials)
            ->withHeader('Accept', 'application/json')
            ->withBody(json_encode([
                'jsonrpc' => '2.0',
                'method'  => $method,
                'params'  => $params,       
                'id'      => 1,
            ]), 'application/json')
            ->post(config('services.glutz.url'));
        return $response->json();
    }

    public static function getUser(Event $event, string $userName)
    {
        if(!isset($event['glutz_user_id'])){
            $response_user = Self::ApiRequest('createUser', [['label' => $userName]]);
                ->withHeader('Accept', 'application/json')
                ->withBody(json_encode([
                    'jsonrpc' => '2.0',
                    'method'  => 'eAccess.createUser',
                    'params'  => [['label' => $userName]],
                    'id'      => 1,
                ]), 'application/json')
                ->post(config('services.glutz.url'));
            $responseData_user = $response_user->json();
            $userId = $responseData_user['result'] ?? null;
            if (isset($userId)) {
                $event->update(['glutz_user_id' => $userId]);
            }
        }
        else{
            $userId = $event['glutz_user_id'];
        }
        return $userId;
    }

    public static function setCode(int $userId,string $code, mixed $credentials): bool
    {
        $response_code = Http::withHeader('Authorization', 'Basic ' . self::getCredentials())
            ->withHeader('Accept', 'application/json')
            ->withBody(json_encode([
                'jsonrpc' => '2.0',
                'method'  => 'eAccess.getModel',
                'params'  => [
                    'Codes',
                    ['code' => $code]
                ],
                'id'      => 1,
            ]), 'application/json')
            ->post(config('services.glutz.url'));
        $responseData_code = $response_code->json();
        $result_code = $responseData_code['result'] ?? null;
        if (($response_code->failed() || $result_code === null || !is_array($result_code) || empty($result_code))) {
            $response_code = Http::withHeader('Authorization', 'Basic ' . self::getCredentials())
                ->withHeader('Accept', 'application/json')
                ->withBody(json_encode([
                    'jsonrpc' => '2.0',
                    'method'  => 'eAccess.setModel',
                    'params'  => [
                        'Codes',
                        [['code' => $code,
                        'userId' => $userId]],
                    ],
                    'id'      => 1,
                ]), 'application/json')
                ->post(config('services.glutz.url'));
            $responseData_code = $response_code->json();
            $result_code = $responseData_code['result'] ?? null;
        }
        else{
            if(!($result_code[0]['userId'] == $userId)){
            // Türcode bereits vergeben, Fehler zurückgeben
            return false;
            }
        }
        return true;
    }

    public static function getAccessPointId(mixed $credentials): int
    {
        $response_AccessPoint = Http::withHeader('Authorization', 'Basic ' . self::getCredentials())
            ->withHeader('Accept', 'application/json')
            ->withBody(json_encode([
                'jsonrpc' => '2.0',
                'method'  => 'eAccess.getAccessPoint',
                'params'  => [['Access Point' => 'Hauseingang']],
                'id'      => 1,
            ]), 'application/json')
            ->post(config('services.glutz.url'));
        $responseData_AccessPoint = $response_AccessPoint->json();
        $accessPointId = $responseData_AccessPoint['result'] ?? null; 
        return $accessPointId;
    }

    public static function getAccessPointOfUser(int $userId, int $accessPointId, mixed $credentials): bool
    {
        // getAccessPointsOfUser
        $response_AccessPoint = Http::withHeader('Authorization', 'Basic ' . self::getCredentials())
            ->withHeader('Accept', 'application/json')
            ->withBody(json_encode([
                'jsonrpc' => '2.0',
                'method'  => 'eAccess.getAccessPointsOfUser',
                'params'  => [$userId],
                'id'      => 1,
            ]), 'application/json')
            ->post(config('services.glutz.url'));
        $responseData_AccessPoint = $response_AccessPoint->json();
        $accessPointId = $responseData_AccessPoint['result'] ?? null; 
        if (($accessPointId === null || !is_array($accessPointId) || empty($accessPointId))) {
        // setAccessPointsOfUser
            $response_AccessPoint = Http::withHeader('Authorization', 'Basic ' . self::getCredentials())
                ->withHeader('Accept', 'application/json')
                ->withBody(json_encode([
                    'jsonrpc' => '2.0',
                    'method'  => 'eAccess.addAccessPointsToUser',
                    'params'  => [[$accessPointId],
                                    $userId,],
                    'id'      => 1,
                ]), 'application/json')
                ->post(config('services.glutz.url'));
        }
        return true;
    }

    public static function setAccessPointsOfUser(Event $event, int $userId, int $accessPointId, mixed $credentials): array
    { 
        $checkIn  = Carbon::parse($event['start_date'])->startOfDay();   // 00:00:00.000
        $checkOut = Carbon::parse($event['end_date'])->endOfDay();     // 23:59:59.999
        $body = json_encode([
                    'jsonrpc' => '2.0',
                    'method'  => 'eAccess.setAccessRightProperties',
                    'params'  => [
                        [$accessPointId],
                        $userId,
                        [
                            'validFrom' =>  $checkIn->format('Y-m-d\TH:i:s.v'),
                            'validTo'   =>  $checkOut->format('Y-m-d\TH:i:s.v'),
                        ]
                    ],
                    'id'      => 1,
                ]);
        $response_AccessRight = Http::withHeader('Authorization', 'Basic ' . $credentials)
                ->withHeader('Accept', 'application/json')
                ->withBody($body, 'application/json')
                ->post(config('services.glutz.url'));
        return $response_AccessRight->json();
    }

    public static function updateDevice(mixed $credentials): array
    {
        $result = [];
        // getDeviceEvaluationAndUpdateState
        $response_Device = Http::withHeader('Authorization', 'Basic ' . $credentials)
            ->withHeader('Accept', 'application/json')
            ->withBody(json_encode([
                'jsonrpc' => '2.0',
                'method'  => 'eAccess.getDeviceEvaluationAndUpdateState',
                'params'  => [],
                'id'      => 1,
            ]), 'application/json')
            ->post(config('services.glutz.url'));
        $responseData_Device = $response_Device->json();
        $response_Result = $responseData_Device['result'] ?? null; 
        if (!($response_Result === null || !is_array($response_Result) || empty($response_Result))) {
            $updateNeededId = $response_Result['updateNeeded'] ?? null;
            if (isset($updateNeededId)) {
                $responseUpdate = Http::withHeader('Authorization', 'Basic ' . $credentials)
                    ->withHeader('Accept', 'application/json')
                    ->withBody(json_encode([
                        'jsonrpc' => '2.0',
                        'method'  => 'eAccess.deviceAction',
                        'params'  => [
                            'DeviceUpdate',
                            ['deviceid' => $updateNeededId]
                        ],
                        'id'      => 1,
                    ]), 'application/json')
                    ->post(config('services.glutz.url'));
                $responseDataUpdate = $responseUpdate->json();
                $resultUpdate = $responseDataUpdate['result'] ?? null;
                $result = $resultUpdate;
            }
        }
        return $result;

    }
}