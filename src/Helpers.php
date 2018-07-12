<?php

declare(strict_types=1);

namespace Aidphp\Template;

use InvalidArgumentException;

class Helpers implements HelpersInterface
{
    protected $factories = [];
    protected $helpers   = [];

    public function __construct(array $factories = [])
    {
        foreach ($factories as $name => $factory)
        {
            $this->register($name, $factory);
        }
    }

    public function register(string $name, callable $factory): self
    {
        $this->factories[$name] = $factory;
        return $this;
    }

    public function get(string $name)
    {
        if (! isset($this->factories[$name]))
        {
            throw new InvalidArgumentException('The helper "' . $name . '" does not exist');
        }

        if (! isset($this->helpers[$name]))
        {
            $this->helpers[$name] = $this->factories[$name]();
        }

        return $this->helpers[$name];
    }

    public function has(string $name): bool
    {
        return isset($this->factories[$name]);
    }
}