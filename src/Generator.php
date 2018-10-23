<?php
namespace PDG;

use PDG\Component\Console;
use PDG\Component\JsonReader;

/**
 * Генератор.
 */
class Generator
{
    /**
     * Файл конфигурации.
     *
     * @var string
     */
    private $config_file = 'phpdocgen.json';

    /**
     * Установить файл конфигурации.
     *
     * @param string $config_file Файл конфигурации
     *
     * @return Generator
     */
    public function setConfigFile(string $config_file): Generator
    {
        $this->config_file = $config_file;

        return $this;
    }

    /**
     * Запустить.
     */
    public function run(): void
    {
        $json = new JsonReader('phpdocgen.json');
        $json->read();
        $console = new Console($json);
        $console->run();
    }
}