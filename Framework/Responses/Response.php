<?php

namespace Andrijaj\DemoProject\Framework\Responses;

/**
 * Class Response
 * @package Andrijaj\DemoProject\Framework
 *
 * An interface to make it easier to manipulate HTTP responses.
 */
abstract class Response
{
    protected int $status;
    protected array $headers = [];

    /**
     * Returns the body of the HTTP response.
     * @return string
     */
    public abstract function getContent(): string;

    /**
     * Returns the status code of the HTTP response.
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Sets the status code of the HTTP response.
     * @param int $statusCode
     */
    public function setStatus(int $statusCode)
    {
        $this->status = $statusCode;
    }

    /**
     * Returns all HTTP headers of the HTTP response.
     * @return array
     */
    public function getAllHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Returns the value of the specified HTTP header.
     * @param string $header
     * @return string
     */
    public function getHeader(string $header): string
    {
        return $this->headers[$header];
    }

    /**
     * Sets the value of the specified HTTP header.
     * @param string $header
     * @param string $value
     */
    public function setHeader(string $header, string $value)
    {
        $this->headers[$header] = $value;
    }

    /**
     * Construct the header the response should return.
     */
    public function constructHeader()
    {
        foreach ($this->headers as $header => $value) {
            header($header . ':' . $value);
        }
    }
}