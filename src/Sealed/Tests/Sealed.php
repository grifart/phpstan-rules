<?php declare(strict_types = 1);

namespace Grifart\PHPStanRules\Sealed\Tests;

/**
 * @sealed
 */
abstract class Sealed {

}

class X extends Sealed {

}

class Y extends Sealed {

}
