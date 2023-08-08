<?php

namespace App\Helpers;

class StringHelper
{
    public static function removeSpecialCharacters(string $input): string
    {
        $normalizedString = preg_replace('/[^a-zA-Z0-9\s]/', '', $input);
        return $normalizedString;
    }
}

?>
