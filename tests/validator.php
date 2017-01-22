<?php

use PHPUnit\Framework\TestCase;

use jugger\model\field\AnyField;
use jugger\model\validator\EmailValidator;
use jugger\model\validator\RangeValidator;
use jugger\model\validator\RegexpValidator;
use jugger\model\validator\CompareValidator;
use jugger\model\validator\RequireValidator;
use jugger\model\validator\DynamicValidator;

class ValidatorTest extends TestCase
{
    public function testBase()
    {
        $field = new AnyField([
            'name' => 'test',
            'value' => 'default',
        ]);

        $field->addValidator(new RequireValidator());
        $field->addValidator(new EmailValidator());

        $field->addValidator(new RangeValidator(10));
        $field->addValidator(new RangeValidator(0,50));
        $field->addValidator(new RangeValidator(10,50));

        $field->addValidator(new CompareValidator('>', 10));
        $field->addValidator(new RegexpValidator("/pattern/"));

        $validators = $field->getValidators();
        $this->assertEquals(count($validators), 7);

        $this->assertInstanceOf(RequireValidator::class, $validators[0]);
        $this->assertInstanceOf(EmailValidator::class, $validators[1]);
        $this->assertInstanceOf(RangeValidator::class, $validators[2]);
        $this->assertInstanceOf(RangeValidator::class, $validators[3]);
        $this->assertInstanceOf(RangeValidator::class, $validators[4]);
        $this->assertInstanceOf(CompareValidator::class, $validators[5]);
        $this->assertInstanceOf(RegexpValidator::class, $validators[6]);
    }

    public function testRequire()
    {
        $field = new AnyField(['name' => 'test']);
        $field->addValidator(new RequireValidator());

        $field->setValue(null);
        $this->assertFalse($field->validate());
        $this->assertEquals(
            $field->getError(),
            "обязательно для заполнения"
        );

        $field->setValue([]);
        $this->assertTrue($field->validate());

        $field->setValue("");
        $this->assertTrue($field->validate());

        $field->setValue(0);
        $this->assertTrue($field->validate());

        $field->setValue("0");
        $this->assertTrue($field->validate());

        $field->setValue(false);
        $this->assertTrue($field->validate());
    }

    public function testEmail()
    {
        $field = new AnyField(['name' => 'test']);
        $field->addValidator(new EmailValidator());
        // эквивалент
        $field->addValidator(new RegexpValidator('/^[0-9a-z\-]+\@[0-9a-z\-]+\.[a-z]+$/i'));

        $field->setValue(null);
        $this->assertFalse($field->validate());
        $this->assertEquals(
            $field->getError(),
            "значение должно быть валидным email-адресом"
        );

        // true

        $field->setValue("word@word.domain");
        $this->assertTrue($field->validate());

        $field->setValue("123@word.domain");
        $this->assertTrue($field->validate());

        $field->setValue("word@123.domain");
        $this->assertTrue($field->validate());

        $field->setValue("123-word@word.domain");
        $this->assertTrue($field->validate());

        $field->setValue("word@word-123.domain");
        $this->assertTrue($field->validate());

        // false

        $field->setValue("word.ru");
        $this->assertFalse($field->validate());

        $field->setValue("@word.ru");
        $this->assertFalse($field->validate());

        $field->setValue("word@ru");
        $this->assertFalse($field->validate());

        $field->setValue("word@.ru");
        $this->assertFalse($field->validate());

        $field->setValue("word@word.123");
        $this->assertFalse($field->validate());

        $field->setValue("word@word.word-word");
        $this->assertFalse($field->validate());
    }

