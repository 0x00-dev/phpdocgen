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
        
        $expected_indexfile_descriptor = \fopen("$test_dir/docs/index.html", 'r');
        $etalon_indexfile_descriptor = \fopen("$test_dir/test_docs/index.html", 'r');
        
        $expected_indexfile_data = \fread($expected_indexfile_descriptor, \filesize("$test_dir/docs/index.html"));
        $etalon_indexfile_data = \fread($etalon_indexfile_descriptor, \filesize("$test_dir/test_docs/index.html"));
        
        $expected_indexfile_data = \str_replace("\n", '', $expected_indexfile_data);
        $etalon_indexfile_data = \str_replace("\n", '', $etalon_indexfile_data);
        
        $expected_indexfile_data = \str_replace("\r", '', $expected_indexfile_data);
        $etalon_indexfile_data = \str_replace("\r", '', $etalon_indexfile_data);
        
        \fclose($expected_indexfile_descriptor);
        \fclose($etalon_indexfile_descriptor);
        
        $this->assertEquals($expected_indexfile_data, $etalon_indexfile_data, 'Проверяем, что сгенерированный заглавный файл и эталон одинаковы.');
        
        $expected_classfile_descriptor = \fopen("$test_dir/docs/TestComponent/GeneratorTestClass.html",'r');
        $etalon_classfile_descriptor = \fopen("$test_dir/test_docs/TestComponent/GeneratorTestClass.html",'r');
       
        $expected_classfile_data = \fread($expected_file_descriptor, \filesize("$test_dir/docs/TestComponent/GeneratorTestClass.html"));
        $etalon_classfile_data = \fread($etalon_file_descriptor, \filesize("$test_dir/test_docs/TestComponent/GeneratorTestClass.html"));
        
        $expected_classfile_data = \str_replace("\n", '', $expected_classfile_data);
        $etalon_classfile_data = \str_replace("\n", '', $etalon_classfile_data);
        
        $expected_classfile_data = \str_replace("\r", '', $expected_classfile_data);
        $etalon_classfile_data = \str_replace("\r", '', $etalon_classfile_data);
        
        \fclose($expected_classfile_descriptor);
        \fclose($etalon_classfile_descriptor);
        
        $this->assertEquals($expected_classfile_data, $etalon_classfile_data, 'Проверяем, что сгенерированный файл класса и эталон одинаковы.');
    }
}
