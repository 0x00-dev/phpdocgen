<?php
namespace PDG\Component;

use Composer\Script\Event;

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
     * Опции.
     *
     * @var iterable
     */
    private $options = [];

    /**
     * Флаги.
     *
     * @var iterable
     */
    private $flags = [];


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
        $this->options = $json->get('twig_options', []);
        $this->flags = $json->get('flags', []);
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
        $creator = new Creator($dir_reader, $doc_reader, $parser, $this->options, $this->views, $this->dst_dir);
        $creator->setRemovedPrefix($this->prefix);
        $creator->setFlags($this->flags);
        $creator->setViews($this->views);
        $creator->create();
    }
    
    /**
     * Выполнить действия после установки.
     */
     public static function postPackageInstall(Event $event)
     {
 		var_dump($event->getComposer()->getConfig());
     	$vendor_dir = $event->getComposer()->getConfig()->get('vendor-dir');
     	$pdg_bin = $vendor_dir . '/0x00-dev/phpdocgen/bin/phpdocgen';
     	$dst_bin = 'bin';
     	if (!\file_exists($dst_bin)) {
     		\mkdir($dstbin);
     	}
     	\copy($pgdbin, $dstbin . '/phpdocgen');
     }
}

