<?php
namespace Pressone\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Telephony {

    // private $baseUrl = "https://pressone-test-1a2875e4-023ea85a610a.herokuapp.com/api/"; 
    private $baseUrl = "https://api.pressone.co/api/";

    private $accessToken = null;

    function __construct($key) {
        if (!$key) {
            throw new \Exception("Secret key needed for this object", 1);
        }
        $this->accessToken = $key;
    }

    function getNumbers($page = 1, $count = 100) {
        $data = $this->get("api/third-party/sdk/number/");
        print_r($data);
        $responseData = $data["data"] ?? [];
        $receivers = $responseData["receivers"] ?? [];
        $response = [
            "data"  => []
        ];
        foreach ($receivers as $receiver) {
            $response["data"][] = [
                "phone_number"  => $receiver["business_number"]["phone_number"],
                "label"         => $receiver["business_number"]["label"],
                "receiver_id"   => $receiver["id"],
                "receiver_code" => $receiver["extension_code"],
            ];
        }
        $response["total"] = $data["total"] ?? 0;
        $response["size"] = $data["page_size"] ?? 0;

        return $response;
    }

    function assignNumber(array $data) {

        if (!isset($data["email"]) || !isset($data["phone_number"])) {
            return [
                "message"   => "both email and phone_number are required.",
                "code"      => "404"
            ];
        }

        $payload = [
            "first_name"    => $data["first_name"] ?? $data["phone_number"],
            "last_name"     => $data["last_name"] ?? $data["phone_number"],
            "email"         => $data["email"],
            "mobile"        => $data["phone_number"],
            "role"          => $data["role"] ?? "owner",
            "note"          => null,
            "country"       => $data["country"] ?? "NG",
            "can_make_calls"=> true,
            "permissions"   => [
                "can_export_call_logs"  => null,
                "can_view_all_call_logs"=> null,
                "can_export_contact"    => null,
                "can_export_report"     => null,
                "can_manage_billing"    => null,
                "can_manage_team"       => null,
                "can_manage_permissions"=> null,
                "can_manage_personalization"    => null,
                "can_access_call_recordings"    => null,
                "can_download_call_recordings"  => null,
                "can_view_performance_report"   => null,
                "can_view_activity_report"      => null,
                "business_numbers"  => [
                    1
                ],
                "role"              => $data["role"] ?? "owner",
            ]
        ];

        $data = $this->post("api/third-party/sdk/team-member/", $data);

        $receivers = $data["receivers"] ?? [];

        $response = [];
        foreach ($receivers as $receiver) {
            $response[] = [
                "phone_number"  => $receiver["business_number"]["phone_number"],
                "label"         => $receiver["business_number"]["label"],
                "receiver_id"   => $receiver["id"],
                "receiver_code" => $receiver["extension_code"],
            ];
        }

        return $response;
    }

    function getCallCredentials($receiver_id, $public_key) {
        return $this->post("api/third-party/sdk/receiver-line/", [
            "public_key"    => $public_key,
            "receiver"      => $receiver_id
        ]);
    }

    function getCallRecords($page = 1, $count = 100) {
        $response = $this->get("api/third-party/sdk/contacts/?page_index=$page&page_size=$count");
        return $response;
    }

    function getContacts($page = 1, $count = 100) {
        $response = $this->get("api/third-party/sdk/contacts/?page_index=$page&page_size=$count");
        return $response;
    }

    function get($url) {
        return $this->makeRequest("GET", $url);
    }

    function post($url, $data) {
        return $this->makeRequest("POST", $url, $data);
    }

    function makeRequest($method, $url, $body = null) {
        $headers = ['Authorization' => 'Bearer ' . $this->accessToken];

        $client = new Client([
            'base_uri'  => $this->baseUrl,
            'headers'   => $headers
        ]);

        $req = [
            'verify' => false
        ];

        if ($body != null) {
            $req["json"] = $body;
        }

        try {

            $response = $client->request($method, $url, $req);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody();

            if ( $statusCode == 401 ) {
                return $body;
            }

            if ($statusCode > 300) {
                throw new Exception("Error Processing Request", 1);
            }

            return $body;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return [
                "message"   => $message,
                "code"      => "404"
            ];
        }
    }

}