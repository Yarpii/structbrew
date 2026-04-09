<?php
declare(strict_types=1);
namespace App\Core;

use Throwable;

abstract class Controller
{
    protected Request $request;
    protected ?Response $response = null;
    public function __construct(?Request $request = null)
    {
        $this->request = $request ?? Request::fromGlobals();
    }

    /**
     * @throws Throwable
     */
    protected function view(string $name, array $data = [], int $status = 200): Response
    {
        $html = View::render($name, $data);
        return Response::html($html, $status);
    }
    protected function json(array|object $data, int $status = 200): Response
    {
        $this->response = Response::json($data, $status);
        return $this->response;
    }
    protected function text(string $text, int $status = 200): Response
    {
        $this->response = Response::text($text, $status);
        return $this->response;
    }
    protected function redirect(string $url, int $status = 302): Response
    {
        $this->response = Response::redirect($url, $status);
        return $this->response;
    }
    protected function input(string $key, mixed $default = null): mixed
    {
        return $this->request->input($key, $default);
    }
    protected function header(string $name, mixed $default = null): mixed
    {
        return $this->request->header($name, $default);
    }
    public function getResponse(): ?Response
    {
        return $this->response;
    }
}