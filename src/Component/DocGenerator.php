<?php
namespace PDG\Component;


/**
 * Генератор.
 */
class DocGenerator
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
     * @return DocGenerator
     */
    public function setConfigFile(string $config_file): DocGenerator
    {
        $this->config_file = $config_file;

        return $this;
    }

    /**
     * Запустить.
     */
    public function run(): void
    {
        $json = new JsonReader($this->config_file);
        $json->read();
        $console = new Console($json);
        $console->run();
    }
}