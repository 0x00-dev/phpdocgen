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
     */
    public function run()
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
    }
}
