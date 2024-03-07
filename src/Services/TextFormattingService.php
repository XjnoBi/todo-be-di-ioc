<?php

namespace App\Services;

use App\Interfaces\TextFormattingInterface;

class TextFormattingService implements TextFormattingInterface
{
    public function trim(string $text, ?string $trimCharacters = null): string
    {
        if ($trimCharacters) {
            return trim($text, $trimCharacters);
        }

        return trim($text);
    }

    public function propercase(string $text): string
    {
        return ucfirst($text);
    }
}
