<?php
use Monad\Unit;

/**
 * Created by PhpStorm.
 * User: tjerk
 * Date: 9/27/13
 * Time: 6:07 PM
 */

require 'src/Monad/Prototype.php';
require 'src/Monad/Monad.php';
require 'src/Monad/Unit.php';

function alert($value)
{
    return "ALERT $value\n";
}

class UnitTest extends PHPUnit_Framework_TestCase
{
    public function testIdentity()
    {
        $identity = Unit::create();
        /** @var $monad \Monad\Monad */
        $monad = $identity('Hello world.');

        $this->assertEquals("ALERT Hello world.\n", $monad->bind('alert'));
    }

    public function testAjax()
    {
        $ajax = Unit::create()->lift('alert', 'alert');

        /** @var $monad \Monad\Monad */
        $monad = $ajax('Hello ajax.');

        $result = $monad->alert();
        $this->assertInstanceOf('\\Monad\\Monad', $result);

        $this->assertEquals("ALERT ALERT Hello ajax.\n\n", $result->bind('alert'));
    }

    public function testMaybe()
    {
        $maybe = Unit::create(function($monad, $value) {
            if (is_null($value)) {
                $monad->bind = function() use ($monad) {
                    return $monad;
                };
            }
            return $value;
        });

        $monad = $maybe(null);

        $this->assertInstanceOf('\\Monad\\Monad', $monad->bind('alert'));
    }

    public function testNumber()
    {
        $number = Unit::create()
            ->lift('inc', function($value) {
                return $value + 1;
            });

        $x = $number(100);

        $this->assertEquals("ALERT 102\n", $x->inc()->inc()->bind('alert'));
    }
}