<?php

namespace Eyrene\Database\Modeling\Relations;


abstract class Relation
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $bind;

    /**
     * HasOne constructor.
     * @param $from
     * @param $to
     * @param $bind
     */
    public function __construct($from, $to, $bind = null)
    {

        $this->from = $from;
        $this->to = $to;
        $this->bind = $bind;
    }

}