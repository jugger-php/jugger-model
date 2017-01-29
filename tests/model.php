<?php

use PHPUnit\Framework\TestCase;

use jugger\model\Model;
use jugger\model\field\IntField;
use jugger\model\field\TextField;
use jugger\model\field\EnumField;
use jugger\model\field\BoolField;
use jugger\model\handler\HandlerException;
use jugger\model\validator\RangeValidator;
use jugger\model\validator\RequireValidator;
use jugger\model\validator\DynamicValidator;

class People extends Model
{
    public static function getSchema(): array
    {
        return [
            new IntField([
                'name' => 'age',
                'validators' => [
                    // возвраст в диапазоне 3-150
                    new RangeValidator(3, 150)
                ],
            ]),
            new TextField([
                'name' => 'fio',
                'validators' => [
                    // ФИО с диапазоне 1-15 символов
                    new RangeValidator(1, 15)
                ],
            ]),
            new EnumField([
                'name' => 'sex',
                'values' => [
                    'man', 'woman'
                ],
                'validators' => [
                    // обязательно с выбранным полом
                    new RequireValidator()
                ],
            ]),
            new BoolField([
                'name' => 'is_superman',
                'value' => false,
                'validators' => [
                    // только супермены
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
            'sex' => 'Пол',
        ];
    }

    public static function getHandlers(): array
    {
        return [
            function() {
                throw new HandlerException("Internal handler");
            },
        ];
    }
}

class ModelTest extends TestCase
{
    public function testBase()
    {
        $people = new People();
        $people->age = 27;
        $people->fio = 'Ilya R';
        $people->sex = 'man';
        $people->is_superman = true;

        $this->assertEquals($people->age, 27);
        $this->assertEquals($people->fio, 'Ilya R');
        $this->assertEquals($people->sex, 'man');
        $this->assertTrue($people->is_superman);

        // not exists
        $this->assertTrue($people->existsField('age'));
        $this->assertTrue($people->existsField('fio'));
        $this->assertFalse($people->existsField('404 field'));

        // array access
        $this->assertEquals($people->age, $people['age']);
        $this->assertEquals($people->fio, $people['fio']);
        $this->assertEquals($people->sex, $people['sex']);

        $this->assertTrue(isset($people['age']));
        $this->assertTrue(isset($people['fio']));
        $this->assertFalse(isset($people['404 field 2']));

        $age = $people['age'];
        unset($people['age']);
        $this->assertNull($people['age']);
        $people['age'] = $age;

        return $people;
    }

    /**
     * @depends testBase
     */
    public function testValues(Model $people)
    {
        $values = $people->getValues();

        $this->assertEquals($values['age'], 27);
        $this->assertEquals($values['fio'], 'Ilya R');
        $this->assertEquals($values['sex'], 'man');
        $this->assertTrue($values['is_superman']);

        $values['age'] = 123;
        $people->setValues($values);
        $this->assertEquals($people['age'], 123);

        // dirty write

        $values = [
            'age'   => '456',
            'key'   => 'value',
            1234    => new stdClass,
        ];

        $people->setValues($values);
        $this->assertEquals($people['age'], 456);
        $this->assertFalse(isset($people['key']));
        $this->assertFalse(isset($people[1234]));
    }

    public function testValidators()
    {
        $superman = new People([
            'age' => 78,
            'fio' => 'Кларк Кент',
            'sex' => 'man',
            'is_superman' => true,
        ]);
        $this->assertTrue($superman->validate());
        $this->assertTrue(count($superman->getErrors()) == 0);

        $superman->age = 666;
        $this->assertFalse($superman->validate());
        $this->assertEquals(
            $superman->getError('age'),
            "Поле 'Возраcт': значение должно быть в диапазоне от 3 до 150"
        );

        $superman->fio = 'Кларк Джозеф Кент';
        $this->assertFalse($superman->validate());
        $this->assertEquals(
            $superman->getError('fio'),
            "Поле 'fio': значение должно быть в диапазоне от 1 до 15"
        );

        $superman->sex = null;
        $this->assertFalse($superman->validate());
        $this->assertEquals(
            $superman->getError('sex'),
            "Поле 'Пол': обязательно для заполнения"
        );

        $superman->is_superman = false;
        $this->assertFalse($superman->validate());
        $this->assertEquals(
            $superman->getError('is_superman'),
            "Поле 'is_superman': jugger\\model\\validator\\DynamicValidator"
        );

        $errors = $superman->getErrors();
        $this->assertEquals($errors['age'], "Поле 'Возраcт': значение должно быть в диапазоне от 3 до 150");
        $this->assertEquals($errors['fio'], "Поле 'fio': значение должно быть в диапазоне от 1 до 15");
        $this->assertEquals($errors['sex'], "Поле 'Пол': обязательно для заполнения");
        $this->assertEquals($errors['is_superman'], "Поле 'is_superman': jugger\\model\\validator\\DynamicValidator");
    }

    public function testAdditionalAttrs()
    {
        $superman = new People();

        // labels
        $this->assertEquals(
            "Возраcт",
            $superman::getLabel('age')
        );
        $this->assertEquals(
            "fio",
            $superman::getLabel('fio')
        );
        $this->assertEquals(
            "Пол",
            $superman::getLabel('sex')
        );
        $this->assertEquals(
            "is_superman",
            $superman::getLabel('is_superman')
        );

        // hints
        $this->assertEquals(
            "",
            $superman::getHint('age')
        );
        $this->assertEquals(
            "",
            $superman::getHint('fio')
        );
        $this->assertEquals(
            "",
            $superman::getHint('sex')
        );
        $this->assertEquals(
            "Если человек супермен, это не скрыть никак",
            $superman::getHint('is_superman')
        );
    }

    public function testHandlers()
    {
        $emptyModel = new class extends Model
        {
            public static function getSchema(): array
            {
                return [];
            }
        };
        // empty handler
        $this->assertTrue($emptyModel->handle()->isSuccess());
        // good handler
        $emptyModel->addHandler(function($emptyModel){});
        $this->assertTrue($emptyModel->handle()->isSuccess());

        // bad handler
        $people = new People();
        $result = $people->handle();
        $this->assertFalse($result->isSuccess());
        $this->assertEquals($result->getMessage(), "Internal handler");

        // сначала выполняются внутрение, затем динамические
        $people = new People();
        $people->addHandler(function() {
            throw new HandlerException("Handler 1");
        });
        $this->assertEquals($people->handle()->getMessage(), "Internal handler");

        // но можно пролезть вперед всех
        $people->addHandler(function() {
            throw new HandlerException("Handler 2");
        }, true);
        $this->assertEquals($people->handle()->getMessage(), "Handler 2");
    }

    public function testProcess()
    {
        $superman = new People();
        $errors = $superman->process();
        $this->assertTrue(count($errors) == 4);

        $superman->setValues([
            'age' => 666,
            'fio' => 'Кларк Кент',
            'sex' => 'man',
            'is_superman' => true,
        ]);
        $errors = $superman->process();
        $this->assertTrue(count($errors) == 1);
        $this->assertTrue(isset($errors['age']));

        $superman->age = 75;
        $errors = $superman->process();
        $this->assertTrue(count($errors) == 1);
        $this->assertEquals($errors['handlers'], 'Internal handler');
    }
}
