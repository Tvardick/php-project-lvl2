<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Parser\chooseParser;
use function Differ\Formatter\{
    stylish,
    plain,
    chooseFormaters
    };
use function Functional\sort as funcSort;

function genDiff(string $filePathFile1, string $filePathFile2, string $format = "stylish"): string
{
    $parseFile1 = chooseParser($filePathFile1);
    $parseFile2 = chooseParser($filePathFile2);

    $ast = ast($parseFile1, $parseFile2);

    return chooseFormaters($ast, $format);
}

function ast(array $parseFile1, array $parseFile2): array
{
    $keys = array_keys(array_merge($parseFile1, $parseFile2));
    $sortKeys = funcSort($keys, fn ($key1, $key2) => $key1 <=> $key2);
    return array_map(function ($key) use ($parseFile1, $parseFile2) {
        $valueFile1 = array_key_exists($key, $parseFile1) ? $parseFile1[$key] : "not exists";
        $valueFile2 = array_key_exists($key, $parseFile2) ? $parseFile2[$key] : "not exists";
        if (is_array($valueFile1) && is_array($valueFile2)) {
            return [
                'key' => $key,
                'status' => "parent",
                "children" => ast($valueFile1, $valueFile2)
            ];
        }
        return [
            'key' => $key,
            'valueFile1' => $valueFile1,
            'valueFile2' => $valueFile2,
            'status' => getStatusFile($valueFile1, $valueFile2)
        ];
    }, $sortKeys);
}

function getStatusFile(mixed $value1, mixed $value2): string
{
    switch (true) {
        case $value1 === $value2:
            return "unchanged";
        case $value1 === 'not exists' && $value2 !== 'not exists':
            return "added";
        case $value1 !== 'not exists' && $value2 === 'not exists':
            return 'removed';
        case $value1 !== 'not exists' && $value2 !== "not exists":
            return 'updated';
        default:
            throw new \Exception(
                "the case is not foreseen\nvalue1->\n{$value1}\nvalue2->\n{$value2}"
            );
    }
}
