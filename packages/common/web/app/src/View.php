<?php

namespace Web;

use Symfony\Component\Routing\Generator\UrlGenerator;

class View implements ViewFactory
{
    private $data = [];
    private $filePath;
    private $templateDir;
    private $generator;

    public function __construct(UrlGenerator $generator, string $templateDir)
    {
        $this->generator = $generator;
        $this->templateDir = $templateDir;
    }

    public function render(): string
    {
        if (empty($this->filePath)) {
            throw new \RuntimeException('No loaded file.');
        }

        ob_start();
        require($this->filePath);

        return ob_get_clean();
    }

    public function set(string $paramName, $value)
    {
        $this->data[$paramName] = $value;
    }

    public function has(string $paramName): bool
    {
        return isset($this->data[$paramName]);
    }

    public function get(string $paramName, $default = '< no data >')
    {
        return $this->data[$paramName] ?? $default;
    }

    public function load(string $fileName): self
    {
        $view = clone $this;
        $view->filePath = $this->templateDir . '/' . $fileName . '.php';
        if (!file_exists($view->filePath)) {
            throw new \RuntimeException("No file exists: {$view->filePath}");
        }

        return $view;
    }

    public function url(string $pathName, array $params = []): string
    {
        return $this->generator->generate($pathName, $params);
    }
}
