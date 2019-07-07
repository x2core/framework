<?php

namespace Types;


class TreeBinary
{
    /**
     * @var TreeBinary
     */
    private $left;

    /**
     * @var TreeBinary
     */
    private $right;

    /**
     * @var mixed|null;
     */
    private $value;

    /**
     * TreeBinary constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed|null $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return TreeBinary
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param TreeBinary $right
     */
    public function setRight(TreeBinary $right)
    {
        $this->right = $right;
    }

    /**
     * @return TreeBinary
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param TreeBinary $left
     */
    public function setLeft(TreeBinary $left)
    {
        $this->left = $left;
    }

    /**
     * @return bool
     */
    public function isEnd(){
        return !($this->left) && !($this->right);
    }
}