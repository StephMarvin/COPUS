<?php
    date_default_timezone_set("Asia/Manila");

    function generate_otp_code() {
        $otp_code = rand(100000, 999999);

        return $otp_code;
    }

    function generate_reset_token($length = 16) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomToken = '';

        for ($i = 0; $i < $length; $i++) {
            $randomToken .= $characters[random_int(0, $charactersLength - 1)];
        }
    
        return $randomToken;
    }

    function generate_expiry_time($minutes) {
         $expiry_time = date("Y-m-d H:i:s", strtotime("+$minutes minutes"));

         return $expiry_time;
    }

    function getBrowser($userAgent) {

        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Google Chrome';
        }

        if (strpos($userAgent, 'Firefox') !== false) {
            return 'Mozilla Firefox';
        }

        if (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
            return 'Apple Safari';
        }

        if (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) {
            return 'Opera';
        }

        if (strpos($userAgent, 'Edge') !== false) {
            return 'Microsoft Edge';
        }

        if (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
            return 'Internet Explorer';
        }

        return 'Unknown Browser';
    }

     function get_device_data() {
        $remote_address = $_SERVER['REMOTE_ADDR'];
        $device = gethostbyaddr($remote_address);

        if(!$device) {
            return "Can't retrieve device data";
        }

        return $device;
    }

    function get_ip_address() {
        $remote_address = $_SERVER['REMOTE_ADDR'];
        $device = gethostbyaddr($remote_address);
        $ip_address = gethostbyname($device);

        if(!$ip_address) {
            return "Can't retrieve IP Address";
        }

        return $ip_address;
    }

    function get_browser_data() {
        $browser_name = getBrowser($_SERVER["HTTP_USER_AGENT"]);

        if(!$browser_name) {
            return "Can't retrieve browser data";
        }

        return $browser_name;
    }
?>