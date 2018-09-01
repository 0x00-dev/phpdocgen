<?php
namespace PDG\Component;

/**
 * Читатель JSON.
 */
class JsonReader
{
    /**
     * Имя файла.
     *
     * @var string
     */
    private $filename;

    /**
     * Хранилище.
     *
     * @var iterable
     */
    private $container = [];

    /**
     * Установить файл.
     *
     * @param string $filename Имя файла.
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * Получить.
     *
     * @param string $key Ключ
     * @param null $default Вернуть если не найден ключ
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->container[$key] ?? $default;
    }

    /**
     * Читать.
     */
    public function read(): void
    {
        if (\file_exists($this->filename)) {
            $file = \file_get_contents($this->filename);
            $this->container = \json_decode($file, true);
        }
    }
}
