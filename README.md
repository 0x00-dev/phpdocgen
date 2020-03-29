# phpdocgen

[![Build Status](https://travis-ci.org/0x00-dev/phpdocgen.svg?branch=master)](https://travis-ci.org/0x00-dev/phpdocgen)

PHP static documentation generator

![Image alt](https://github.com/0x00-dev/phpdocgen/raw/master/example.jpg)

## Как использовать:
1. Настроить параметры генерации.
### Работа через терминал:
Запустить `vendor/bin/phpdocgen` из директории с конфигурацией.
### Внедрение в код проекта:
```php
(new \PDG\Component\DocGenerator())
    ->setConfigFile('path_to_your_config/phpdocgen.json')
    ->run();
```


## Настройка генератора

### `phpdocgen.json`
```json
{
  "src" : "src",
  "dst" : "docs",
  "exclude" : [".", "..", ".git", ".idea"],
  "file_pattern" : "^[a-zA-Z0-9\\_]+\\.php$",
  "removed_prefix" : "",
  "twig_options" : {
    "this_name" :  "PHP static documentation generator"
  },
  "flags" : ["-l"]
}
```

|      Параметр       |                    Описание                   |             По умолчанию                |
| --------------------|:---------------------------------------------:| ---------------------------------------:|
| `src`               | Директория с кодом для генерации документации | *src*                                   |
| `dst`               | Конечная директория документации              | *docs*                                  | 
| `exclude`           | Исключаемые директории                        | *[".", "..", ".git", ".idea"]*          |
| `file_pattern`      | Шаблон поиска имени файла для генерации       | *^[a-zA-Z0-9\\_]+\\.php$*               |
| `removed_prefix`    | Удаялемый префикс пути                        |                                         |
| `twig_options`      | Параметры для передачи в шаблонизатор         |                                         |
| `this_name`         | Имя документации. Учавствует в title и brand  | PHP static documentation generator      |
| `flags`             | Флаги                                         | [-l]                                    |

**Наличие всех параметров в конфигурации не обязательно.**

|      Флаг       |                    Описание                   |
| ----------------|:---------------------------------------------:|
| `-l`            | Выводить список сгенерированных файлов.       |
