<?php
namespace PDG\Component;

/**
 * Парсер.
 */
class Parser
{
    /**
     * Данные.
     * 
     * @var string
     */
    private $data;

    /**
     * Объект.
     * 
     * @var iterable
     */
    private $object;

    /**
     * Директория исходников.
     *
     * @var string
     */
    private $src_dir;

    /**
     * Директория документов.
     *
     * @var string
     */
    private $doc_dir;

    /**
     * Удаляемый префикс пространства имен.
     *
     * @var string
     */
    private $removed_prefix;

    /**
     * Шаблон основной информации.
     *
     * @const string
     */
    private const INFO_PATTERN = '^(([a-z]+)\s)?(class|interface|trait)\s([a-zA-Z0-9\_]+)';

    /**
     * Шаблон пространства имен.
     *
     * @const string
     */
    private const NAMESPACE_PATTERN = '^namespace\s([a-zA-Z0-9\\\]+);$';

    /**
     * Шаблон описания класса.
     *
     * @const string
     */
    private const ABOUT_PATTERN = '^\/[\*\s]+?([\sa-zа-яА-ЯA-Z0-9*.\[;\]-_+=\-\)\(\`\~\@\\\'\"\:\?\/\>\<\!\#\$\%\^\&]+)[*\/]$';

    /**
     * Шаблон свойств.
     *
     * @const string
     */
    private const PROPERTY_PATTERN = '\/[*\s]+([a-zA-Zа-яА-Я0-9.\s\-\@\+,{}]+)[\s*]+(@var\s([a-z]+)|\{@inheritdoc\})[\s*\/]+([a-z]+)\s\$([a-z_]+)(\s=\s([\s\na-zA-Z0-9\]\[\@\,\'\"\_\-]+))?\;$';

    /**
     * Шаблон наследования.
     *
     * @const string
     */
    private const EXTENDS_PATTERN = 'extends\s([a-zA-Z0-9\_\\\]+)$';

    /**
     * Шаблон интерфейса.
     *
     * @const string
     */
    private const IMPLEMENTS_PATTERN = 'implements\s([a-zA-Z0-9\_\\\,]+)$';

    /**
     * Шаблон используется.
     */
    private const USE_PATTERN = 'use\s(.*)\;$';

    /**
     * Индекс modifer.
     *
     * @const int
     */
    private const I_MODIFER = 2;

    /**
     * Индекс type.
     *
     * @const int
     */
    private const I_TYPE = 3;

    /**
     * Индекс name.
     *
     * @const int.
     */
    private const I_NAME = 4;

    /**
     * Индекс namespace.
     *
     * @const int
     */
    private const I_NS = 1;

    /**
     * Индекс property.about.
     *
     * @const int
     */
    private const I_PROPERTY_ABOUT = 1;

    /**
     * Индекс property.type.
     *
     * @const int
     */
    private const I_PROPERTY_TYPE = 3;

    /**
     * Индекс property.visibility.
     *
     * @const int
     */
    private const I_PROPERTY_VISIBILITY = 4;

    /**
     * Индекс property.name.
     *
     * @const int
     */
    private const I_PROPERTY_NAME = 5;

    /**
     * Предустановленное значение.
     *
     * @const int
     */
    private const I_PROPERTY_DEFAULT = 7;

    /**
     * Установить директорию документов.
     *
     * @param string $doc_dir Директория документов
     *
     * @return Parser
     */
    public function setDocDir(string $doc_dir): Parser
    {
        $this->doc_dir = $doc_dir;

        return $this;
    }

    /**
     * Установить директорию исходников.
     *
     * @param string $src_dir Директория исходников
     *
     * @return Parser
     */
    public function setSrcDir(string $src_dir): Parser
    {
        $this->src_dir = $src_dir;

        return $this;
    }

    /**
     * Установить префикс.
     *
     * @param string $removed_prefix Префикс
     *
     * @return Parser
     */
    public function setRemovedPrefix(string $removed_prefix): Parser
    {
        $this->removed_prefix = $removed_prefix;

        return $this;
    }

    /**
     * Установить данные.
     *
     * @param string $data Данные
     *
     * @return Parser
     */
    public function setData(string $data): Parser
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Прочитать.
     *
     * @return iterable
     */
    public function read(): iterable
    {
        $this->clearObject();
        $about = $this->getAbout();
        $info = $this->getInfo();
        $properties = $this->getProperties();
        $uses = $this->getUses();
        $namespace = $this->getNamespace();
        $parent = $this->getParent();
        $interfaces = $this->getInterfaces();

        $this->setObjectParent($parent);
        $this->setObjectNamespace($namespace);
        $this->setObjectInterfaces($interfaces);
        $this->createParentDocName();
        $this->setObjectInfo($info);
        $this->setObjectUses($uses);
        $this->setObjectAbout($about);
        $this->setObjectProperties($properties);

        return $this->object;
    }

