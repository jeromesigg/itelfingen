<?php

namespace App\Helper;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GlutzAPI
{
    public static function getCredentials(): mixed
    {
        return base64_encode(
            config('services.glutz.username').':'.config('services.glutz.password')
        );
    }

    public static function apiRequest(string $method, array $params = [], int $id = 1, bool $returnResult = true): mixed
    {
        $response = self::apiRequestCall($method, $params, $id);
        $responseJson = $response->json();
        if ($returnResult) {
            return $responseJson['result'] ?? null;
        } else {
            return $responseJson;
        }
    }

    public static function apiRequestCall(string $method, array $params = [], int $id = 1): mixed
    {
        $method = 'eAccess.'.$method;
        $credentials = self::getCredentials();

        return Http::withHeader('Authorization', 'Basic '.$credentials)
            ->withHeader('Accept', 'application/json')
            ->withBody(json_encode([
                'jsonrpc' => '2.0',
                'method' => $method,
                'params' => $params,
                'id' => $id,
            ]), 'application/json')
            ->post(config('services.glutz.url'));
    }

    public static function getUser(Event $event, string $userName, string $code): mixed
    {
        if (! isset($event['glutz_user_id'])) {
            $userId = self::apiRequest('createUser', [['label' => $userName]]);
            if (isset($userId)) {
                $event->update(['glutz_user_id' => $userId]);
            }
        } else {
            $userId = $event['glutz_user_id'];
        }
        self::apiRequest('setUserProperty', ['Code', $code, $userId]);
        self::apiRequest('setUserProperty', ['Name', $event['name'], $userId]);
        self::apiRequest('setUserProperty', ['Vorname', $event['firstname'], $userId]);
        self::apiRequest('setUserProperty', ['Event ID', $event['id'], $userId]);

        return $userId;
    }

    public static function setCode(int $userId, string $code): mixed
    {
        $result_code = self::apiRequest('getModel', ['Codes', ['code' => $code]]);
        if ($result_code === null || ! is_array($result_code) || empty($result_code)) {
            self::apiRequest('setModel', ['Codes', ['code' => $code, 'userId' => $userId]]);
        } elseif ($result_code[0]['userId'] != $userId) {
            // Türcode bereits vergeben, Fehler zurückgeben
            return false;
        }

        return true;
    }

    public static function getAccessPointId(): ?int
    {
        return self::apiRequest('getAccessPoint', [['Access Point' => 'Hauseingang']]);
    }

    public static function getAccessPointOfUser(int $userId, int $accessPointId): mixed
    {
        // getAccessPointsOfUser
        $accessPointResult = self::apiRequest('getAccessPointsOfUser', [(string) $userId], 2);
        if ($accessPointResult === null || empty($accessPointResult)) {
            // addAccessPointsToUser
            return self::apiRequest('addAccessPointsToUser', [[$accessPointId], (string) $userId], 4);
        }

        return true;
    }

    public static function setAccessPointsOfUser(Event $event, int $userId, int $accessPointId): bool
    {
        $checkIn = Carbon::parse($event['start_date'])->startOfDay();   // 00:00:00.000
        $checkOut = Carbon::parse($event['end_date'])->endOfDay();     // 23:59:59.999
        self::apiRequest('setAccessRightProperties', [[$accessPointId], (string) $userId, ['validFrom' => $checkIn->format('Y-m-d\TH:i:s.v'), 'validTo' => $checkOut->format('Y-m-d\TH:i:s.v')]], 4);

        return true;
    }

    public static function updateDevice(): mixed
    {
        $result = [];
        // getDeviceEvaluationAndUpdateState
        $response_Result = self::apiRequest('getDeviceEvaluationAndUpdateState', []);
        if ($response_Result !== null) {
            $updateNeededId = $response_Result['updateNeeded'] ?? null;
            if (is_array($updateNeededId) && ! empty($updateNeededId)) {
                $resultUpdate = self::apiRequest('deviceAction', ['DeviceUpdate', ['deviceid' => $updateNeededId[0]]]);
                $result = $resultUpdate;
            }
        }

        return $result;

    }

    public static function deleteUser(int $userId): mixed
    {
        return self::apiRequest('deleteUser', [(string) $userId]);
    }
}
