<?php
namespace Pressone\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Telephony {

    private $baseUrl = "https://api.pressone.co/";
    private $testBaseUrl = "https://pressone-internal-live.herokuapp.com/";

    private $accessToken = null;
    private $isTest = false;

    function __construct($key, $isTest = false) {
        if (!$key) {
            throw new \Exception("Secret key needed for this object", 1);
        }
        $this->accessToken = $key;
        $this->isTest = $isTest;
    }

    function availableNumbers($page = 1, $count = 100) {
        $available_number = $this->get("api/available_number/numbers/?page_index=$page&page_size=$count");
        $response = [
            "data"  => [],
            "total" => $available_number["total"],
            "size"  => $available_number["page_size"]
        ];

        foreach ($available_number["data"] as $available_number_data) {
            $number = [
                "phone_number"  => $available_number_data["phone_number"],
                "country_code"  => $available_number_data["country_code"]
            ];

            $response["data"][] = $number;
        }

        return $response;
    }

    function getNumbers($page = 1, $count = 100) {
        $data = $this->get("api/third-party/sdk/number/?page_index=$page&page_size=$count");
        
        $response = [];

        foreach ($data as $number) {
            $response[] = [
                "phone_number"  => $number["phone_number"],
                "status"        => $number["verification_status"],
                "label"         => $number["label"] ?? $number["phone_number"],
                "number_id"     => $number["id"],
            ];
        }

        return $response;
    }

    function getMembers($page = 1, $count = 100) {
        $data = $this->get("api/third-party/sdk/team-member/?page_index=$page&page_size=$count");
        // 
        $responseData = $data["data"] ?? [];
        
        $response = [
            "data"  => []
        ];

        foreach ($responseData as $businessData) {
            $receivers = $businessData["receivers"] ?? [];
            foreach ($receivers as $receiver) {
                $response["data"][] = [
                    "phone_number"  => $businessData["mobile"],
                    "full_name"     => $businessData["first_name"] . " " . $businessData["last_name"],
                    "receiver_id"   => $receiver["business_number"],
                    "receiver_code" => $receiver["extension_code"],
                ];
            }
        }
        
        $response["total"] = $data["total"] ?? 0;
        $response["size"] = $data["page_size"] ?? 0;

        return $response;
    }

    function assignNumber(array $data) {

        if (!isset($data["email"]) || !isset($data["phone_number"]) || !isset($data["number_ids"])) {
            return [
                "message"   => "both email and phone_number are required.",
                "code"      => "404"
            ];
        }

        if ( !is_array($data["number_ids"])) {
            return [
                "message"   => "number_ids must be an array of int.",
                "code"      => "401"
            ];
        }

        $payload = [
            "first_name"    => $data["first_name"] ?? $data["phone_number"],
            "last_name"     => $data["last_name"] ?? $data["phone_number"],
            "email"         => $data["email"],
            "mobile"        => $data["phone_number"],
            "role"          => $data["role"] ?? "agent",
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
                "business_numbers"  => $data["number_ids"],
                "role"              => $data["role"] ?? "agent",
            ]
        ];

        $data = $this->post("api/third-party/sdk/team-member/", $data);

        if (isset($data["data"]) && $data["data"] === "400") {
            return $data;
        }

        $receivers = $data["receivers"] ?? [];
        $profile = $data["profile"] ?? [];

        $response = [];
        foreach ($receivers as $receiver) {
            $response[] = [
                "phone_number"  => $profile["mobile"],
                "full_name"     => $profile["first_name"] . " " . $profile["last_name"],
                "receiver_id"   => isset($receiver["business_number"]) ? $receiver["business_number"]["id"] : $receiver["id"],
                "receiver_code" => $receiver["extension_code"],
                "user"          => $profile["user"]
            ];
        }

        if (count($response) === 0) {
            $response[] = [
                "phone_number"  => $profile["mobile"],
                "full_name"     => $profile["first_name"] . " " . $profile["last_name"],
                "receiver_id"   => null,
                "receiver_code" => null,
                "user_id"       => $profile["user"]
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

    function getCredentials($user_id) {
        if (!$user_id) {
            return [
                "message"   => "The user ID is required for this request.",
                "code"      => "400"
            ];
        }

        return $this->post("api/third-party/sdk/team-member/login/", [
            $user_id
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

    function getBaseUrl() {
        return $this->isTest ? $this->testBaseUrl : $this->baseUrl;
    }

    function makeRequest($method, $url, $body = null) {
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Pressone-X-Api-Key' => $this->accessToken,
        ];

        $client = new Client([
            'base_uri'  => $this->getBaseUrl(),
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
            $body = json_decode($response->getBody(),true);
      
            if ( $statusCode == 401 ) {
                return $body;
            }

            if ($statusCode > 300) {
                throw new \Exception("Error Processing Request", 1);
            }

            return $body;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return [
                "message"   => $message,
                "code"      => "400"
            ];
        }
    }

}