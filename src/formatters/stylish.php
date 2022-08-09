<?php

namespace Differ\Formatter;

use function Differ\Parser\stringify;

function stylish(array $ast): string
{
    return iter($ast, " ", 4);
}

function iter(array $ast, string $replacer, int $spaceCount, int $depth = 1): string
{
        $ident = $spaceCount * $depth;
        $bracketIdent = str_repeat($replacer, ($ident - $spaceCount));
    $line = array_reduce($ast, function ($acc, $data) use ($ident, $replacer, $spaceCount, $depth) {

        $currentIdent = genCurrentIdent($ident, $replacer, $data['status']);

        switch ($data['status']) {
            case 'unchanged':
                $acc[] = "{$currentIdent}{$data["key"]}: " .
                    stringify(
                        $data['valueFile1'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                break;
            case "removed":
                $acc[] = "{$currentIdent}{$data['key']}: " .
                    stringify(
                        $data['valueFile1'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                break;
            case 'added':
                $acc[] = "{$currentIdent}{$data['key']}: " .
                    stringify(
                        $data['valueFile2'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                break;
            case "updated":
                $acc[] = "{$currentIdent[0]}{$data['key']}: " .
                    stringify(
                        $data['valueFile1'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                $acc[] = "{$currentIdent[1]}{$data['key']}: " .
                    stringify(
                        $data['valueFile2'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                break;
            case "parent":
                $acc[] = "{$currentIdent}{$data['key']}: " .
                    iter(
                        $data['children'],
                        $replacer,
                        $spaceCount,
                        $depth + 1
                    );
                break;
        }
        return $acc;
    }, []);
    $result = ["{", ...$line, "{$bracketIdent}}"];

    return implode("\n", $result);
}

function genCurrentIdent(int $ident, string $replacer, string $status): string|array
{
    $basicIdent = str_repeat($replacer, $ident);
    $len = strlen($basicIdent) - 3;
    switch ($status) {
        case 'unchanged':
            return substr_replace($basicIdent, "  ", $len, 2);
        case "removed":
            return substr_replace($basicIdent, " -", $len, 2);
        case 'added':
            return substr_replace($basicIdent, " +", $len, 2);
        case 'updated':
            return [
                substr_replace($basicIdent, " -", $len, 2),
                substr_replace($basicIdent, " +", $len, 2)
            ];
        case 'parent':
            return $basicIdent;
    }
}
