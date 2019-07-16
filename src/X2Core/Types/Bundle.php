<?php

namespace X2Core\Types;

/**
 * Class Bundle
 * @package X2Core\Types
 *
 * This class is package manager with container system
 *
 */
class Bundle implements \ArrayAccess, \Countable
{
  use Container;
}