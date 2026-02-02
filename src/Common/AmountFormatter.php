<?php

declare(strict_types=1);

namespace Afea\Einvoice\Common;

class AmountFormatter
{
    private const ONES = [
        '', 'BİR', 'İKİ', 'ÜÇ', 'DÖRT', 'BEŞ', 'ALTI', 'YEDİ', 'SEKİZ', 'DOKUZ'
    ];

    private const TENS = [
        '', 'ON', 'YİRMİ', 'OTUZ', 'KIRK', 'ELLİ', 'ALTMIŞ', 'YETMİŞ', 'SEKSEN', 'DOKSAN'
    ];

    private const HUNDREDS = [
        '', 'YÜZ', 'İKİYÜZ', 'ÜÇYÜZ', 'DÖRTYÜZ', 'BEŞYÜZ', 'ALTIYÜZ', 'YEDİYÜZ', 'SEKİZYÜZ', 'DOKUZYÜZ'
    ];

    /**
     * Converts a numeric amount to Turkish text format.
     * Example: 576.45 -> "YALNIZ : BEŞYÜZYETMİŞALTI TL KIRKBEŞ Kr."
     *
     * @param float|string $amount The amount to convert
     * @return string The amount in Turkish text format
     */
    public static function toText($amount): string
    {
        // Convert to string and normalize decimal separator
        $amountStr = str_replace(',', '.', (string) $amount);

        // Parse the amount
        $parts = explode('.', $amountStr);
        $lira = (int) ($parts[0] ?? 0);
        $kurus = isset($parts[1]) ? (int) str_pad(substr($parts[1], 0, 2), 2, '0') : 0;

        // Convert lira part
        $liraText = self::convertNumber($lira);

        // Convert kurus part
        $kurusText = self::convertNumber($kurus);

        // Build final text
        $result = 'YALNIZ : ';

        if ($lira === 0 && $kurus === 0) {
            return $result . 'SIFIR TL SIFIR Kr.';
        }

        if ($lira > 0) {
            $result .= $liraText . ' TL';
        } else {
            $result .= 'SIFIR TL';
        }

        if ($kurus > 0) {
            $result .= ' ' . $kurusText . ' Kr.';
        } else {
            $result .= ' SIFIR Kr.';
        }

        return $result;
    }

    /**
     * Converts a number (0-999999) to Turkish text.
     *
     * @param int $number The number to convert
     * @return string The number in Turkish text
     */
    private static function convertNumber(int $number): string
    {
        if ($number === 0) {
            return 'SIFIR';
        }

        if ($number < 0 || $number > 999999) {
            throw new \InvalidArgumentException('Number must be between 0 and 999999');
        }

        $result = [];

        // Thousands (1000-999999)
        $thousands = (int) ($number / 1000);
        if ($thousands > 0) {
            if ($thousands === 1) {
                $result[] = 'BİN';
            } else {
                $result[] = self::convertHundreds($thousands) . 'BİN';
            }
        }

        // Remaining part (0-999)
        $remainder = $number % 1000;
        if ($remainder > 0) {
            $result[] = self::convertHundreds($remainder);
        }

        return implode('', $result);
    }

    /**
     * Converts a number (0-999) to Turkish text.
     *
     * @param int $number The number to convert (0-999)
     * @return string The number in Turkish text
     */
    private static function convertHundreds(int $number): string
    {
        $result = [];

        // Hundreds (100-900)
        $hundreds = (int) ($number / 100);
        if ($hundreds > 0) {
            $result[] = self::HUNDREDS[$hundreds];
        }

        // Tens and ones (0-99)
        $remainder = $number % 100;

        // Tens (10-90)
        $tens = (int) ($remainder / 10);
        if ($tens > 0) {
            $result[] = self::TENS[$tens];
        }

        // Ones (1-9)
        $ones = $remainder % 10;
        if ($ones > 0) {
            $result[] = self::ONES[$ones];
        }

        return implode('', $result);
    }
}