    /**
     * Собрать шаблон.
     *
     * @param string $pattern Шаблон
     *
     * @return string
     */
    private function makePattern(string $pattern): string
    {
        return "`$pattern`miu";
    }

    /**
     * Найти.
     *
     * @param string $haystack Данные
     * @param string $pattern Шаблон
     * @param int $index Индекс вхождения
     * @param null $default Вернуть при неудаче
     *
     * @return mixed
     */
    private function find(string $haystack, string $pattern, int $index = null, $default = null)
    {
        \preg_match($this->makePattern($pattern), $haystack, $result);

        return \count($result) > 0 ? $index ? $result[$index] : $result : $default;
    }

    /**
     * Найти все.
     *
     * @param string $haystack Данные
     * @param string $pattern Шаблон
     * @param int $index Индекс
     *
     * @return iterable
     */
    private function findAll(string $haystack, string $pattern, int $index = null): iterable
    {
        \preg_match_all($this->makePattern($pattern), $haystack, $result, \PREG_SET_ORDER);
        if(null !== $index) {
            $tmp_result = [];
            foreach ($result as $item) {
                $tmp_result[] = $item[$index];
            }
            $result = $tmp_result;
        }

        return $result;
    }

    /**
     * Собрать свойства.
     *
     * @param iterable $data Данные
     *
     * @return iterable
     */
    private function makePropertyContainer(iterable $data): iterable
    {
        $properties = [];
        $i=0;
        foreach ($data as $field)
        {
            if ($field[static::I_PROPERTY_TYPE] !== $type = $this->getTypeClassOrType($field[static::I_PROPERTY_TYPE])) {
                $type_class = $this->revertSlashes($type);
                $type_class_file = "{$this->src_dir}/$type_class.php";
                $link = \file_exists($type_class_file) ? $this->createLink("$type_class.html", $field[static::I_PROPERTY_TYPE]) : $type_class;
                $properties[$i]['type'] = $link;
            } else {
                $properties[$i]['type'] = $field[static::I_PROPERTY_TYPE];
            }
            $properties[$i]['about'] = $field[static::I_PROPERTY_ABOUT];
            $properties[$i]['visibility'] = $field[static::I_PROPERTY_VISIBILITY];
            $properties[$i]['name'] = $field[static::I_PROPERTY_NAME];
            $properties[$i]['default'] = \count($field) === 8 ? $field[static::I_PROPERTY_DEFAULT] : null;
            $properties[$i]['parent_doc'] = (($field[2] === '{@inheritdoc}' || $field[2] === '@inheritdoc') && \strlen(\trim($properties[$i]['about'])) === 0);
            $i++;
        }
        unset($i, $data);

        return $properties;
    }

    /**
     * Создать ссылку.
     *
     * @param string $href Ссылка
     * @param string $text Текст
     *
     * @return string
     */
    private function createLink(string $href, string $text): string
    {
        $clear_href = $this->removePrefix($href);

        return "<a href='/{$clear_href}'>{$text}</a>";
    }

    /**
     * Очистить объект.
     */
    private function clearObject(): void
    {
        $this->object = null;
    }

    /**
     * Получить класс.
     *
     * @param string $namespace Полный путь к классу
     *
     * @return string
     */
    private function getClass(string $namespace): string
    {
        $paths = \explode('\\', $namespace);
        $class = $paths[\count($paths) - 1];
        unset($paths);

        return $class;
    }

    /**
     * Получить класс типа или тип.
     *
     * @param string $type Тип
     *
     * @return string
     */
    private function getTypeClassOrType(string $type): string
    {
        $uses = $this->getUses();

        return isset($uses[$type]) ? $this->removePrefix($this->revertSlashes($uses[$type])) : $type;
    }

    /**
     * Перевернуть слэши.
     *
     * @param string $string Строка
     *
     * @return string
     */
    private function revertSlashes(string $string): string
    {
        return \str_replace('\\', '/', $string);
    }

    /**
     * Получить описание.
     *
     * @return string
     */
    private function getAbout(): string
    {
        $about = $this->find($this->data, static::ABOUT_PATTERN, 1, 'Отсутствует');

        return \str_replace('*', '', $about);
    }

    /**
     * Получить информацию.
     *
     * @return iterable
     */
    private function getInfo(): iterable
    {
        return $this->find($this->data, static::INFO_PATTERN);
    }

