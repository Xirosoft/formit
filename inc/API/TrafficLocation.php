<?php 
namespace Xirosoft\Formit\API;
class TrafficLocation {

        public function __construct() {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        public function getLocation() {
            // API endpoint for ip-api.com
            $api_url = "http://ip-api.com/json/{$this->ip}";
            
            // Send a GET request to the API
            $response = file_get_contents($api_url);
            
            if ($response) {
                // Parse the JSON response
                $location_data = json_decode($response);
                return $location_data;
            } else {
                return false; // Unable to retrieve location data
            }
        }
    }
?>