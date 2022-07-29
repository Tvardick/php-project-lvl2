<?php

namespace Differ\Formatter\Stylish;

use function Differ\Parser\{chooseParser, toString};

function stylish($differed)
{
    return iter($differed, 1);
}

function iter($differed, $depth)
{
    $replacer = " ";
    $spaceCount = 1;
    $test = [" " => "    ", "-" => "  - ", "+" => "  + "];
    $ident = $spaceCount * $depth;
    $currentIdent = str_repeat($replacer, $ident);
    $bracketIdent = str_repeat($replacer, ($ident - $depth));
    $line = array_reduce($differed, function ($carry, $data) use ($test, $currentIdent, $depth) {
        switch ($data['status']) {
            case 'unchanged':
                print_r("1");
                $carry[] = "{$currentIdent}{$test[' ']}{$data["key"]}: {$data['valueFile1']}";
                break;
            case "removed":
                print_r("2");
                $carry[] = "{$currentIdent}{$test['-']}{$data['key']}: " . toArray($data['valueFile1'], $currentIdent);
                break;
            case 'added':
                print_r("3");
                $carry[] = "{$currentIdent}{$test["+"]}{$data['key']}: " . toArray($data['valueFile2'], $currentIdent);
                print_r($carry);
                break;
            case "updated":
                print_r("4");
                $carry[] = "{$currentIdent}{$test["-"]}{$data['key']}: " . toArray($data['valueFile1'], $currentIdent);
                $carry[] = "{$currentIdent}{$test["+"]}{$data['key']}: " . toArray($data['valueFile2'], $currentIdent);
                break;
            case "parent":
                print_r("5");
                $carry[] = "{$currentIdent}{$test[' ']}{$data['key']}: " . iter($data['children'], $depth + 1);
                break;
        }
        return $carry;
    }, []);
    $result = ["{", ...$line, "{$bracketIdent}}"];

    //print_r($result);
    return implode("\n", $result);
}

function toArray($coll, $currentIdent)
{
    if (is_array($coll)) {
        $key = array_keys($coll)[0];
        $value = array_values($coll)[0];
        $ident = str_repeat($currentIdent, 11);
        print_r("!!!!!!coll\n");
        print_r($coll);

        print_r("!!!!!!!key\n");
        print_r($key);
        print_r("!!!!!!!!value\n");
        print_r($value);
        return "{\n" . implode("\n", ["{$ident}{$key} : {$value}"]) . "\n}";
    }
    return $coll;
}
/*
    return array_reduce($differed, function ($carry, $data) {
        switch ($data['status']) {
            case 'unchanged':
                $carry[] = "   {$data["key"]}: {$data['valueFile1']}";
                break;
            case "removed":
                $carry[] = " - {$data['key']}: " . toString($data['valueFile1']);
                break;
            case 'added':
                $carry[] = " + {$data['key']}: " . toString($data['valueFile2']);
                break;
            case "updated":
                $carry[] = " - {$data['key']}: " . toString($data['valueFile1']);
                $carry[] = " + {$data['key']}: " . toString($data['valueFile2']);
                break;
            case "parent":
                $temp = stylish($data['children']);
                var_dump($temp);
                $carry[] = "   {$data['key']}: {$temp}";
                break;
        }
        return $carry;
    }, []);
 */