    public function testRange()
    {
        $field = new AnyField(['name' => 'test']);
        $field->addValidator(new RangeValidator(0));

        $field->setValue(null);
        $this->assertFalse($field->validate());
        $this->assertEquals(
            $field->getError(),
            "значение должно быть в диапазоне от 0"
        );

        /**
         * strings
         */
        $field->setValue("");
        $this->assertFalse($field->validate());

        $field->setValue("1");
        $this->assertTrue($field->validate());

        $field = new AnyField(['name' => 'test']);
        $field->addValidator(new RangeValidator(5, 10));

        $field->setValue("12345");
        $this->assertFalse($field->validate());

        $field->setValue("123456789");
        $this->assertTrue($field->validate());

        $field->setValue("1234567890");
        $this->assertFalse($field->validate());

        /**
         * numbers
         */
         $field = new AnyField(['name' => 'test']);
         $field->addValidator(new RangeValidator(0));

         $field->setValue(0);
         $this->assertFalse($field->validate());

         $field->setValue(0.1);
         $this->assertTrue($field->validate());

         $field->setValue(1);
         $this->assertTrue($field->validate());

         $field = new AnyField(['name' => 'test']);
         $field->addValidator(new RangeValidator(5, 10));

         $field->setValue(-2);
         $this->assertFalse($field->validate());

         $field->setValue(2);
         $this->assertFalse($field->validate());

         $field->setValue(5);
         $this->assertFalse($field->validate());

         $field->setValue(7);
         $this->assertTrue($field->validate());

         $field->setValue(10);
         $this->assertFalse($field->validate());

         $field->setValue(123);
         $this->assertFalse($field->validate());
    }

    public function testDynamic()
    {
        $field = new AnyField(['name' => 'test']);
        $field->addValidator(new DynamicValidator(function($value) {
            return true;
        }));

        $field->setValue(null);
        $this->assertTrue($field->validate());

        $field->setValue("");
        $this->assertTrue($field->validate());

        $field->setValue(-100);
        $this->assertTrue($field->validate());

        $field->setValue(0);
        $this->assertTrue($field->validate());

        $field->setValue([1,2,3]);
        $this->assertTrue($field->validate());

        $field = new AnyField(['name' => 'test']);
        $field->addValidator(new DynamicValidator(function($value) {
            return (int)($value) > 7;
        }));

        $field->setValue(0);
        $this->assertFalse($field->validate());

        $field->setValue(7);
        $this->assertFalse($field->validate());

        $field->setValue(10);
        $this->assertTrue($field->validate());
    }

    public function dataForCompare()
    {
        return [
            [
                '==',
                '0',
                [0, '0', false],
                [null, "", [], 1]
            ],
            [
                '===',
                '0',
                ['0'],
                [0, false, null, "", [], 1]
            ],
            [
                '!=',
                '0',
                [null, "", [], 1],
                [0, '0', false]
            ],
            [
                '!==',
                '0',
                [0, false, null, "", [], 1],
                ['0']
            ],
            [
                '>',
                'string',
                ['xyz', 123, 'абв', 'АБВ'],
                ['s', 'strin', 'abc', 'ABC', '123']
            ],
            [
                '>=',
                123,
                [124, 1000, []],
                [-10, 0, 100, 'wat?']
            ],
            [
                '<',
                'string',
                ['s', 'strin', 'abc', 'ABC', '123'],
                ['xyz', 123, 'абв', 'АБВ']
            ],
            [
                '<=',
                123,
                [-10, 0, 100, 'wat?'],
                [124, 1000, []]
            ],
        ];
    }

    /**
     * @dataProvider dataForCompare
     */
    public function testCompare($operator, $value, $validValues, $inValidValues)
    {
        $field = new AnyField(['name' => 'test']);
        $field->addValidator(new CompareValidator($operator, $value));

        foreach ($validValues as $x) {
            $field->setValue($x);
            $this->assertTrue($field->validate());
        }

        foreach ($inValidValues as $x) {
            $field->setValue($x);
            $this->assertFalse($field->validate());
        }
    }

    public function testCompareMessage()
    {
        $field = new AnyField([
            'name' => 'test',
            'value' => 123,
        ]);
        $field->addValidator(new CompareValidator('>', 456));
        $this->assertFalse($field->validate());
        $this->assertEquals(
            $field->getError(),
            "значение должно быть '> 456'"
        );
    }
}
