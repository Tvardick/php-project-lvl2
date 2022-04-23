<?php

namespace Differ\Differ;

function genDiff(string $filePathFile1, string $filePathFile2)
{
    $contentFile1 = json_decode(getData($filePathFile1), true);
    $contentFile2 = json_decode(getData($filePathFile2), true);
    $collection = collect(array_merge($contentFile1, $contentFile2))->sortKeys();
    $diffJson = $collection->reduce(function ($carry, $value, $key) use ($contentFile1, $contentFile2) {
        if (array_key_exists($key, $contentFile1) && array_key_exists($key, $contentFile2)) {
            if ($contentFile1[$key] === $contentFile2[$key]) {
                $carry[] = "   {$key}: " . convertBoolToText($value);
            } elseif ($contentFile1[$key] !== $contentFile2[$key]) {
                $carry[] = " - {$key}: " . convertBoolToText($contentFile1[$key]);
                $carry[] = " + {$key}: " . convertBoolToText($contentFile2[$key]);
            }
        } elseif (array_key_exists($key, $contentFile1) && !array_key_exists($key, $contentFile2)) {
                $carry[] = " - {$key}: " . convertBoolToText($value);
        } elseif (!array_key_exists($key, $contentFile1) && array_key_exists($key, $contentFile2)) {
                $carry[] = " + {$key}: " . convertBoolToText($value);
        }
        return $carry;
    });
    return "{\n" . implode("\n", $diffJson) . "\n}";
}

function getData(string $path)
{
    if (!empty($path)) {
        return file_get_contents($path);
    }
    return throw new Exception("empty path file");
}

function convertBoolToText($data)
{
    if (is_bool($data)) {
        return $data === true ? "true" : "false";
    }
    return $data;
}
