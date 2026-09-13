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

    public static function ApiRequest(string $method, array $params = [], int $id = 1, bool $returnResult = true): mixed
    {
        $response = self::ApiRequestCall($method, $params, $id);
        $responseJson = $response->json();
        if ($returnResult) {
            return $responseJson['result'] ?? null;
        }
        else{
            return $responseJson;
        }
    }

    public static function ApiRequestCall(string $method, array $params = [], int $id = 1): mixed
    {
        $method = 'eAccess.' . $method;
        $credentials = self::getCredentials();
        return Http::withHeader('Authorization', 'Basic ' . $credentials)
            ->withHeader('Accept', 'application/json')
            ->withBody(json_encode([
                'jsonrpc' => '2.0',
                'method'  => $method,
                'params'  => $params,       
                'id'      => $id,
            ]), 'application/json')
            ->post(config('services.glutz.url'));
    }

    public static function getUser(Event $event, string $userName, string $code): mixed
    {
        if(!isset($event['glutz_user_id'])){
            $userId = Self::ApiRequest('createUser', [['label' => $userName]]);
            if (isset($userId)) {
                $event->update(['glutz_user_id' => $userId]);
            }
        }
        else{
            $userId = $event['glutz_user_id'];
        }
        Self::ApiRequest('setUserProperty', ["Code", $code, $userId]);
        Self::ApiRequest('setUserProperty', ["Name", $event['name'], $userId]);
        Self::ApiRequest('setUserProperty', ["Vorname", $event['firstname'], $userId]);
        Self::ApiRequest('setUserProperty', ["Event ID", $event['id'], $userId]);
        return $userId;
    }

    public static function setCode(int $userId,string $code): mixed
    {
        $result_code = Self::ApiRequest('getModel', ['Codes', ['code' => $code]]);
        if (($result_code === null || !is_array($result_code) || empty($result_code))) {
            Self::ApiRequest('setModel', ['Codes', ['code' => $code, 'userId' => $userId]]);
        }
        else{
            if(!($result_code[0]['userId'] == $userId)){
                // Türcode bereits vergeben, Fehler zurückgeben
                return false;
            }
        }
        return true;
    }

    public static function getAccessPointId(): int
    {
        return Self::ApiRequest('getAccessPoint', [['Access Point' => 'Hauseingang']]);
    }

    public static function getAccessPointOfUser(int $userId, int $accessPointId): mixed
    {
        // getAccessPointsOfUser
        $accessPointResult = Self::ApiRequest('getAccessPointsOfUser', [(string) $userId], 2);
        if (($accessPointResult === null || empty($accessPointResult))) {
            // addAccessPointsToUser
           return Self::ApiRequest('addAccessPointsToUser',[[$accessPointId], (string) $userId], 4);
        }
        return true;
    }

    public static function setAccessPointsOfUser(Event $event, int $userId, int $accessPointId): bool
    { 
        $checkIn  = Carbon::parse($event['start_date'])->startOfDay();   // 00:00:00.000
        $checkOut = Carbon::parse($event['end_date'])->endOfDay();     // 23:59:59.999
        Self::ApiRequest('setAccessRightProperties',[[$accessPointId], (string) $userId, ['validFrom' =>  $checkIn->format('Y-m-d\TH:i:s.v'),'validTo'   =>  $checkOut->format('Y-m-d\TH:i:s.v'),]],4);
        return true;
    }

    public static function updateDevice(): mixed
    {
        $result = [];
        // getDeviceEvaluationAndUpdateState
        $response_Result = Self::ApiRequest('getDeviceEvaluationAndUpdateState',[]);
        if (!($response_Result === null)) {
            $updateNeededId = $response_Result['updateNeeded'] ?? null;
            if (is_array($updateNeededId) && !empty($updateNeededId)) {
                $resultUpdate = Self::ApiRequest('deviceAction',['DeviceUpdate', ['deviceid' => $updateNeededId[0]]]);
                $result = $resultUpdate;
            }
        }
        return $result;

    }

        public static function deleteUser(int $userId): mixed
    {
        return Self::ApiRequest('deleteUser',[(string) $userId]);
    }
}