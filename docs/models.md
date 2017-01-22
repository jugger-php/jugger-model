# Model

Модель - это набор полей со список сопутствующих валидаторов, описаний, названий.

Работу с моделями лучше рассмотреть на примере с сущностью `People`.

## Создание модели

Минимальный код, для работы с моделью выглядит так:
```php
class People extends Model
{
    public static function getSchema(): array
    {
        return [];
    }
}
```

Таким образом получается пустая модель человек. Зачем это нужно? Незачем. Поэтому добавим поля `age`, `fio`, `sex` и `is_superman`:
```php
class People extends Model
{
    public static function getSchema(): array
    {
        return [
            new IntField([
                'name' => 'age',
            ]),
            new TextField([
                'name' => 'fio',
            ]),
            new TextField([
                'name' => 'sex',
            ]),
            new BoolField([
                'name' => 'is_superman',
            ]),
        ];
    }
}
```

Каждое поле должно быть наследником класса `BaseField`. [Подробнее про поля](docs/fields.md).

### Валидаторы

Чтобы добавить какую-либо логику и ограничения на поля, можно использовать валидаторы. Валидатор - это объект, который проверяет - удовлетворяет ли значение заданному условию?
В сущность `People`, можно добавить валидаторы:
- `age` в диапазоне от 3 до 150
- `fio` в диапазоне от 1 до 15 символов
- `sex` имеет только 2 значения: `man` или `woman` (данное условие решается изменением типа поля `EnumField`)
- `is_superman` динамический валидатор

После добавления валидаторов класс будет выглядеть так:
```php
class People extends Model
{
    public static function getSchema(): array
    {
        return [
            new IntField([
                'name' => 'age',
                'validators' => [
                    new RangeValidator(3, 150)
                ],
            ]),
            new TextField([
                'name' => 'fio',
                'validators' => [
                    new RangeValidator(1, 15)
                ],
            ]),
            new EnumField([
                'name' => 'sex',
                'values' => [
                    'man', 'woman'
                ],
                'validators' => [
                    new RequireValidator()
                ],
            ]),
            new BoolField([
                'name' => 'is_superman',
                'validators' => [
                    new DynamicValidator(function(bool $value) {
                        return $value === true;
                    })
                ],
            ]),
        ];
    }
}
```

Каждый валидатор должен быть наследником класса `BaseValidator`. [Подробнее про валидаторы](docs/validators.md).

### Hints & Labels

Для удобства работы и отображения (что скорее), можно также добавить для полей названия (labels) и подсказки (hints).
```php
class People extends Model
{
    // ...

    public static function getHints(): array
    {
        return [
            'is_superman' => 'Если человек супермен, это не скрыть никак',
        ];
    }

    public static function getLabels(): array
    {
        return [
            'age' => 'Возраcт',
            'fio' => 'ФИО',
            'sex' => 'Пол',
        ];
    }
}
```

В качестве ключа массива используется название поля, а в качестве значения `label` или `hint`.

### Обработчик

В заключении, чтобы модели добавить бизнес-логику, можно "навешать" на нее обработчики.
```php
class People extends Model
{
    // ...

    public static function getHandlers(): array
    {
        return [
            function(People $people) {
                // добавляем в базу запись
            },
            function(People $people) {
                // логгируем действие пользователя
            },
        ];
    }
}
```

Обработчик должен быть быть анонимной функцией (`Closure`), принимающей в качестве параметров объект модели. [Подробнее про обработчики](docs/handlers.md).

### Итого

В итоге полученный класс имеет вид:
```php
class People extends Model
{
    public static function getSchema(): array
    {
        return [
            new IntField([
                'name' => 'age',
                'validators' => [
                    new RangeValidator(3, 150)
                ],
            ]),
            new TextField([
                'name' => 'fio',
                'validators' => [
                    new RangeValidator(1, 15)
                ],
            ]),
            new EnumField([
                'name' => 'sex',
                'values' => [
                    'man', 'woman'
                ],
                'validators' => [
                    new RequireValidator()
                ],
            ]),
            new BoolField([
                'name' => 'is_superman',
                'validators' => [
                    new DynamicValidator(function(bool $value) {
                        return $value === true;
                    })
                ],
            ]),
        ];
    }

    public static function getHints(): array
    {
        return [
            'is_superman' => 'Если человек супермен, это не скрыть никак',
        ];
    }

    public static function getLabels(): array
    {
        return [
            'age' => 'Возраcт',
            'fio' => 'ФИО',
            'sex' => 'Пол',
        ];
    }

    public static function getHandlers(): array
    {
        return [
            function(People $people) {
                // добавляем в базу запись
            },
            function(People $people) {
                // логгируем действие пользователя
            },
        ];
    }
}
```

## Работа с моделью
