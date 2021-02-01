<?php

namespace Frankie\Routing\Managers;

use Ds\Collection;
use Ds\Queue;
use Frankie\Routing\Parsers\RouteParserInterface;
use Frankie\Routing\Route;
use Frankie\Routing\RouteInterface;
use Frankie\Routing\Validators\Validator;
use OutOfBoundsException;
use OutOfRangeException;

final class RouteManager implements RouteManagerInterface
{
    private Collection $params;
    private Collection $keys;
    private Validator $validator;
    private RouteParserInterface $parser;
    private array $tmpParam;
    private string $tmpKey;
    private Route $route;

    public function __construct(
        Validator $validator, RouteParserInterface $parser, ?array $params
    )
    {
        if ($params === null) {
            $params = [];
        }
        $this->params = new Queue();
        $this->keys = new Queue();
        foreach ($params as $key => $val) {
            $this->params->push($val);
            $this->keys->push($key);
        }
        $this->validator = $validator;
        $this->parser = $parser;
    }

    public function isEmpty(): bool
    {
        return $this->params->isEmpty();
    }

    public function take(): self
    {
        if ($this->params->isEmpty() || $this->keys->isEmpty()) {
            throw new OutOfRangeException('Routing queue is empty.');
        }
        $this->tmpParam = $this->params->pop();
        $this->tmpKey = $this->keys->pop();
        return $this;
    }

    public function validate(): self
    {
        if ($this->tmpParam === null || $this->tmpKey === null) {
            throw new OutOfBoundsException('Temp routing is empty.');
        }
        $this->validator->set($this->tmpKey, $this->tmpParam)
            ->validate();
        return $this;
    }

    public function parse(): self
    {
        if ($this->tmpParam === null || $this->tmpKey === null) {
            throw new OutOfBoundsException('Temp routing is empty.');
        }
        $this->route = $this->parser->set($this->tmpParam)
            ->create()
            ->get();
        return $this;
    }

    public function get(): RouteInterface
    {
        if ($this->route === null) {
            throw new OutOfBoundsException('Parser routing is empty.');
        }
        return $this->route;
    }
}
