<?php

use Test\Event;
use X2Core\Contracts\ListenerInterface;
use X2Core\Dispatcher;

class EventTest extends TestsBasicFramework
{
    const elmTest = 1;
    /**
     * @var Dispatcher
     */
    private $manager;

    /**
     * @var int
     */
    private $elm;

    /**
     *@desc create instance
     */
    public function prepareTest(){
        $this->manager = new Dispatcher();
    }

    /**
     * @desc add event listener
     */
    public function addListeners(){
        // for class name string
        $this->manager->listen(Event::class, Test\Listener::class);

        // for Closure or anonymous function
        $this->manager->listen(Event::class, function(Event $event){
            $event->data2 = EventTest::elmTest;
        });

        // for anonymous class or any instance of an object to implements
        // the interface
        $this->manager->listen(Event::class, new class implements ListenerInterface {

            private $event;

            public function isValid()
            {
                return true;
            }

            public function exec($context)
            {
                if(is_object($context)){
                    $context->sample = EventTest::elmTest;
                }
            }

            /**
             * ListenerInterface constructor.
             * @param $event
             */
            public function __construct($event = NULL)
            {
                $this->event = new stdClass();
            }
        });
    }

    public function onTestBinder($event, $context){
        $this->elm = $context;
    }

    /**
     * @desc main execute test
     */
    public function run()
    {
        $value = 2; // any value should be equal to event result

        // call method dependencies
        $this->depends('prepareTest');
        $this->depends('addListeners');

        // instance an event
        $event = new Event($value);

        // fire
        $this->manager->dispatch($event, $context = new stdClass);

        // this assert is correct if the first event is dispatched
        $this->assert($value, $event->testEvent, 'std listener');

        // this second is correct if Closure (anonymous function) is dispatched
        $this->assert(EventTest::elmTest, $event->data2, 'by closure');

        // this second is correct if anonymous class is dispatched and if context is passed
        $this->assert(EventTest::elmTest, $context->sample, 'by anonymous listener');

    }
}