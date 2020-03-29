<?php
namespace PDG\Component;


/**
 * Создатель.
 */
class Creator
{
    /**
     * Читатель директорий.
     *
     * @var DirReader
     */
    private $dir_reader;

    /**
     * Читатель документов.
     *
     * @var DocReader
     */
    private $doc_reader;

    /**
     * Парсер.
     *
     * @var Parser
     */
    private $parser;

    /**
     * Директория шаблонов.
     *
     * @var string
     */
    private $views;

    /**
     * Директория назначения.
     *
     * @var string
     */
    private $dst_dir;

    /**
     * Префикс.
     *
     * @var string
     */
    private $removed_prefix;

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
     * Флаг списка.
     */
    public const LIST_FLAG = "-l";

    /**
     * Creator constructor.
     *
     * @param DirReader $dir_reader Читатель директорий
     * @param DocReader $doc_reader Читатель документов
     * @param Parser $parser Парсер
     * @param iterable $options Опции
     * @param string|null $views Директория шаблонов
     * @param string|null $dst_dir Директория назначения
     */
    public function __construct(DirReader $dir_reader, DocReader $doc_reader, Parser $parser, iterable $options = [], string $views = null, string $dst_dir = null)
    {
        $this->dir_reader = $dir_reader;
        $this->doc_reader = $doc_reader;
        $this->parser = $parser;
        $this->views = $views ?? '/twig_tpl';
        $this->dst_dir = $dst_dir ?? 'docs';
        $this->options = $options;
    }

    /**
     * Установить флаги.
     *
     * @param iterable $flags Флаги
     */
    public function setFlags(iterable $flags): void
    {
        $this->flags = $flags;
    }

    /**
     * Установить директорию хранения шаблонов.
     *
     * @param string $views Директория хранения шаблонов.
     *
     * @return Creator
     */
    public function setViews(?string $views): Creator
    {
        $this->views = $views ?? '/twig_tpl';

        return $this;
    }

    /**
     * @param string $removed_prefix
     */
    public function setRemovedPrefix(string $removed_prefix): void
    {
        $this->removed_prefix = $removed_prefix;
    }

    /**
     * Создать.
     */
    public function create()
    {
        $link_file = $this->getViewsPath() . '/tmp/links.html.twig';
        $this->clear($link_file, $this->dst_dir);
        \touch($link_file);
        $this->dir_reader->do();
        $files = $this->sort($this->dir_reader->getFiles());
        $twig = $this->getRender();
        foreach ($files as $file) {
            $data = $this->doc_reader->setClassFile($file)->read();
            $items = $this->parser->setData($data)->read();
            $html_file_path = $this->phpToHtmlExt($file);
            if (\in_array(self::LIST_FLAG, $this->flags)) {
                echo $html_file_path . PHP_EOL;
            }
            $unstarted_path = $this->trimSrcPath($this->trimPathStart($html_file_path));
            $unprefixed_path = $this->removePrefix($unstarted_path);
            $link = $twig->render('link.html.twig', ['link' => [
                'href' => '/' . $this->phpToHtmlExt($unprefixed_path),
                'text' => \str_replace('.php', '', $this->getFilename($file)),
            ]]);
            \file_put_contents($this->getViewsPath() . '/tmp/links.html.twig', $link, FILE_APPEND);
            unset($items);
        }
        foreach ($files as $file) {
            $twig = $this->getRender();
            $file_path = \str_replace($this->dir_reader->getDir() . '/', '', $file);
            if ($this->flags)
            $html_file_path = $this->phpToHtmlExt($file_path);
            $unstarted_path = $this->trimPathStart($html_file_path);
            $docs_file_path = "{$this->dst_dir}/$unstarted_path";
            if (!\file_exists(\dirname($docs_file_path))) {
                \mkdir(\dirname($docs_file_path), 0755, true);
            }
            \touch($docs_file_path);
            $item_data = $this->doc_reader->setClassFile($file)->read();
            $object = $this->parser->setData($item_data)->read();
            $file_data = $twig->render('class.html.twig', ['object' => $object]);
            \file_put_contents($docs_file_path, $file_data);
        }
        $index_file = "{$this->dst_dir}/index.html";
        @\unlink($index_file);
        \touch($index_file);
        $data = $twig->render('base.html.twig', $this->options);
        \file_put_contents($index_file, $data);
    }

    /**
     * Удалить префикс.
     *
     * @param string $string Строка
     *
     * @return string
     */
    private function removePrefix(string $string): string
    {
        $clear_string = \str_replace($this->removed_prefix, '', $string);

        return $clear_string;
    }

    /**
     * Замена расширения php на html.
     *
     * @param string $file Имя файла
     *
     * @return string
     */
    private function phpToHtmlExt(string $file): string
    {
        return \str_replace('.php', '.html', $file);
    }

    /**
     * Удалить начало пути.
     *
     * @param string $path Путь
     *
     * @return string
     */
    private function trimPathStart(string $path): string
    {
        return \str_replace('./', '', $path);
    }

    /**
     * Удалить src/ из строки.
     *
     * @param string $path Путь
     *
     * @return string
     */
    private function trimSrcPath(string $path): string
    {
        return \str_replace('src/', '', $path);
    }

    /**
     * Получить путь к видам.
     *
     * @return string
     */
    private function getViewsPath(): string
    {
        return  $views_path = __DIR__ . '/../..' . $this->views;
    }

    /**
     * Получить имя файла.
     *
     * @param string $file_path Путь файла
     *
     * @return string
     */
    private function getFilename(string $file_path): string
    {
        $file_array = \explode('/', $file_path);

        return $file_array[count($file_array) - 1];
    }

    /**
     * Получить рендер.
     *
     * @return \Twig\Environment
     */
    private function getRender()
    {
        $loader = new \Twig\Loader\FilesystemLoader($this->getViewsPath());

        $twig = new \Twig\Environment($loader, [
            'debug' => true,
            'cache' => false,
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        return $twig;
    }

    /**
     * Сортировать.
     *
     * @param iterable $files Файлы
     *
     * @return array
     */
    private function sort(iterable $files)
    {
        $tmp_array = [];
        foreach ($files as $key => $val)
        {
            $key_name = \str_replace('.php', '', $this->getFilename($val));
            $tmp_array[$key_name] = $val;
        }
        \ksort($tmp_array);

        return \array_values($tmp_array);
    }

    /**
     * Очистить.
     *
     * @param mixed ...$vars
     */
    private function clear(...$vars): void
    {
        $links = \func_get_args();
        foreach ($links as $link) {
            @unlink($link);
        }
    }
}
