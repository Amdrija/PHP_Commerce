<?php

namespace Andrijaj\DemoProject\Framework\Responses;

class JSONResponse extends Response
{
    private array $array;

    public function __construct(array $array)
    {
        $this->array = $array;
        $this->status = 200;
    }

    public static function buildErrorJSONResponse(string $errorMessage, int $status)
    {
        $response = new JSONResponse(['errorMessage' => $errorMessage]);
        $response->setStatus($status);

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return json_encode($this->array);
    }
}