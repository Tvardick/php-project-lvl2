<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

use function Functional\flatten;

function chooseParser($filepath)
{
    return getExtensionFile($filepath) === "json"
        ? parseJson($filepath)
        : parseYaml($filepath);
}

function parseYaml(string $filePathFile1)
{
    $contentFile = Yaml::parse(getData($filePathFile1), Yaml::PARSE_OBJECT_FOR_MAP);
    return collect($contentFile)->reduce(function ($carry, $value) {
        $convertToArray = (array) $value;
        $carry[array_keys($convertToArray)[0]] = array_values($convertToArray)[0];
        return $carry;
    });
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
    return throw new Exception("empty path file");
}

function getExtensionFile($filepath)
{
    return pathinfo($filepath, PATHINFO_EXTENSION);
}
