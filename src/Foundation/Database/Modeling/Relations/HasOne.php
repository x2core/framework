<?php

namespace Eyrene\Database\Modeling\Relations;


class HasOne extends Relation
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
    public function __construct($from, $to, $bind)
    {

        $this->from = $from;
        $this->to = $to;
        $this->bind = $bind;
    }

    /**
     * @param $fields
     * @return void
     */
    public function setBind($fields)
    {
        // TODO: Implement setBind() method.
    }
}