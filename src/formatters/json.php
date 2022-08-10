<?php

namespace Differ\Formatter;

function json(array $ast)
{
    return json_encode($ast, JSON_PRETTY_PRINT) !== false ?
        json_encode($ast, JSON_PRETTY_PRINT) :
        throw new \Exception("This data can't encoding to json -> \n{$ast}");
}
