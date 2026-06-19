<?php

namespace App\Support;

use InvalidArgumentException;

class Code39Barcode
{
    /**
     * @var array<string, string>
     */
    private const PATTERNS = [
        '0' => 'nnnwwnwnn',
        '1' => 'wnnwnnnnw',
        '2' => 'nnwwnnnnw',
        '3' => 'wnwwnnnnn',
        '4' => 'nnnwwnnnw',
        '5' => 'wnnwwnnnn',
        '6' => 'nnwwwnnnn',
        '7' => 'nnnwnnwnw',
        '8' => 'wnnwnnwnn',
        '9' => 'nnwwnnwnn',
        'A' => 'wnnnnwnnw',
        'B' => 'nnwnnwnnw',
        'C' => 'wnwnnwnnn',
        'D' => 'nnnnwwnnw',
        'E' => 'wnnnwwnnn',
        'F' => 'nnwnwwnnn',
        'G' => 'nnnnnwwnw',
        'H' => 'wnnnnwwnn',
        'I' => 'nnwnnwwnn',
        'J' => 'nnnnwwwnn',
        'K' => 'wnnnnnnww',
        'L' => 'nnwnnnnww',
        'M' => 'wnwnnnnwn',
        'N' => 'nnnnwnnww',
        'O' => 'wnnnwnnwn',
        'P' => 'nnwnwnnwn',
        'Q' => 'nnnnnnwww',
        'R' => 'wnnnnnwwn',
        'S' => 'nnwnnnwwn',
        'T' => 'nnnnwnwwn',
        'U' => 'wwnnnnnnw',
        'V' => 'nwwnnnnnw',
        'W' => 'wwwnnnnnn',
        'X' => 'nwnnwnnnw',
        'Y' => 'wwnnwnnnn',
        'Z' => 'nwwnwnnnn',
        '-' => 'nwnnnnwnw',
        '.' => 'wwnnnnwnn',
        ' ' => 'nwwnnnwnn',
        '$' => 'nwnwnwnnn',
        '/' => 'nwnwnnnwn',
        '+' => 'nwnnnwnwn',
        '%' => 'nnnwnwnwn',
        '*' => 'nwnnwnwnn',
    ];

    public static function normalize(string $value): string
    {
        $normalized = strtoupper(trim($value));

        if ($normalized === '') {
            throw new InvalidArgumentException('Barcode value cannot be empty.');
        }

        foreach (str_split($normalized) as $character) {
            if ($character === '*') {
                throw new InvalidArgumentException('Barcode value cannot contain the start/stop character.');
            }

            if (! array_key_exists($character, self::PATTERNS)) {
                throw new InvalidArgumentException("Unsupported Code39 character: {$character}");
            }
        }

        return $normalized;
    }

    public static function svg(string $value, int $narrowWidth = 2, int $wideWidth = 5, int $height = 56): string
    {
        $normalized = self::normalize($value);
        $encoded = '*'.$normalized.'*';
        $gap = $narrowWidth;
        $x = 0;
        $rectangles = [];

        foreach (str_split($encoded) as $character) {
            $pattern = self::PATTERNS[$character];

            foreach (str_split($pattern) as $index => $widthCode) {
                $width = $widthCode === 'w' ? $wideWidth : $narrowWidth;

                if ($index % 2 === 0) {
                    $rectangles[] = '<rect x="'.$x.'" y="0" width="'.$width.'" height="'.$height.'" fill="#111827" />';
                }

                $x += $width;
            }

            $x += $gap;
        }

        $width = max($x - $gap, $narrowWidth);

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$width.' '.$height.'" role="img" aria-label="Barcode '.$normalized.'" preserveAspectRatio="none">'.implode('', $rectangles).'</svg>';
    }
}
