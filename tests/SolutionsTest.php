<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;
use function Differ\Parser\parseYaml;
use function Differ\Differ\getExtensionFile;

class SolutionsTest extends TestCase
{
    private string $path = __DIR__ . "/fixtures/";

    private function getFilePath($name)
    {
        return $this->path . $name;
    }

    public function testRecursiveJsonFiles()
    {
        $expected = file_get_contents($this->getFilePath("expectedResultFileRecursiveJson.txt"));
        $pathToFile1 = __DIR__ . "/fixtures/fileRecursive.json";
        $pathToFile2 = __DIR__ . "/fixtures/fileRecursive2.json";
        $this->assertEquals(trim($expected), genDiff($pathToFile1, $pathToFile2));
    }

    public function testRecursiveYamlFiles()
    {
        $expected = file_get_contents($this->getFilePath("expectedResultFileRecursiveJson.txt"));
        $pathToFile1 = __DIR__ . "/fixtures/fileRecursive.yml";
        $pathToFile2 = __DIR__ . "/fixtures/fileRecursive2.yaml";
        $this->assertEquals(trim($expected), genDiff($pathToFile1, $pathToFile2));
    }
}
