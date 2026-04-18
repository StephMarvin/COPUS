<?php
    function validate_date($date, $format = "Y-m-d") {
        $dateTime = dateTime::createFromFormat($format, $date);
        return $dateTime && $dateTime->format($format) === $date;
    }
?>