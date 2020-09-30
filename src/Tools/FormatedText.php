<?php


namespace App\Tools;


class FormatedText
{
    /**
     * Create slug
     *
     * @param string $string
     * @param string $delimiter
     * @return string
     */
    public static function slugify($string, $delimiter = '_')
    {

        $oldLocale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, 'en_US.UTF-8');
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower($clean);
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        $clean = trim($clean, $delimiter);
        setlocale(LC_ALL, $oldLocale);

        return $clean;
    }
}
