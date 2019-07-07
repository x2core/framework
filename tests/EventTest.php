<?php

use Test\Event;
use X2Core\Contracts\ListenerInterface;
use X2Core\Dispatcher;
use X2Core\Types\Bundle;

class EventTest extends TestsBasicFramework
{
    const elmTest = 1;
    /**
     * @var Dispatcher
     */
    private $manager;

    /**
     *@desc create instance
     */
    public function prepareTest(){
        $this->manager = new Dispatcher();
        $this->manager->setBundle(new Bundle());
    }

    /**
     * @desc add event listener
     */
    public function addListeners(){
        // for class name string
        $this->manager->listen(Event::class, Test\Listener::class);

        // for Closure or anonymous function
        $this->manager->listen(Event::class, function(Bundle $bundle){
            $bundle->data2 = EventTest::elmTest;
        });

        // for anonymous class or any instance of an object to implements
        // the interface
        $this->manager->listen(Event::class, new class implements ListenerInterface {

            public function isValid()
            {
                return true;
            }

            public function exec($bundle, $context)
            {
                $bundle->data3 = EventTest::elmTest;
            }
        });
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
        $this->manager->dispatch($event);

        // this assert is correct if the first event is dispatched
        $this->assert($value, $this->manager->getBundle()->testEvent);

        // this second is correct if Closure (anonymous function) is dispatched
        $this->assert(EventTest::elmTest, $this->manager->getBundle()->data2);

        // this second is correct if anonymous class is dispatched
        $this->assert(EventTest::elmTest, $this->manager->getBundle()->data3);
    }
}