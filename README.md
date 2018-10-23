# phpdocgen
PHP static documentation generator

[![Build Status](https://travis-ci.org/0x00-dev/phpdocgen.svg?branch=master)](https://travis-ci.org/0x00-dev/phpdocgen)

## Как использовать:
1. Скопировать файл `phpdocgen.json` в директорию генерации.
2. Настроить параметры генерации.
### Если проект был распакован/клонирован:
Запустить `phpdocgen/bin/phpdocgen` из директории с конфигурацией.
### Если проект установлен в качестве зависимости:
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
  }
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

**Наличие всех параметров в конфигурации не обязательно.**
