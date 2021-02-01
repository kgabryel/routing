<?php

namespace Frankie\Routing\Managers;

use Ds\Collection;
use Ds\Queue;
use Frankie\Routing\Parsers\ResourceRouteParserInterface;
use Frankie\Routing\ResourceRouteInterface;
use Frankie\Routing\Validators\Validator;
use OutOfBoundsException;
use OutOfRangeException;

final class ResourceRouteManager implements ResourceRouteManagerInterface
{
    private Collection $params;
    private Collection $keys;
    private Validator $validator;
    private ResourceRouteParserInterface $parser;
    private array $tmpParam;
    private string $tmpKey;
    private Collection $routes;

    public function __construct(
        Validator $validator, ResourceRouteParserInterface $parser, ?array $params
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
        $this->routes = new Queue();
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
        $this->routes = $this->parser->set($this->tmpParam)
            ->create()
            ->get();
        return $this;
    }

    /**
     * @return ResourceRouteInterface
     */
    public function get(): ResourceRouteInterface
    {
        if ($this->routes->isEmpty()) {
            throw new OutOfRangeException('Parsed route queue is empty.');
        }
        return $this->routes->pop();
    }

    public function hasRoute(): bool
    {
        return !$this->routes->isEmpty();
    }
}
