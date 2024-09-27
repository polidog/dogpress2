<?php

namespace Polidog\DogPress;

class Config
{
    private string $templatePath = __DIR__ . '/../templates';

    public function  setTemplatePath(string $templatePath): void
    {
        $this->templatePath = $templatePath;
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }
}