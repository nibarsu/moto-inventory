<?php

namespace App\Support;

class SpreadsheetXmlExporter
{
    /**
     * @param array<int, string> $headers
     * @param array<int, array<int, scalar|null>> $rows
     */
    public static function make(string $sheetName, array $headers, array $rows): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0"?>';
        $xml[] = '<?mso-application progid="Excel.Sheet"?>';
        $xml[] = '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
        $xml[] = '<Worksheet ss:Name="'.self::escape($sheetName).'">';
        $xml[] = '<Table>';
        $xml[] = self::row($headers, true);

        foreach ($rows as $row) {
            $xml[] = self::row($row, false);
        }

        $xml[] = '</Table>';
        $xml[] = '</Worksheet>';
        $xml[] = '</Workbook>';

        return implode('', $xml);
    }

    /**
     * @param array<int, scalar|null> $cells
     */
    private static function row(array $cells, bool $header): string
    {
        $xml = '<Row>';

        foreach ($cells as $value) {
            $text = (string) ($value ?? '');
            $type = is_numeric($value) && ! $header ? 'Number' : 'String';
            $xml .= '<Cell><Data ss:Type="'.$type.'">'.self::escape($text).'</Data></Cell>';
        }

        $xml .= '</Row>';

        return $xml;
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
