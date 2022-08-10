<?php

namespace Differ\Formatter;

use function Differ\Parser\toString;

function stylish(array $ast): string
{
    return iter($ast, " ", 4);
}

function iter(array $ast, string $replacer, int $spaceCount, int $depth = 1): string
{
    $indent = $spaceCount * $depth;
    $bracketIndent = str_repeat($replacer, ($indent - $spaceCount));
    $stringify = function ($data, $replacer, $spaceCount, $depth) use (&$stringify) {
        if (!is_array($data)) {
            return toString($data);
        }

        $indentSize = ($depth * $spaceCount);
        $currentIdent = str_repeat($replacer, $indentSize);
        $bracketIdent = str_repeat($replacer, ($indentSize - $spaceCount));

        $lines = array_map(
            function (
                $node,
                $key
            ) use (
                $stringify,
                $currentIdent,
                $replacer,
                $spaceCount,
                $depth
            ) {
                $value = is_array($node) ?
                    $stringify($node, $replacer, $spaceCount, $depth + 1) :
                    $node;
                return "{$currentIdent}{$key}: {$value}";
            },
            $data,
            array_keys($data)
        );

        $result = ['{', ...$lines, "{$bracketIdent}}"];

        return implode("\n", $result);
    };

    $lines = array_map(
        function ($data) use (
            $stringify,
            $indent,
            $replacer,
            $spaceCount,
            $depth
        ) {

            $currentIndent = genCurrentIndent($indent, $replacer, $data['status']);

            switch ($data['status']) {
                case 'unchanged':
                case "removed":
                    $value = $stringify(
                        $data['valueFile1'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                    return "{$currentIndent}{$data['key']}: {$value}";
                case 'added':
                    $value = $stringify(
                        $data['valueFile2'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                    return "{$currentIndent}{$data['key']}: {$value}";
                case "updated":
                    [$updateRemoved, $updateAdded] =
                        explode("\n\n\n", $currentIndent);
                    $value1 = $stringify(
                        $data['valueFile1'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                    $value2 = $stringify(
                        $data['valueFile2'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                    return "{$updateRemoved}{$data['key']}: {$value1}\n{$updateAdded}{$data['key']}: {$value2}";
                case "parent":
                    $value3 = iter(
                        $data['children'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                    return "{$currentIndent}{$data['key']}: {$value3}";
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
