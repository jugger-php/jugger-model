<?php

use PHPUnit\Framework\TestCase;

use jugger\model\Model;
use jugger\model\field\TextField;
use jugger\validator\RequireValidator;
use jugger\model\validator\RepeatValidator;

class LoginForm extends Model
{
    public static function getSchema(): array
    {
        return [
            new TextField([
                'name' => 'username',
            ]),
            new TextField([
                'name' => 'password',
                'validators' => [
                    new RequireValidator()
                ],
            ]),
            new TextField([
                'name' => 'password_repeat',
            ]),
        ];
    }

    public function init()
    {
        parent::init();
        $this->getField("password_repeat")->addValidator(
            new RepeatValidator("password", $this)
        );
    }
}

class RepeatValidatorTest extends TestCase
{
    public function testBase()
    {
        $form = new LoginForm([
            'username' => 'test',
        ]);
        $form->validate();
        $this->assertInstanceOf(RequireValidator::class, $form->getError('password'));
        $this->assertNull($form->getError('password_repeat'));

        $form->password = "qwerty123456";
        $form->validate();
        $this->assertNull($form->getError('password'));
        $this->assertInstanceOf(RepeatValidator::class, $form->getError('password_repeat'));

        $form->password_repeat = "qwerty123456";
        $form->validate();
        $this->assertNull($form->getError('password'));
        $this->assertNull($form->getError('password_repeat'));
    }
}
