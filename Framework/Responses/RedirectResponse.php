<?php

namespace Andrijaj\DemoProject\Framework\Responses;

class RedirectResponse extends Response
{
    public function __construct(string $redirectLocation)
    {
        $this->status = 302;
        $this->setHeader('Location', $redirectLocation);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return "Redirecting to page: {$this->getHeader('Location')}";
    }
}