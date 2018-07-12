<?php

declare(strict_types=1);

namespace Test\Aidphp\View;

use PHPUnit\Framework\TestCase;
use Aidphp\Template\Helpers;
use Aidphp\Template\HelpersInterface;
use stdClass;
use InvalidArgumentException;

class HelpersTest extends TestCase
{
    public function testConstructor()
    {
        $helpers = new Helpers([
            'foo' => function () {},
            'bar' => function () {},
        ]);
        $this->assertInstanceOf(HelpersInterface::class, $helpers);
        $this->assertTrue($helpers->has('foo'));
        $this->assertTrue($helpers->has('bar'));
    }

    public function testRegister()
    {
        $helpers = new Helpers();
        $this->assertSame($helpers, $helpers->register('foo', function () {}));
        $this->assertTrue($helpers->has('foo'));
    }

    public function testGet()
    {
        $helpers = new Helpers([
            'foo' => function () {return new stdClass();},
        ]);

        $this->assertTrue($helpers->has('foo'));
        $helper = $helpers->get('foo');
        $this->assertInstanceOf(stdClass::class, $helper);
        $helper2 = $helpers->get('foo');
        $this->assertInstanceOf(stdClass::class, $helper2);
        $this->assertSame($helper, $helper2);
    }

    public function testGetInvalidHelper()
    {
        $name = 'foo';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The helper "' . $name . '" does not exist');

        $helpers = new Helpers();
        $helpers->get('foo');
    }
}