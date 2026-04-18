<?php
    date_default_timezone_set("Asia/Manila");

    function format_unix_time($timestamp) {
        if(!$timestamp) {
            return $timestamp;
        }

        $format_unix = date("M d, Y, h:i:s A", strtotime($timestamp));

        return $format_unix;
    }

    function format_timestamp($timestamp) {
        if(!$timestamp) {
            return $timestamp;
        }

        $formatted_timestamp = date("M. d, Y, h:i:s A", strtotime($timestamp));

        return $formatted_timestamp;
    }

    function format_date($date) {
        if(!$date) {
            return $date;
        }

        $formatted_date = date("M. d, Y", strtotime($date));

        return $formatted_date;
    }

    function get_current_timestamp() {
        $current_date = date("M. d, Y");
        $current_time = date("h:i A");

        $current_timestamp = $current_date . ", " . $current_time;
        
        return $current_timestamp;
    }
?>