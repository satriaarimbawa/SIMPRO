<?php

namespace App\Helpers;

class HashId
{
    private static string $salt = 'SIMPRO-SPK-SECRET-KEY-2026';

    /**
     * Encodes a numeric database ID into a URL-safe, non-sequential alphanumeric hash.
     */
    public static function encode(int|string|null $id): string
    {
        if (!$id || !is_numeric($id)) {
            return '';
        }
        
        $num = (int)$id;
        $scrambled = ($num * 15823) ^ 0x5A5A5A5A;
        $hex = dechex($scrambled);
        $crc = substr(md5(self::$salt . $num), 0, 4);
        
        return strtoupper($crc . $hex);
    }

    /**
     * Decodes a URL hash back to the original numeric database ID.
     * Returns null if the hash is invalid or tampered with.
     */
    public static function decode(string|null $hash): ?int
    {
        if (!$hash || strlen($hash) <= 4) {
            return null;
        }

        $crc = substr($hash, 0, 4);
        $hex = substr($hash, 4);

        if (!ctype_xdigit($hex)) {
            return null;
        }

        $scrambled = hexdec($hex);
        $num = ($scrambled ^ 0x5A5A5A5A) / 15823;

        if (!is_numeric($num) || floor($num) != $num) {
            return null;
        }

        $num = (int)$num;
        $expectedCrc = substr(md5(self::$salt . $num), 0, 4);

        if (strtoupper($crc) !== strtoupper($expectedCrc)) {
            return null;
        }

        return $num > 0 ? $num : null;
    }
}
