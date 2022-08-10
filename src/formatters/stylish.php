<?php

namespace Differ\Formatter;

use function Differ\Parser\stringify;

function stylish(array $ast): string
{
    return iter($ast, " ", 4);
}

function iter(array $ast, string $replacer, int $spaceCount, int $depth = 1): string
{
    $indent = $spaceCount * $depth;
    $bracketIndent = str_repeat($replacer, ($indent - $spaceCount));
    $line = array_map(
        function ($data) use (
            $indent,
            $replacer,
            $spaceCount,
            $depth
        ) {

            $currentIndent = genCurrentIndent($indent, $replacer, $data['status']);

            switch ($data['status']) {
                case 'unchanged':
                    return "{$currentIndent}{$data["key"]}: " .
                        stringify(
                            $data['valueFile1'],
                            $replacer,
                            $spaceCount,
                            $depth + 1
                        );
                case "removed":
                    return "{$currentIndent}{$data['key']}: " .
                        stringify(
                            $data['valueFile1'],
                            $replacer,
                            $spaceCount,
                            $depth + 1
                        );
                case 'added':
                    return "{$currentIndent}{$data['key']}: " .
                        stringify(
                            $data['valueFile2'],
                            $replacer,
                            $spaceCount,
                            $depth + 1
                        );
                case "updated":
                    return "{$currentIndent[0]}{$data['key']}: " .
                        stringify(
                            $data['valueFile1'],
                            $replacer,
                            $spaceCount,
                            $depth + 1
                        ) .
                        "\n{$currentIndent[1]}{$data['key']}: " .
                        stringify(
                            $data['valueFile2'],
                            $replacer,
                            $spaceCount,
                            $depth + 1
                        );
                case "parent":
                    return "{$currentIndent}{$data['key']}: " .
                        iter(
                            $data['children'],
                            $replacer,
                            $spaceCount,
                            $depth + 1
                        );
                default:
                    throw new \Exception("status isn't foreseen -> {$status}");
            }
        },
        $ast
    );
    $result = ["{", ...$line, "{$bracketIndent}}"];

    return implode("\n", $result);
}

function genCurrentIndent(int $indent, string $replacer, string $status): string|array
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
            return [
                substr_replace($basicIndent, " -", $startIndent, 2),
                substr_replace($basicIndent, " +", $startIndent, 2)
            ];
        case 'parent':
            return $basicIndent;
        default:
            throw new \Exception("status isn't foreseen -> {$status}");
    }
}
