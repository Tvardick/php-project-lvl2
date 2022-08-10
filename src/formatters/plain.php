<?php

namespace Differ\Formatter;

use function Differ\Parser\toString;

function plain(array $ast): string
{
    $iter = function ($currentValue, $acc) use (&$iter) {
        $lines = array_map(function ($data) use ($iter, $acc) {
            switch ($data['status']) {
                case "unchanged":
                    return;
                case 'removed':
                    return "Property '{$acc}{$data['key']}' was removed";
                case 'added':
                    return "Property '{$acc}{$data['key']}' was added with value: " .
                        checkPlainProperty($data['valueFile2']);
                case 'updated':
                    return "Property '{$acc}{$data['key']}' was updated. From " .
                        checkPlainProperty($data['valueFile1']) .
                        " to " .
                        checkPlainProperty($data['valueFile2']);
                case 'parent':
                    return $iter($data['children'], "{$acc}{$data["key"]}.");
                default:
                    throw new \Exception("status didn't expect: {$data['status']}");
            }
        }, $currentValue);
        $filteredEmptyLine = array_filter($lines, fn($line) => isset($line));
        return implode("\n", $filteredEmptyLine);
    };
    return $iter($ast, "");
}

function checkPlainProperty(mixed $data): string
{
    if (!is_array($data)) {
        return is_string($data) ? "'{$data}'" : toString($data);
    }
    return "[complex value]";
}
