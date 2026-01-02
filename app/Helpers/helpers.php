<?php

use App\Helpers\NumberHelper;

if (!function_exists('thaiNumberToWord')) {
    function thaiNumberToWord($number) {
        return NumberHelper::thaiNumberToWord($number);
    }
}
