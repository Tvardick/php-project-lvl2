<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Parser\chooseParser;
use function Differ\Formatter\Stylish\stylish;
use function Functional\sort;

function genDiff(string $filePathFile1, string $filePathFile2, string $format = 'stylish'): string
{
    $parseFile1 = chooseParser($filePathFile1);
    $parseFile2 = chooseParser($filePathFile2);

    $ast = ast($parseFile1, $parseFile2);
    $formate = stylish($ast);
    return $formate;
}

function ast(array $parseFile1, array $parseFile2): array
{
    $keys = array_keys(array_merge($parseFile1, $parseFile2));
    $sortKeys = sort($keys, fn ($key1, $key2) => $key1 <=> $key2);
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
    if ($value1 === $value2) {
        return 'unchanged';
    }
    if ($value1 === 'not exists' && $value2 !== 'not exists') {
        return 'added';
    }
    if ($value1 !== 'not exists' && $value2 === 'not exists') {
        return 'removed';
    }
    if ($value1 !== 'not exists' && $value2 !== "not exists") {
        return 'updated';
    }
}
