<?php

namespace Andrijaj\DemoProject\Framework;

/**
 * Class Request
 * @package Andrijaj\DemoProject\Framework
 *
 * A class to make manipulation with HTTP Requests easier
 */
class Request
{
    public const HTTP_METHODS = ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'];
    private string $method;
    private array $headers;
    private array $body;
    private string $uri;
    private array $queryParameters;
    private array $files;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = $this->constructHeaders();
        $this->body = $this->constructBody();
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->queryParameters = $this->constructQueryParameters();
        $this->files = $_FILES;
    }

    /**
     * Returns the HTTP Request's method.
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the value of the specified header.
     * @param string $header
     * @return string
     */
    public function getHeader(string $header): string
    {
        return $this->headers[$header];
    }

    /**
     * Returns the HTTP Request's headers in the form of an associative array.
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Returns the Request body in the form of an associative array.
     * If the body isn't specified (in case of GET request), then
     * the method returns an empty array.
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Returns the value of the specified body parameter.
     * @param string $parameter
     * @return mixed
     */
    public function getBodyParameter(string $parameter)
    {
        return $this->body[$parameter];
    }

    /**
     * Returns the URI of the request.
     * @return string
     */
    public function getURI(): string
    {
        return $this->uri;
    }

    /**
     * Returns the query parameters contained in the request URI.
     * @return array
     */
    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    /**
     * Returns the contents of $_FILES.
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Returns the contents of the $_FILES[$parameter]
     * @param string $parameter
     * @return array
     */
    public function getFilesByParameter(string $parameter): array
    {
        return $this->files[$parameter];
    }

    /**
     * Constructs and returns an array which represents the HTTP
     * request's headers.
     * HTTP headers are located in the $_SERVER super-global
     * as key => value pairs, where the key starts with HTTP_.
     * First the method matches all keys that start with HTTP_ and the header name
     * will be everything afterwards. This name is going to be in the form
     * of WORDS_SEPARATED_BY_DASHES and this function converts it to lowerCamelCase.
     * @return array
     */
    private function constructHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (preg_match('/^HTTP_(.*)/', $key, $matches)) {
                $headerName = strtolower($matches[1]);
                $headerName = ucwords($headerName, '_');
                $headerName = lcfirst($headerName);
                $headerName = str_replace("_", "", $headerName);
                $headers[$headerName] = $value;
            }
        }

        return $headers;
    }

    /**
     * Constructs and returns an array which represents the body of the request.
     * If the request doesn't have a body (i.e. GET request) then
     * it returns an empty array.
     * @return array
     */
    private function constructBody(): array
    {
        $body = $_POST;
        if (isset($this->headers['contentType']) && $this->headers['contentType'] === 'application/json') {
            $body = json_decode(file_get_contents('php://input'), true);
        }

        foreach ($body as $key => $value) {
            if (is_string($value)) {
                $body[$key] = trim($value);
            }
        }

        return $body;
    }

    /**
     * Constructs and returns an array which represents the query parameters
     * of the request. If the request doesn't have a body (i.e. GET request)
     * then it returns an empty array.
     * @return array
     */
    private function constructQueryParameters(): array
    {
        return $_GET;
    }
}