<?php

namespace Differ\Formatter;

use function Differ\Formatter\{stylish, plain, json};

function chooseFormaters(array $ast, string $format): string
{
    switch ($format) {
        case "stylish":
            return stylish($ast);
        case "plain":
            return plain($ast);
        case "json":
            return json($ast);
        default:
            throw new \Exception("format isn't foreseen ->\n{$format}");
    }
}
