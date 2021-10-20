<?php

namespace Andrijaj\DemoProject\Framework\Responses;

class HTMLResponse extends Response
{
    private const DEFAULT_VIEW_PATH = '../resources/views/';
    private const DEFAULT_LAYOUT = 'emptyLayout';
    private string $view;
    private array $variables;
    private string $layout;

    public function __construct(string $view, array $variables = [], int $status = 200)
    {
        $this->view = $view;
        $this->headers = [];
        $this->status = $status;
        $this->variables = $variables;
        $this->layout = self::DEFAULT_LAYOUT;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        $this->variables['view'] = $this->view;
        extract($this->variables);
        ob_start();
        include self::DEFAULT_VIEW_PATH . $this->layout . '.php';

        return ob_get_clean();
    }

    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }
}