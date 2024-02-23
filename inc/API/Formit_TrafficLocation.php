<?php

namespace Xirosoft\Formit\API;

if ( ! class_exists( 'Formit_TrafficLocation' ) ) {

    class Formit_TrafficLocation {

        private $ip;

        public function __construct() {
            // Retrieve the client's IP address from httpbin.org
            $this->ip = $this->formit_getUserIP();
        }

        private function formit_getUserIP() {
            // Query httpbin.org to get client's IP
            $api_url = 'https://httpbin.org/ip';
            $response = wp_remote_get($api_url);

            if (!is_wp_error($response) && $response['response']['code'] === 200) {
                $body = wp_remote_retrieve_body($response);
                $location_data = json_decode($body, true);

                if (isset($location_data['origin'])) {
                    return $location_data['origin'];
                }
            }

            // If unable to retrieve, return a default IP
            return '127.0.0.1';
        }

        public function formit_getLocation() {
            // API endpoint for ip-api.com
            $api_url = "http://ip-api.com/json/{$this->ip}";

            // Send a GET request to the API using wp_remote_get
            $response = wp_remote_get($api_url);

            if (!is_wp_error($response) && $response['response']['code'] === 200) {
                $body = wp_remote_retrieve_body($response);
                $location_data = json_decode($body);

                return $location_data;
            } else {
                return false; // Unable to retrieve location data
            }
        }
    }
}