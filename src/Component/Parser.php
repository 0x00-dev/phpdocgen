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
    private const ABOUT_PATTERN = '^\/[\*\s]+?(([a-zA-Zа-яА-Я0-9.\- ]+)[\.]$|({@inheritdoc}|@inheritdoc[\s]$))$';

    /**
     * Шаблон свойств.
     *
     * @const string
     */
    private const PROPERTY_PATTERN = '\/[*\s]+([a-zA-Zа-яА-Я0-9.\s\-\@\+,{}]+)[\s*]+(@var\s([a-zA-Z_\\\]+)|\{@inheritdoc\})[\s*\/]+([a-z]+)(\s[a-zA-Z]+)?\s\$([a-z_]+)(\s=\s([\s\na-zA-Z0-9\]\[\@\,\'\"\_\-]+))?\;$';

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
  
    private const IMPLEMENTS_PATTERN = 'implements\s([a-zA-Z0-9\_\\\, ]+)$';

    /**
     * Шаблон используется.
     *
     * @const string
     */
    private const USE_PATTERN = 'use\s(.*)\;$';

    /**
     * Шаблон метода.
     *
     * @const string
     */
    private const METHOD_PATTERN = '(\/\*\*)[\s]+([\*\s_\-\+.a-zA-Zа-яА-Я@$\/\|\{\},]+)\s(private|protected|public)\s(static\s)?function\s([a-zA-Z0-9_]+)\((([a-zA-Z_]+\s)?\$[a-zA-Z_]+[,\s=[a-z0-9A-Z\[\]\\\'\"\\\]+]{0,}){0,}\)(:\s?\??([a-zA-Z_]+))?';

    /**
     * Шаблон параметров метода.
     *
     * @const string
     */
    private const METHOD_INFO_PARAM_PATTERN = '@param\s([\w+\\\|]+)\s(\$([a-zA-Z0-9_]+))([a-zа-я0-9\s]+)?$';

    /**
     * Шаблон описания метода.
     *
     * @const string
     */
    private const METHOD_ABOUT_PATTERN = '[\* ]([a-zA-Zа-яА-Я0-9.\- ]+)[\.]$|{@inheritdoc}|@inheritdoc$';

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
     * Индекс property.pre_modifer.
     *
     * @const int
     */
    private const I_IF_PROPERTY_PREMODIFER = 5;

    /**
     * Индекс property.name.
     *
     * @const int
     */
    private const I_PROPERTY_NAME = 6;

    /**
     * Индекс property.default.
     *
     * @const int
     */
    private const I_PROPERTY_DEFAULT = 7;

    /**
     * Индекс method._info
     *
     * @const int
     */
    private const I_METHOD_INFO = 2;

    /**
     * Индекс method.visibility.
     *
     * @const int
     */
    private const I_METHOD_VISIBILITY = 3;

    /**
     * Индекс method.pre_modifer.
     *
     * @const int
     */
    private const I_IF_METHOD_PREMODIFER = 4;

    /**
     * Индекс method.name.
     *
     * @const int
     */
    private const I_METHOD_NAME = 5;

    /**
     * Индекс method.return.
     *
     * @const int
     */
    private const I_METHOD_RETURN = 9;

    /**
     * Длина метода имеющего описание.
     *
     * @const int
     */
    private const COUNT_METHOD_IS_HAS_ABOUT = 5;

    /**
     * Длина метода имеющего возвращаемое значение.
     *
     * @const int
     */
    private const COUNT_METHOD_IS_HAS_RETURN = 10;

    /**
     * Индекс method.params[N].type.
     *
     * @const int
     */
    private const I_METHOD_PARAM_TYPE = 1;

    /**
     * Индекс method.params[N].name.
     *
     * @const int
     */
    private const I_METHOD_PARAM_NAME = 2;

    /**
     * Индекс чистого имени параметра метода.
     *
     * @const int
     */
    private const I_METHOD_PARAM_CLEAR_NAME = 3;

    /**
     * Индекс method.params[N].about.
     *
     * @const int
     */
    private const I_METHOD_PARAM_ABOUT = 4;

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
        $methods = $this->getMethods();

        $this->setObjectParent($parent);
        $this->setObjectNamespace($namespace);
        $this->setObjectInterfaces($interfaces);
        $this->createParentDocName();
        $this->setObjectInfo($info);
        $this->setObjectUses($uses);
        $this->setObjectAbout($about);
        $this->setObjectProperties($properties);
        $this->setObjectMethods($methods);

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
        unset($tmp_result);

        return $result;
    }

    /**
     * Метод имеет пре-модификатор.
     *
     * @param iterable $object Объект
     *
     * @return bool
     */
    private function hasMethodPreModifer(iterable $object): bool
    {
        return \count($object) >= static::I_IF_METHOD_PREMODIFER ? \strlen(\trim($object[static::I_IF_METHOD_PREMODIFER])) > 0 : false;
    }

    /**
     * Поле имеет пре-модификатор.
     *
     * @param iterable $object Объект
     *
     * @return bool
     */
    private function hasFieldPreModifer(iterable $object): bool
    {
        return \count($object) >= static::I_IF_PROPERTY_PREMODIFER ? \strlen(\trim($object[static::I_IF_PROPERTY_PREMODIFER])) > 0 : false;
    }

    /**
     * Имеет интерфейсы.
     *
     * @return bool
     */
    private function hasInterfaces(): bool
    {
        return 0 !== \count($this->object['interfaces']);
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
            $properties[$i]['pre_modifer'] = $this->hasFieldPreModifer($field) ? \trim($field[static::I_IF_PROPERTY_PREMODIFER]) : null;
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
     * @param null $class Класс (селектор)
     *
     * @return string
     */
    private function createLink(string $href, string $text, $class = null): string
    {
        $clear_href = $this->removePrefix($href);

        return null === $class ? "<a href='/{$clear_href}'>{$text}</a>" : "<a href='/{$clear_href}' class='{$class}'>{$text}</a>";
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
            $uses_vars = \explode('\\', $use);
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
     * Получить методы.
     *
     * @return iterable|null
     */
    private function getMethods(): ?iterable
    {
        if ('ConfigurationInterface' === $this->getInfo()[static::I_NAME]) {
            var_dump($this->data);
        }
        return $this->findAll($this->data, static::METHOD_PATTERN);
    }

    /**
     * Получить описание метода.
     *
     * @param iterable $method Метод
     *
     * @return null|string
     */
    private function getMethodAbout(iterable $method): ?string
    {
        $about = $this->find($method[static::I_METHOD_INFO], static::METHOD_ABOUT_PATTERN, 0, ['Не описан.']);
        switch (\count($about)) {
            case 1:
                $info = \trim($about[0]);
                break;
            case 2:
                $info = \trim($about[1]);
                break;
            default:
                $info = \trim($about[0]);
                break;
        }
        if (!\in_array($info[\strlen($info)-1], ['.', '}'])) {
            $info .= '.';
        }

        return $this->isInheritDoc($info) ? $this->getParentDoc($method, $info) : $info;
    }

    /**
     * Получить документацию предка.
     *
     * @param iterable $method Метод
     * @param string $info Информация
     *
     * @return null|string
     */
    private function getParentDoc(iterable $method, string $info): ?string
    {
        $link = $info;
        $method_name = $method[static::I_METHOD_NAME];
        if ($this->object['parent']) {
            $link = $this->createLink("{$this->object['parent_doc_file']}#method_{$method_name}", $this->object['parent'] . '::' . $method_name);
        } elseif ($this->hasInterfaces()) {
            $links = null;
            foreach ($this->object['interfaces'] as $interface) {
                $interface_file = $this->object['interfaces_files'][$interface];
                $href_link = $this->createLink( "$interface_file#method_$method_name", $interface . '::' . $method_name);
                $links .= \file_exists("{$this->doc_dir}/$interface_file") ? $href_link : $interface;
            }
            $link = $links;
        }

        return $link;
    }

    /**
     * Установить интерфейсы объекта.
     *
     * @param string $interfaces Инерфейсы
     */
    private function setObjectInterfaces(?string $interfaces): void
    {
        if (null !== $interfaces) {
            $interfaces = \str_replace(' ', '', $interfaces);
            $this->object['interfaces'] = \explode(',', $interfaces);
            if (\count($this->object['interfaces']) > 0) {
                foreach ($this->object['interfaces'] as $interface) {
                    $this->object['interfaces_files'][$interface] = $interface;
                    $interface_doc = null;
                    $interface_path = $this->find($this->data, 'use\s(.*)' . \str_replace('\\', '\\\\', $interface) . '\;$', 1);
                    $interface_doc_file = null === $interface_path ? $interface :  $interface_path . $interface . '.html';
                    $reslashed_doc_file = \str_replace('\\', '/', "/$interface_doc_file");
                    $unprefixed_doc_file = $this->removePrefix($reslashed_doc_file);
                    if (\file_exists($this->doc_dir . $unprefixed_doc_file)) {
                        $interface_doc = $unprefixed_doc_file;
                    }
                    $this->object['interfaces_files'][$interface] = $interface_doc ? \substr($interface_doc, 1, \strlen($interface_doc) - 1) : $interface;
                    $this->object['interfaces_links'][$interface] = $interface_doc ? $this->createLink(\substr($interface_doc, 1, \strlen($interface_doc) - 1), $interface) : $interface;
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
            $link = '@todo';//TODO: Заменить ссылкой.
            if ($property['parent_doc']) {
                if ($this->object['parent']) {
                    $link = $this->createLink($this->object['parent_doc_file'] . "#{$property['name']}", $this->object['parent'] . '::' . $property['name']);
                }
                $this->object['properties'][$key]['about'] = $link;
            }
        }
    }

    /**
     * Установить методы.
     *
     * @param iterable $methods Методы
     */
    private function setObjectMethods(iterable $methods): void
    {
        if ('ConfigurationInterface' === $this->object['name']) {
            var_dump($methods);
        }
        $this->object['methods'] = [];
        foreach ($methods as $method) {
            $current_method = [];
            $current_method['name'] = $method[static::I_METHOD_NAME];
            $current_method['pre_modifer'] = $this->hasMethodPreModifer($method) ? \trim($method[static::I_IF_METHOD_PREMODIFER]) : null;
            $current_method['visibility'] = $method[static::I_METHOD_VISIBILITY];
            $current_method['return'] = count($method) >= static::COUNT_METHOD_IS_HAS_RETURN ? $method[static::I_METHOD_RETURN] : 'void';
            $return_class = $this->getTypeClassOrType($current_method['return']);
            $doc_file = "{$this->doc_dir}/$return_class.html";
            if ($current_method['return'] !== $return_class) {
                if (\file_exists($doc_file)) {
                    $return_class_link = $this->createLink( "$return_class.html", $current_method['return']);
                    $current_method['return'] = $return_class_link;
                } else {
                    $current_method['return'] = \str_replace('/', '\\', $return_class);
                }

            }
            $method_info = $this->prepareMethodInfo($method);
            $current_method['params'] = $method_info['params'];
            $current_method['about'] = $method_info['about'];
            $current_method['params_list'] = $method_info['params_list'];
            $this->object['methods'][] = $current_method;
        }
    }

    /**
     * Подготовить информацию метода.
     *
     * @param iterable $method Метод
     *
     * @return iterable
     */
    private function prepareMethodInfo(iterable $method): iterable
    {
        $info = [];
        $prepared_params = [];
        $params_list = [];
        $i = 0;
        $method_info = $method[static::I_METHOD_INFO];
        $params = $this->findAll($method_info, static::METHOD_INFO_PARAM_PATTERN);
        foreach ($params as $param) {
            $prepared_params[$i]['type'] = $param[static::I_METHOD_PARAM_TYPE];
            $prepared_params[$i]['name'] = $param[static::I_METHOD_PARAM_NAME];
            $prepared_params[$i]['clear_name'] = $param[static::I_METHOD_PARAM_CLEAR_NAME];
            $prepared_params[$i]['about'] = \count($param) >= static::COUNT_METHOD_IS_HAS_ABOUT ? \trim($param[static::I_METHOD_PARAM_ABOUT]) : 'Не описан.';
            $params_list[$i] = $param[static::I_METHOD_PARAM_CLEAR_NAME];
            $i++;
        }
        $info['params_list'] = $params_list;
        $info['params'] = $prepared_params;
        $info['about'] = $this->getMethodAbout($method);

        return $info;
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

    /**
     * Наследуемая документация.
     *
     * @param string $doc Документация
     *
     * @return bool
     */
    private function isInheritDoc(string $doc): bool
    {
        return \in_array($doc, ['@inheritdoc', '{@inheritdoc}']);
    }
}
