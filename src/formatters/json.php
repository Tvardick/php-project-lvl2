<?php

namespace Differ\Formatter;

function json(array $ast): string
{
    $encodeAst = json_encode($ast, JSON_PRETTY_PRINT);
    return $encodeAst !== false ?
        $encodeAst :
        throw new \Exception("This data can't encoding to json -> \n{$encodeAst}");
}
