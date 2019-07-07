<?php

use X2Core\Foundation\Validator\ArrayValidator;
use X2Core\Foundation\Validator\Descriptor;
use X2Core\Foundation\Validator\Rules\Email;
use X2Core\Foundation\Validator\Rules\Enum;
use X2Core\Foundation\Validator\Rules\Ip;
use X2Core\Foundation\Validator\Rules\Length;
use X2Core\Foundation\Validator\Rules\NotBlank;
use X2Core\Foundation\Validator\Rules\Number;
use X2Core\Foundation\Validator\Rules\RegExp;
use X2Core\Foundation\Validator\Rules\Url;
use X2Core\Foundation\Validator\Validator;


/**
 * Class ValidatorTest
 * @package Test
 */
class ValidatorTest extends \TestsBasicFramework
{

    /**
     * return void
     */
    public function testRules(){

        // not blank rule general assert in both cases
        $notBlank = new NotBlank();
        $this->assertToFalse( $notBlank->onValidate("   ") );
        $this->assertToTrue( $notBlank->onValidate("  a ") );

        // not blank rule general assert in both cases
        $isEmail = new Email();
        $this->assertToFalse( $isEmail->onValidate("test@exa%^mple.com") );
        $this->assertToTrue( $isEmail->onValidate("test@example.com") );

        // not blank rule general assert in both cases
        $ip = new Ip();
        $this->assertToFalse( $ip->onValidate("127.0.0.1...1") );
        $this->assertToTrue( $ip->onValidate("127.0.0.1") );


        // not blank rule general assert in both cases
        $num = new Number(Number::INTEGER);
        $this->assertToFalse( $num->onValidate("11.11") );
        $this->assertToTrue( $num->onValidate("11") );
        $num = new Number();
        $this->assertToTrue( $num->onValidate("11.11") );

        // not blank rule general assert in both cases
        $regexp = new RegExp("^elm[1-3]");
        $this->assertToFalse( $regexp->onValidate("elm8") );
        $this->assertToTrue( $regexp->onValidate("elm245667") );

        // not blank rule general assert in both cases
        $enum = new Enum(["test","rules","phpunit"]);
        $this->assertToFalse( $enum->onValidate("rule") );
        $this->assertToTrue( $enum->onValidate("phpunit") );
        $this->assertToTrue( $enum->onValidate("test") );

        // not blank rule general assert in both cases
        $length = new Length(["min" => 4]);
        $this->assertToFalse( $length->onValidate("rul") );

        $length = new Length(["min" => 4, 'max' => 12]);
        $this->assertToTrue( $length->onValidate("phpunit") );

        $length = new Length(["min" => 4, 'max' => 8]);
        $this->assertToTrue( $length->onValidate("test-elm") );

        $url = new Url();
        $this->assertToTrue( $url->onValidate("http://example.com/data/sys?elm=2") );
    }

    /**
     * @return void
     */
    public function testDescriptor(){
        $desc = new Descriptor();
        $rules = $desc->isNotBlank()->matchWith("^a")->getRules();

        $this->assert(count($rules), 2);

        $ruleIsNotBlank = current($rules);

        next($rules);

        $ruleMtachWith = current($rules);

        // assert true in both cases
        $this->assertToTrue($ruleIsNotBlank instanceof NotBlank);
        $this->assertToTrue($ruleMtachWith instanceof RegExp);
        $this->assertToFalse($ruleIsNotBlank->onValidate('  '));
        $this->assertToFalse($ruleMtachWith->onValidate('test'));
    }

    /**
     * @return void
     */
    public function testSimpleValidator(){
        $desc = new Descriptor();
        $rules = $desc->mustEmail()->lengthMax(27)->getRules();
        $result = Validator::validateData('hello-world@test.com', $rules);

        // first case all is positive
        $this->assert(count($result), count($rules));
        $this->assertToTrue($result[0] === true AND $result[1] === true);

        // second case the just assert is true
        $result = Validator::validateData('email.is.too.long@long.test.com', $rules);
        $this->assertToTrue($result[0] === true AND $result[1] === Length::class);
    }

    /**
     * @return void
     */
    public function run(){

        $this->depends('testRules');
        $this->depends('testDescriptor');
        // should be true to this data
        $validator = new ArrayValidator([
           'status' => "200",
           'system' => 'deploy',
           'addr' => '127.0.0.1',
           'data-extra' => null
        ]);

        $validator->to('addr')->mustIp()->matchWith('^127');
        $validator->to('status')->isNumber(Number::INTEGER)->enum(["200","403","401","404"]);
        $validator->to('system')->isNotBlank()->lengthMax(7);
        $validator->require('system');
        $validator->require('addr');

        $result = $validator->validate();

        $this->assertToTrue( $result->isValid() );

    }
}