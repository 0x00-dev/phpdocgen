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
            ->setConfigFile("$test_dir/phpdocgen.json")
            ->run();
        $this->assertDirectoryExists("$test_dir/docs", 'Проверяем, что директория документации создалась.');
        $this->assertFileExists("$test_dir/docs/index.html", 'Проверяем, что заглавный файл страницы создан.');
        $this->assertDirectoryExists("$test_dir/docs/TestComponent", 'Проверяем, что директория TestComponent создана.');
        $this->assertFileExists("$test_dir/docs/TestComponent/GeneratorTestClass.html", 'Проверяем, что документация для GeneratorTest создана.');
        $this->assertFileEquals("$test_dir/docs/index.html", "$test_dir/test_docs/index.html", 'Проверяем, что сгенерированный заглавный файл и эталон одинаковы.');
        $expected_file_descriptor = \fopen("$test_dir/docs/TestComponent/GeneratorTestClass.html",'r');
        $etalon_file_descriptor = \fopen("$test_dir/test_docs/TestComponent/GeneratorTestClass.html",'r');
        $expected_file_data = \fread($expected_file_descriptor, \filesize("$test_dir/docs/TestComponent/GeneratorTestClass.html"));
        $etalon_file_data = \fread($etalon_file_descriptor, \filesize("$test_dir/test_docs/TestComponent/GeneratorTestClass.html"));
        \fclose($expected_file_descriptor);
        \fclose($etalon_file_descriptor);
        $this->assertEquals($expected_file_data, $etalon_file_data, 'Проверяем, что сгенерированный файл класса и эталон одинаковы.');
    }
}
