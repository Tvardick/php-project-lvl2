<?php

namespace Differ\Formatter;

use function Differ\Parser\{toString, stringify};

function stylish(array $ast): string
{
    return iter($ast, " ", 4);
}

function iter(array $ast, string $replacer, int $spaceCount, int $depth = 1): string
{
    $indent = $spaceCount * $depth;
    $bracketIndent = str_repeat($replacer, ($indent - $spaceCount));
    $lines = array_map(
        function ($data) use (
            $indent,
            $replacer,
            $spaceCount,
            $depth
        ) {

            $currentIndent = genCurrentIndent($indent, $replacer, $data['status']);

            switch ($data['status']) {
                case 'unchanged':
                case "removed":
                    $removedValue = stringify(
                        $data['valueFile1'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                    return "{$currentIndent}{$data['key']}: {$removedValue}";
                case 'added':
                    $addedValue = stringify(
                        $data['valueFile2'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                    return "{$currentIndent}{$data['key']}: {$addedValue}";
                case "updated":
                    [$removedIndent, $addedIndent] =
                        explode("\n\n\n", $currentIndent);
                    $removedVal = stringify(
                        $data['valueFile1'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                    $addedVal = stringify(
                        $data['valueFile2'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                    return "{$removedIndent}{$data['key']}: {$removedVal}\n{$addedIndent}{$data['key']}: {$addedVal}";
                case "parent":
                    $value = iter(
                        $data['children'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                    return "{$currentIndent}{$data['key']}: {$value}";
                default:
                    throw new \Exception("status isn't foreseen -> {$data['status']}");
            }
        },
        $ast
    );
    $result = ["{", ...$lines, "{$bracketIndent}}"];

    return implode("\n", $result);
}

function genCurrentIndent(int $indent, string $replacer, string $status): string
{
    $basicIndent = str_repeat($replacer, $indent);
    $startIndent = strlen($basicIndent) - 3;
    switch ($status) {
        case 'unchanged':
            return substr_replace($basicIndent, "  ", $startIndent, 2);
        case "removed":
            return substr_replace($basicIndent, " -", $startIndent, 2);
        case 'added':
            return substr_replace($basicIndent, " +", $startIndent, 2);
        case 'updated':
            $updateRemoved = substr_replace($basicIndent, " -", $startIndent, 2);
            $updateAdded = substr_replace($basicIndent, " +", $startIndent, 2);
            return "{$updateRemoved}\n\n\n{$updateAdded}";
        case 'parent':
            return $basicIndent;
        default:
            throw new \Exception("status isn't foreseen -> {$status}");
    }
}
