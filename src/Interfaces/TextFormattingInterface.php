<?php

namespace App\Interfaces;

interface TextFormattingInterface
{
    public function trim(string $text, string|null $trimCharacters): string;
    public function propercase(string $text): string;
}