    /**
     * Получить свойства.
     *
     * @return iterable
     */
    private function getProperties(): iterable
    {
        $properties = $this->findAll($this->data, static::PROPERTY_PATTERN);

        return $this->makePropertyContainer($properties);
    }

    /**
     * Получить подключения классов.
     *
     * @return iterable
     */
    private function getUses(): iterable
    {
        $uses = $this->findAll($this->data, static::USE_PATTERN, 1);
        $named_uses = [];
        foreach ($uses as $use) {
            $uses_vars = explode('\\', $use);
            $named_uses[$uses_vars[\count($uses_vars) - 1]] = $use;
        }

        return $named_uses;
    }

    /**
     * Получить пространство имен.
     *
     * @return string
     */
    private function getNamespace(): string
    {
        return $this->find($this->data, static::NAMESPACE_PATTERN, static::I_NS, 'Не указано');
    }

    /**
     * Получить родителя.
     *
     * @return string
     */
    private function getParent(): ?string
    {
        return $this->find($this->data, static::EXTENDS_PATTERN, 1);
    }

    /**
     * Получить интерфейсы.
     *
     * @return string|null
     */
    private function getInterfaces(): ?string
    {
        return $this->find($this->data, static::IMPLEMENTS_PATTERN, 1);
    }

    /**
     * Установить интерфейсы объекта.
     *
     * @param string $interfaces Инерфейсы
     */
    private function setObjectInterfaces(?string $interfaces): void
    {
        if (null !== $interfaces) {
            $this->object['interfaces'] = \explode(',', $interfaces);
            if (count($this->object['interfaces']) > 0) {
                foreach ($this->object['interfaces'] as $interface) {
                    $interface_path = $this->find($this->data, 'use\s(.*)' . \str_replace('\\', '\\\\', $interface) . '\;$', 1);
                    $interface_doc_file = ($interface_path ?? $this->object['namespace']) . '/' . $interface . '.html';
                    $interface_doc_file = \str_replace('//', '/', \str_replace('\\', '/', $interface_doc_file));
                    $interface_doc_file = $this->removePrefix($interface_doc_file);
                    $this->object['interfaces_files'] = [];
                    $this->object['interfaces_files'][$interface] = $interface_doc_file;
                }
            }
        }
    }

    /**
     * Установить предка объекта.
     *
     * @param string $parent Предок
     */
    private function setObjectParent(?string $parent): void
    {
        $this->object['parent'] = $parent;
    }

    /**
     * Установить подключения классов объекта.
     *
     * @param iterable $uses Подключения классов
     */
    private function setObjectUses(iterable $uses): void
    {
        $this->object['uses'] = [];
        if ($uses) {
            foreach ($uses as $use) {
                $this->object['uses'][$this->getClass($use)] = $use;
            }
        }
    }

    /**
     * Установить синформацию объекта.
     *
     * @param iterable $info Информация
     */
    private function setObjectInfo(iterable $info): void
    {
        $this->object['type'] = $info[static::I_TYPE];
        $this->object['name'] = $info[static::I_NAME];
        $this->object['modifer'] = $info[static::I_MODIFER];
    }

    /**
     * Установить пространство имен объекта.
     *
     * @param string $namespace Пространство имен
     */
    private function setObjectNamespace(string $namespace): void
    {
        $this->object['namespace'] = $namespace;
    }

    /**
     * Установить описание объекта.
     *
     * @param string $about Описание
     */
    private function setObjectAbout(string $about): void
    {
        $this->object['about'] = $about;
    }

    /**
     * Установить свойства объекта.
     *
     * @param iterable $properties Свойства
     */
    private function setObjectProperties(iterable $properties): void
    {
        $this->object['properties'] = $properties;
        foreach ($properties as $key => $property) {
            $link = '@todo';
            if ($property['parent_doc']) {
                if ($this->object['parent']) {
                    $link = $this->createLink($this->object['parent_doc_file'] . "#{$property['name']}", $this->object['parent'] . '::' . $property['name']);
                }
                $this->object['properties'][$key]['about'] = $link;
            }
        }
    }

    /**
     * Создать имя документа предка.
     */
    private function createParentDocName(): void
    {
        if ($this->object['parent']) {
            $parent_path = $this->find($this->data, '~use\s(.*)' . \str_replace('\\', '\\\\', $this->object['parent']) . '\;$~miu');
            $parent_doc_file = ($parent_path ?? $this->object['namespace']) . '/' . $this->object['parent'] . '.html';
            $parent_doc_file = $this->revertSlashes($parent_doc_file);
            $parent_doc_file = $this->removePrefix($parent_doc_file);
            $this->object['parent_doc_file'] = \str_replace('//', '/', \str_replace('\\', '/', $parent_doc_file));
        }
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
}
