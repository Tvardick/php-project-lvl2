<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class SolutionsTest extends TestCase
{
    public function testGenDiffJson()
    {
        $pathToFile1 = __DIR__ . "/fixture/file1.json";
        $pathToFile2 = __DIR__ . "/fixture/file2.json";
        $result = "{
 - follow: false
   host: hexlet.io
 - proxy: 123.234.53.22
 - timeout: 50
 + timeout: 20
 + verbose: true
}";
        $this->assertEquals($result, genDiff($pathToFile1, $pathToFile2));
    }
}
