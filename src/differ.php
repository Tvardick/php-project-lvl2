<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Parser\parseJson;
use function Differ\Parser\parseYaml;
use function Differ\Parser\chooseParser;

function genDiff(string $filePathFile1, string $filePathFile2): string
{
    $parseFile1 = chooseParser($filePathFile1);
    $parseFile2 = chooseParser($filePathFile2);
    $differ = differ($parseFile1, $parseFile2);
    return "{\n" . implode("\n", $differ) . "\n}";
}

function convertBoolToText($data)
{
    if (is_bool($data)) {
        return $data === true ? "true" : "false";
    }
    return $data;
}

function differ(array $parseFile1, array $parseFile2): array
{
    $collection = collect(array_merge($parseFile1, $parseFile2))->sortKeys();
    $difference = $collection->reduce(function ($carry, $value, $key) use ($parseFile1, $parseFile2) {
        if (array_key_exists($key, $parseFile1) && array_key_exists($key, $parseFile2)) {
            if ($parseFile1[$key] === $parseFile2[$key]) {
                $carry[] = "   {$key}: " . convertBoolToText($value);
            } elseif ($parseFile1[$key] !== $parseFile2[$key]) {
                $carry[] = " - {$key}: " . convertBoolToText($parseFile1[$key]);
                $carry[] = " + {$key}: " . convertBoolToText($parseFile2[$key]);
            }
        } elseif (array_key_exists($key, $parseFile1) && !array_key_exists($key, $parseFile2)) {
                $carry[] = " - {$key}: " . convertBoolToText($value);
        } elseif (!array_key_exists($key, $parseFile1) && array_key_exists($key, $parseFile2)) {
                $carry[] = " + {$key}: " . convertBoolToText($value);
        }
        return $carry;
    });
    return $difference;
}
