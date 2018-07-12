<?php

declare(strict_types=1);

namespace Test\Aidphp\Template;

use PHPUnit\Framework\TestCase;
use Aidphp\Template\Renderer;
use Interop\Renderer\RendererInterface;
use Aidphp\Template\HelpersInterface;
use RuntimeException;

class RendererTest extends TestCase
{
    const PATH = __DIR__ . '/Asset/';

    public function testConstructor()
    {
        $renderer = new Renderer(self::PATH, $this->createMock(HelpersInterface::class));
        $this->assertInstanceOf(RendererInterface::class, $renderer);
    }

    public function testRender()
    {
        $renderer = new Renderer(self::PATH, $this->createMock(HelpersInterface::class));
        $this->assertSame(file_get_contents(self::PATH . 'foo' . Renderer::DEFAULT_EXTENSION), $renderer->render('foo'));
    }

    public function testRenderInvalidTemplate()
    {
        $template = 'bar';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The template file "' . $template . '" does not exist');

        $renderer = new Renderer(self::PATH, $this->createMock(HelpersInterface::class));
        $renderer->render($template);
    }

    public function testExtend()
    {
        $renderer = new Renderer(self::PATH, $this->createMock(HelpersInterface::class));
        $content = $renderer->render('child');

        $this->assertSame('Main View' . PHP_EOL . 'Child Block' . PHP_EOL . 'Child View', $content);
    }

    public function testHelper()
    {
        $name   = 'foo';
        $helper = 'bar';

        $helpers = $this->createMock(HelpersInterface::class);
        $helpers->expects($this->once())
            ->method('get')
            ->with($name)
            ->willReturn($helper);

        $renderer = new Renderer(self::PATH, $helpers);
        $this->assertSame($helper, $renderer->helper($name));
    }
}