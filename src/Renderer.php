<?php

declare(strict_types=1);

namespace Aidphp\Template;

use Interop\Renderer\RendererInterface;
use RuntimeException;

class Renderer implements RendererInterface
{
    const DEFAULT_EXTENSION = '.phtml';

    protected $path;
    protected $helpers;
    protected $extension;
    protected $current;
    protected $parent  = [];
    protected $content = '';
    protected $capture = [];
    protected $blocks  = [];

    public function __construct(string $path, HelpersInterface $helpers, string $extension = self::DEFAULT_EXTENSION)
    {
        $this->path = $path;
        $this->helpers = $helpers;
        $this->extension = $extension;
    }

    public function helper($name)
    {
        return $this->helpers->get($name);
    }

    public function render(string $template, array $parameters = []): string
    {
        $this->current = $template;

        $file = $this->path . $template . $this->extension;

        if (! file_exists($file))
        {
            throw new RuntimeException('The template file "' . $template . '" does not exist');
        }

        $content = $this->include($file, $parameters);

        if (isset($this->parent[$template]))
        {
            $this->content = $content;
            $content = $this->render($this->parent[$template], $parameters);
            unset($this->parent[$template]);
        }

        return $content;
    }

    public function extend(string $template)
    {
        $this->parent[$this->current] = $template;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function block(string $name, string $default = ''): string
    {
        return $this->blocks[$name] ?? $default;
    }

    public function start(string $name)
    {
        $this->capture[] = $name;
        ob_start();
    }

    public function stop()
    {
        $name = array_pop($this->capture);
        $this->blocks[$name] = ob_get_clean();
    }

    protected function include(string $__file__, array $__parameters__ = []): string
    {
        extract($__parameters__);
        ob_start();
        include($__file__);
        return ob_get_clean();
    }
}