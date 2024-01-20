<?php 
namespace Xirosoft\Formit\Utils;

 class VisitorInfo
{
    public function __construct(){

    }
    
    /**
     * Get for User Agent function
     * @getUserAgentInfo
     * @return string
     */
    function getUserAgentInfo() {
        // Get the user agent string
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
        // Initialize an associative array to store user agent information
        $user_agent_info = array(
            'user_agent' => $user_agent,
            'is_mobile' => (strpos($user_agent, 'Mobile') !== false),
            'browser' => 'Unknown',
            'os' => 'Unknown'
        );
    
        // Regular expressions to match common browsers and operating systems
        $browser_patterns = array(
            'Chrome', 'Firefox', 'Edge', 'Safari', 'Opera', 'MSIE', 'Trident'
        );
    
        $os_patterns = array(
            'Windows', 'Mac OS X', 'Linux', 'Android', 'iOS'
        );
    
        // Detect browser and operating system
        foreach ($browser_patterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $user_agent, $matches)) {
                $user_agent_info['browser'] = $matches[0];
                break;
            }
        }
    
        foreach ($os_patterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $user_agent, $matches)) {
                $user_agent_info['os'] = $matches[0];
                break;
            }
        }
    
        // Convert the associative array to JSON and return it
        return wp_json_encode($user_agent_info);
    }

    /**
     * Get Visitor IP function
     *
     * @return void
     */
    public function getPublicIp() {
        $api_url = 'https://api.ipify.org?format=json';

        $response = wp_remote_get($api_url);

        if (!is_wp_error($response) && $response['response']['code'] === 200) {
            $body = wp_remote_retrieve_body($response);
            $publicIpData = json_decode($body);

            if ($publicIpData && isset($publicIpData->ip)) {
                return $publicIpData->ip;
            }
        }

        return _e("Unable to determine your public IP address.");
    }


    /**
     * Get Refer page function
     *
     * @return void
     */
    function getReferringPage() {
        $referrer = isset($_SERVER['HTTP_REFERER']) ? esc_url($_SERVER['HTTP_REFERER']) : '';

        if (!empty($referrer)) {
            return $referrer;
        } else {
            return __('No referrer information available.', 'formit');
        }
    }



}
