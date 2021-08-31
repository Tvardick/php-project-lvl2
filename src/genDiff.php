<?php

namespace Differ\Differ;

function genDiff(string $filePathFile1, string $filePathFile2)
{
     $contentFile1 = json_decode(getData($filePathFile1), true);
     $contentFile2 = json_decode(getData($filePathFile2), true);
     $different = array_reduce_assoc($contentFile1, function ($acc, $key, $value) use ($contentFile1, $contentFile2) {
         $differentKey = array_keys(array_diff_key($contentFile2, $contentFile1))[0];
        if (array_key_exists($key, $contentFile2) && $value === $contentFile2[$key]) {
             $acc["  $key"] = $value;
        }
        if (array_key_exists($key, $contentFile2) && $value !== $contentFile2[$key]) {
            $acc["- $key"] = $value;
            $acc["+ $key"] = $contentFile2[$key];
        }
        if (!array_key_exists($key, $contentFile2)) {
            $acc["- $key"] = $value;
        }
        if (!array_key_exists($differentKey, $contentFile1)) {
            $acc["+ $differentKey"] = $contentFile2[$differentKey];
        }

        return $acc;
     }, []);
     uksort($different, function ($firstKey, $secondKey) {
        return substr($firstKey, 2) <=> substr($secondKey, 2);
     });
     return json_encode($different, JSON_PRETTY_PRINT);
}

function array_reduce_assoc(array $coll, callable $callable, $init = null)
{
    $carry = $init;
    foreach ($coll as $key => $item) {
        $carry = $callable($carry, $key, $item);
    }
    return $carry;
}

function getData(string $path)
{
    return file_get_contents($path);
}
//$filePathFile1 = __DIR__ . "/../file1.json";
//$filePathFile2 = __DIR__ . "/../file2.json";
