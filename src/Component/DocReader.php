<?php
namespace PDG\Component;

/**
 * Читатель документа.
 */
class DocReader
{

    /**
     * Класс.
     *
     * @var string
     */
    private $class_file;

    /**
     * Установить класс.
     *
     * @param string $class_file Класс
     *
     * @return DocReader
     */
    public function setClassFile(string $class_file): DocReader
    {
        $this->class_file = $class_file;

        return $this;
    }

    /**
     * Читать.
     *
     * @return string
     */
    public function read(): string
    {
        if (!\file_exists($this->class_file)) {
            throw new \InvalidArgumentException("Невозможно открыть {$this->class_file}" . PHP_EOL);
        }

        return \file_get_contents($this->class_file);
    }
}
