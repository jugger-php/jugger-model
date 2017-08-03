<?php

use PHPUnit\Framework\TestCase;

use jugger\model\Model;
use jugger\model\field\IntField;
use jugger\model\field\TextField;
use jugger\model\field\EnumField;
use jugger\model\field\BoolField;
use jugger\validator\RangeValidator;
use jugger\validator\RequireValidator;
use jugger\validator\DynamicValidator;

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
                    new RangeValidator(1, 15),
                    new RequireValidator()
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
}

class ModelTest extends TestCase
{
    protected function getPeople()
    {
        $people = new People();
        $people->age = 27;
        $people->fio = 'Ilya R';
        $people->sex = 'man';
        $people->is_superman = true;
        return $people;
    }

    public function testBase()
    {
        $people = $this->getPeople();

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
    }

    public function testValues()
    {
        $people = $this->getPeople();
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
        $this->assertInstanceOf(RangeValidator::class, $superman->getError('age'));

        $superman->fio = 'Кларк Джозеф Кент';
        $this->assertFalse($superman->validate());
        $this->assertInstanceOf(RangeValidator::class, $superman->getError('fio'));

        $superman->sex = null;
        $this->assertFalse($superman->validate());
        $this->assertInstanceOf(RequireValidator::class, $superman->getError('sex'));

        $superman->is_superman = false;
        $this->assertFalse($superman->validate());
        $this->assertInstanceOf(DynamicValidator::class, $superman->getError('is_superman'));

        $errors = $superman->getErrors();
        $this->assertTrue($errors['age'] === $superman->getError('age'));
        $this->assertTrue($errors['fio'] === $superman->getError('fio'));
        $this->assertTrue($errors['sex'] === $superman->getError('sex'));
        $this->assertTrue($errors['is_superman'] === $superman->getError('is_superman'));
    }
}
