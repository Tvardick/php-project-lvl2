<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function toString($data)
{
    if (is_bool($data)) {
        return $data === true ? 'true' : 'false';
    }
    if (is_null($data)) {
        return 'null';
    }
    return $data;
}

function chooseParser($filepath)
{
    return getExtensionFile($filepath) === "json"
        ? parseJson($filepath)
        : parseYaml($filepath);
}

function parseYaml(string $filePathFile1)
{
    $toObject = Yaml::parse(getData($filePathFile1), Yaml::PARSE_OBJECT_FOR_MAP);
    return json_decode(json_encode($toObject), true);
}

function parseJson(string $filePathFile1): array
{
    return json_decode(getData($filePathFile1), true);
}

function getData(string $path): string
{
    if (!empty($path)) {
        return file_get_contents($path);
    }
    return throw new \Exception("empty path: {$path}");
}

function getExtensionFile($filepath)
{
    return pathinfo($filepath, PATHINFO_EXTENSION);
}
