<?php
namespace PDG\Component;

/**
 * Читатель директорий.
 */
class DirReader
{
    /**
     * Директория.
     *
     * @var string
     */
    private $dir;

    /**
     * Исключить.
     *
     * @var iterable
     */
    private $exclude = ['.', '..', '.git', '.idea'];

    /**
     * Шаблон файла.
     *
     * @var string
     */
    private $file_pattern = '^[a-zA-Z0-9\_]+\.php$';

    /**
     * Файлы.
     *
     * @var array
     */
    private $files = [];

    /**
     * Установить директорию.
     *
     * @param string $dir Директория
     *
     * @return DirReader
     */
    public function setDir(string $dir): DirReader
    {
        $this->dir = $dir;

        return $this;
    }

    /**
     * Установить шаблон файла.
     *
     * @param string $file_pattern Шаблон файла
     *
     * @return DirReader
     */
    public function setFilePattern(string $file_pattern = null): DirReader
    {
        $this->file_pattern = $file_pattern ?? $this->file_pattern;

        return $this;
    }

    /**
     * Установить исключяемые директории.
     *
     * @param iterable $exclude Исключаемые директории
     *
     * @return DirReader
     */
    public function setExclude(iterable $exclude = null): DirReader
    {
        $this->exclude = \array_merge($this->exclude, $exclude ?? []);

        return $this;
    }

    /**
     * Выполнять.
     */
    public function do(): void
    {
        if (!\file_exists($this->dir)) {
            throw new \InvalidArgumentException("Невозможно открыть {$this->dir}" . PHP_EOL);
        }
        $this->readRecursive($this->dir);
    }

    /**
     * Получить список файлов.
     *
     * @return iterable
     */
    public function getFiles(): iterable
    {
        return $this->files;
    }

    /**
     * Получить директорию.
     *
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * Исключен.
     *
     * @param string $item Объект
     *
     * @return bool
     */
    private function isExcluded(string $item): bool
    {
        return \in_array($item, $this->exclude);
    }

    /**
     * Соответствует шаблону.
     *
     * @param string $pattern Шаблон
     * @param string $var Выражение
     *
     * @return bool
     */
    private function isMatchPattern(string $pattern, string $var): bool
    {
        return (bool)\preg_match("/$pattern/im", $var);
    }

    /**
     * ЧИтать директорию рекурсивно.
     *
     * @param string $dir Директория
     */
    private function readRecursive(string $dir)
    {
        foreach (\scandir($dir) as $item) {
            if (!$this->isExcluded($item)) {
                $item_path = $dir . '/' . $item;
                if (\is_dir($item_path)) {
                    $this->readRecursive($item_path);
                } elseif (\is_file($item_path)) {
                    if ($this->isMatchPattern($this->file_pattern, $item)) {
                        $this->files[] = $item_path;
                    }
                }
            }
        }
    }
}
