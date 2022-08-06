<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

use function Differ\Differ\ast;

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

function stringify($data, $replacer, $spaceCount = 1, $depth = 1)
{
    if (!is_array($data)) {
        return toString($data);
    }

    $indentSize = ($depth * $spaceCount);
    $currentIdent = str_repeat($replacer, $indentSize);
    $bracketIdent = str_repeat($replacer, ($indentSize - $spaceCount));

    $lines = array_map(function ($node, $key) use ($indentSize, $currentIdent, $replacer, $spaceCount, $depth) {
        $value = is_array($node) ?
            stringify($node, $replacer, $spaceCount, $depth + 1) :
            $node;
        return "{$currentIdent}{$key}: {$value}";
    }, $data, array_keys($data));

    $result = ['{', ...$lines, "{$bracketIdent}}"];

    return implode("\n", $result);
}
