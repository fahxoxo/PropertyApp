<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // สร้าง helper function แปลงตัวเลขเป็นตัวหนังสือภาษาไทย
        if (!function_exists('thaiNumberToWord')) {
            function thaiNumberToWord($number) {
                // Simple implementation for baht conversion
                $thai_digits = array(
                    0 => 'ศูนย์',
                    1 => 'หนึ่ง',
                    2 => 'สอง',
                    3 => 'สาม',
                    4 => 'สี่',
                    5 => 'ห้า',
                    6 => 'หก',
                    7 => 'เจ็ด',
                    8 => 'แปด',
                    9 => 'เก้า'
                );

                // Split baht and satang
                $parts = explode('.', (string)round($number, 2));
                $baht = (int)$parts[0];
                $satang = isset($parts[1]) ? (int)str_pad($parts[1], 2, '0') : 0;

                $result = '';

                // Baht conversion
                if ($baht == 0) {
                    $result .= 'ศูนย์';
                } else {
                    // Millions
                    if ($baht >= 1000000) {
                        $millions = (int)($baht / 1000000);
                        if ($millions > 0) {
                            if ($millions == 1) {
                                $result .= 'หนึ่งล้าน';
                            } else {
                                $result .= $thai_digits[$millions] . 'ล้าน';
                            }
                            $baht = $baht % 1000000;
                        }
                    }

                    // Hundred thousands
                    if ($baht >= 100000) {
                        $hundred_thousands = (int)($baht / 100000);
                        $result .= $thai_digits[$hundred_thousands] . 'แสน';
                        $baht = $baht % 100000;
                    }

                    // Ten thousands
                    if ($baht >= 10000) {
                        $ten_thousands = (int)($baht / 10000);
                        $result .= $thai_digits[$ten_thousands] . 'หมื่น';
                        $baht = $baht % 10000;
                    }

                    // Thousands
                    if ($baht >= 1000) {
                        $thousands = (int)($baht / 1000);
                        $result .= $thai_digits[$thousands] . 'พัน';
                        $baht = $baht % 1000;
                    }

                    // Hundreds
                    if ($baht >= 100) {
                        $hundreds = (int)($baht / 100);
                        $result .= $thai_digits[$hundreds] . 'ร้อย';
                        $baht = $baht % 100;
                    }

                    // Tens and ones
                    if ($baht >= 10) {
                        $tens = (int)($baht / 10);
                        if ($tens > 1) {
                            $result .= $thai_digits[$tens] . 'สิบ';
                        } else {
                            $result .= 'สิบ';
                        }
                        $ones = $baht % 10;
                        if ($ones > 0) {
                            $result .= $thai_digits[$ones];
                        }
                    } else if ($baht > 0) {
                        $result .= $thai_digits[$baht];
                    }
                }

                $result .= 'บาท';

                // Satang conversion
                if ($satang > 0) {
                    if ($satang >= 10) {
                        $tens = (int)($satang / 10);
                        if ($tens > 1) {
                            $result .= $thai_digits[$tens] . 'สิบ';
                        } else {
                            $result .= 'สิบ';
                        }
                        $ones = $satang % 10;
                        if ($ones > 0) {
                            $result .= $thai_digits[$ones];
                        }
                    } else {
                        $result .= $thai_digits[$satang];
                    }
                    $result .= 'สตางค์';
                }

                return $result;
            }
        }
    }
}
