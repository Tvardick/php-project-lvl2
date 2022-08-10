<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

use function Differ\Differ\ast;

function toString(mixed $data): string|array
{
    if (is_bool($data)) {
        return $data === true ? 'true' : 'false';
    }
    if (is_null($data)) {
        return 'null';
    }
    return $data;
}

function chooseParser(string $filepath): array
{
    return getExtensionFile($filepath) === "json"
        ? parseJson($filepath)
        : parseYaml($filepath);
}

function parseYaml(string $filePathFile1): array
{
    $toObject = Yaml::parse(getData($filePathFile1), Yaml::PARSE_OBJECT_FOR_MAP);
    $toJson = json_encode($toObject);
    return $toJson !== false ?
        json_decode($toJson, true) :
        throw new \Exception("The data is't String -> \n" . gettype($toJson));
}

function parseJson(string $filePathFile1): array
{
    return json_decode(getData($filePathFile1), true);
}

function getData(string $path): string
{
    if ($path !== "") {
        return file_get_contents($path);
    }
    return throw new \Exception("empty path: {$path}");
}

function getExtensionFile(string $filepath): string
{
    return pathinfo($filepath, PATHINFO_EXTENSION);
}
