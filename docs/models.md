# Model

Модель - это набор полей со список сопутствующих валидаторов, описаний, названий.

Работу с моделями лучше рассмотреть на примере с сущностью `People`.

## Создание модели

Минимальный код, для работы с моделью выглядит так:
```php
use jugger\model\Model;

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

После того как модель создана можно приступать к работе:
```php
// create
$superman = new People([
    'age' => 78,
    'fio' => 'Кларк Кент',
    'sex' => 'man',
    'is_superman' => true,
]);

// get
$age = $superman->age;
$age = $superman['age'];
$age = $superman->getValue('age');

// set
$superman->age = 123;
$superman['age'] = 123;
$superman->setValue('age', 123);

// isset
isset($superman['age']);
$superman->existsField('age'); // true
$superman->existsField('404 field'); // false

// unset
unset($superman['age']);
is_null($superman['age']); // true

/**
 * "грязная" множественая запись - атрибуты которых нет, не будут записаны в модель
 */
$superman->setValues([
    'age'   => '456',
    'key'   => 'value',
    1234    => new stdClass,
]);

/*
$values = [
    'age' => 456,
    'fio' => 'Кларк Кент',
    'sex' => 'man',
    'is_superman' => true,
]
 */
$values = $superman->getValues();

/*
Labels & Hints
Обратите внимание, что это статические методы, доступ через ::
 */
$superman::getLabel('age'); // Возраcт
$superman::getLabel('is_superman'); // is_superman
$superman::getLabel('not found label'); // not found label

$superman::getHint('age'); // ""
$superman::getHint('is_superman'); // Если человек супермен, это не скрыть никак
$superman::getHint('not found label'); // ""

/*
Валидация
 */
if ($superman->validate()) {
    // success
}
else {
    $errors = $superman->getErrors();
    echo $errors['age']; // Поле 'Возраcт': значение должно быть в диапазоне от 3 до 150
}

/*
Обработчики, могут быть как внутреними так и динамическими
 */
$superman->addHandler(function(People $model) {
    // handler 1
});
$superman->addHandler(function(People $model) {
    // handler 2
});

$result = $superman->handle();
if ($result->isSuccess()) {
    // выполнились оба обработчика
}
else {
    // ошибка одного из обработчиков
    // после возникновения ошибки
    // далее обработка не идет
    $errorMessage = $result->getMessage();
}


/*
Обработчики + Валидация (или как правильно делать)
 */
if ($superman->validate()) {
    $message = $superman->handle()->getMessage();
    if ($message == 'success') {
        // success - данные валидны и обработаны
    }
    else {
        $errors = [$message];
    }
}
else {
    $errors = $superman->getErorrs();
}
```
