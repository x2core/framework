<?php

use X2Core\Util\Arr;
use X2Core\Util\Str;
use X2Core\Util\URL;

class IntroTest extends TestsBasicFramework
{

    /**
     * @desc To test few utilities from X2Core
     *
     * @return void
     */
    public function run()
    {
        // should be equal
        $arr = [2,3,5,3,2];
        $this->assertToTrue(Arr::has($arr, 5));

        // should be equal
        $this->assert(Str::toDashCase('helloWorld'), 'hello_world');

        $path = '/index/some_path';
        $result_url = URL::match(URL::MATCH_ARRAY_PARAM, ['index', '$path'], $path);

        $this->assertToTrue(isset($result_url['path']));

        $result_url = URL::match(URL::MATCH_ARRAY_PARAM, ['index_2', '$path'], $path);

        $this->assertToFalse(isset($result_url['path']));

        $someVar = 0;
        $struct = new \X2Core\Util\Structure([
            'elm.sys' => function ($data) use(&$someVar){
                $someVar = $data ;
            }
        ]);
        $elms = [ 'elm' => [ 'sys' => 1]];
        $struct->exec($elms);
        $this->assert($elms['elm']['sys'], $someVar);

        $strValue = 'Hello ${elm} and ${sys}';

        $rStr = Str::capture($strValue, '${', '}');
        $this->assert($rStr[1], 'sys');

        $this->assertToTrue(Str::start($strValue, 'Hello'));
        $this->assertToFalse(Str::start($strValue, 'He@llo'));
        $this->assertToTrue(Str::end('hello world!', 'world!'));

        // hard tests

//        $srcXML = (new Arr([
//            'eee' => 1111,
//            'make' =>  ['hello' => 1]
//        ]))->toXml('root');
//        $this->assertToTrue($srcXML === '<root eee="1111"><make hello="1"/></root>');

//        // test to parse array model
//        $doc = new \X2Core\Util\DOM('1.0');
//        $doc->appendModel([
//            'name' => 'system',
//            'value' => [
//                [
//                    'name' => 'service',
//                    'value' => 'Router'
//                ],
//                [
//                    'name' => 'service',
//                    'value' => 'Request',
//                    'attributes' => ['lazy' => true]
//                ]
//            ],
//            'attributes' => ['init' => 1, 'env' => 'prod']
//        ]);
//        var_dump($doc->toXML());

    }
}