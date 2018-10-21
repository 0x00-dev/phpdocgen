<?php

/**
 * Тест генерации документации.
 */
class GenerationTest extends \Codeception\Test\Unit
{
    /**
     * Тест генерации документации.
     */
    public function testGenerate()
    {
        $test_dir = 'tests/_data';
        $json = new \PDG\Component\JsonReader("$test_dir/phpdocgen.json");
        $json->read();
        $console = new \PDG\Component\Console($json);
        $console->run();
        $this->assertDirectoryExists("$test_dir/docs");
        $this->assertFileExists("$test_dir/docs/index.html");
        $this->assertDirectoryExists("$test_dir/docs/TestComponent");
        $this->assertFileExists("$test_dir/docs/TestComponent/GeneratorTestClass.html");
        $this->assertFileEquals("$test_dir/docs/index.html", "$test_dir/test_docs/index.html");
        $this->assertFileEquals("$test_dir/docs/TestComponent/GeneratorTestClass.html", "$test_dir/test_docs/TestComponent/GeneratorTestClass.html");
    }
}
