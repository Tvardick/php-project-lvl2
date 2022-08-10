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
                    [$updateRemoved, $updateAdded] =
                        explode("\n\n\n", $currentIndent);
                    return "{$updateRemoved}{$data['key']}: " .
                        stringify(
                            $data['valueFile1'],
                            $replacer,
                            $spaceCount,
                            $depth + 1
                        ) .
                        "\n{$updateAdded}{$data['key']}: " .
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
                    throw new \Exception("status isn't foreseen -> {$data['status']}");
            }
        },
        $ast
    );
    $result = ["{", ...$line, "{$bracketIndent}}"];

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
