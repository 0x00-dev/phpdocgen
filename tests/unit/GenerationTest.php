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
        $test_dir = getcwd() . '/tests/_data';
        (new \PDG\Component\DocGenerator())
            ->setConfigFile("$test_dir/autoconfig.json")
            ->run();
        (new \PDG\Component\DocGenerator())
            ->setConfigFile("$test_dir/phpdocgen.json")
            ->run();
        $this->assertDirectoryExists("$test_dir/docs", 'Проверяем, что директория документации создалась.');
        $this->assertFileExists("$test_dir/docs/index.html", 'Проверяем, что заглавный файл страницы создан.');
        $this->assertDirectoryExists("$test_dir/docs/TestComponent", 'Проверяем, что директория TestComponent создана.');
        $this->assertFileExists("$test_dir/docs/TestComponent/GeneratorTestClass.html", 'Проверяем, что документация для GeneratorTest создана.');
        $this->assertFileEquals("$test_dir/docs/index.html", "$test_dir//index.html", 'Проверяем, что сгенерированный заглавный файл и эталон одинаковы.');        
        $this->assertFileEquals("$test_dir/docs/TestComponent/GeneratorTestClass.html", "$test_dir/test_docs/TestComponent/GeneratorTestClass.html", 'Проверяем, что сгенерированный файл класса и эталон одинаковы.');
    }
}
