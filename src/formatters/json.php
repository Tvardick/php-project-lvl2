<?php

namespace Differ\Formatter;

function json(array $ast)
{
    return json_encode($ast, JSON_PRETTY_PRINT);
}
