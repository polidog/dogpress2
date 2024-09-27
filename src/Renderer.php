<?php

namespace Polidog\DogPress;

class Renderer
{
    public function __construct(private readonly string $templatePath)
    {
        file_exists($this->templatePath) || throw new \InvalidArgumentException('template file not found');
    }

    public function render(string $content, array $params = []): string
    {
        ob_start();
        require_once $this->templatePath;
        return ob_get_clean();
    }
}