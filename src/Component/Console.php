<?php
namespace PDG\Component;

/**
 * Консоль.
 */
class Console
{

    /**
     * Исходная директория.
     *
     * @var string
     */
    private $src_dir;

    /**
     * Директория назначения.
     *
     * @var string
     */
    private $dst_dir;

    /**
     * Директория шаблонов.
     *
     * @var string
     */
    private $views;

    /**
     * Исключаемые.
     *
     * @var iterable
     */
    private $exclude;

    /**
     * Шаблон файла.
     *
     * @var string
     */
    private $pattern;

    /**
     * Префикс.
     *
     * @var string
     */
    private $prefix;


    /**
     * Console constructor.
     *
     * @param JsonReader $json Объект-контейнер конфигурации
     */
    public function __construct(JsonReader $json)
    {
        $this->src_dir = $json->get('src', 'src');
        $this->dst_dir = $json->get('dst', 'docs');
        $this->views = $json->get('views');
        $this->exclude = $json->get('exclude');
        $this->pattern = $json->get('pattern');
        $this->prefix = $json->get('removed_prefix', '');
    }

    /**
     * Запустить.
     *
     * @param array $args Аргументы
     */
    public function run(array $args = [])
    {
        $dir_reader = new DirReader();
        $dir_reader->setDir($this->src_dir)
            ->setExclude($this->exclude)
            ->setFilePattern($this->pattern);
        $doc_reader = new DocReader();
        $parser = new Parser();
        $parser->setDocDir($this->dst_dir)
            ->setSrcDir($this->src_dir)
            ->setRemovedPrefix($this->prefix);
        $creator = new Creator($dir_reader, $doc_reader, $parser, $this->views, $this->dst_dir);
        $creator->create();
        if (\count($args) > 1) {
            $this->runArg(\str_replace('--', '', $args[1]));
        }
    }

    /**
     * Выполнить аргумент.
     *
     * @param string $arg Аргумент
     */
    private function runArg(string $arg): void
    {
        $method = 'argument' . \ucfirst(\strtolower($arg));
        if (\method_exists($this, $method)) {
            $this->{$method}();
        } else {
            echo "Неизвестный аргумент." . PHP_EOL;
        }
    }

    /**
     * Очистить кэш.
     */
    final function argumentClear(): void
    {
        $cache_dir = 'phpdocgen/cache';
        $dir_reader = new DirReader();
        $dir_reader->setDir($cache_dir)->do();
        foreach ($dir_reader->getFiles() as $file) {
            if (\file_exists($file)) {
                \unlink($file);
            }
        }
        echo "Кэш очищен." . PHP_EOL;
    }
}
