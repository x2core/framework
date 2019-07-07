<?php

use Test\Event;
use X2Core\Hub;

class HubTest extends TestsBasicFramework
{
    const elmTest = 1;
    /**
     * @var Hub
     */
    private $manager;

    /**
     *@desc create instance
     */
    public function prepareTest(){
        $this->manager = new Hub();
        $bundle = new stdClass();
        $bundle->record = [];
        $this->manager->setBundle($bundle);
    }

    /**
     * @desc add event listener
     */
    public function addListeners(){
        $this->manager->listen(Event::class, function(stdClass $bundle, Event $event){
            array_push($bundle->record,$event->data);
        });
    }

    /**
     * @desc main execute test
     */
    public function run()
    {
        $value1 = 1; // any value should be equal to event result
        $value2 = 2; // any value should be equal to event result

        // call method dependencies
        $this->depends('prepareTest');
        $this->depends('addListeners');

        // instance an event
        $event1 = new Event($value1);
        $event2 = new Event($value2);

        // add to hub
        $this->manager->push($event1);
        $this->manager->push($event2);

        // fire
        $this->manager->dispatchAs(Event::class, Hub::STACK);

        // the stack is inverse to queue then the last input is first that output
        // because value2 is in index -> 0
        $this->assert($value2, $this->manager->getBundle()->record[0]);
        $this->assert($value1, $this->manager->getBundle()->record[1]);
    }
}