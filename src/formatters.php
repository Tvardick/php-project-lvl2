<?php

namespace Differ\Formatter;

use function Differ\Formatter\{stylish, plain};

function chooseFormaters(array $ast, string $format): string
{
    switch ($format) {
        case "stylish":
            return stylish($ast);
        case "plain":
            return plain($ast);
    }
}
