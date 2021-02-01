<?php

namespace Frankie\Routing\Parsers;

use Ds\Sequence;
use Ds\Vector;
use Frankie\Routing\Route;

final class RouteParser implements RouteParserInterface
{
    private const PATH_KEY = 'path';
    private const MIDDLEWARE_KEY = 'middleware';
    private const AFTER_KEY = 'after';
    private const METHODS_KEY = 'methods';
    private const CONTROLLER_KEY = 'controller';
    private const ACTION_KEY = 'action';
    private const DATA_TYPE_KEY = 'dataType';
    private array $route;
    private Sequence $methods;
    private Sequence $middleware;
    private Sequence $after;
    private ?string $dataType;

    public function create(): self
    {
        $this->createMethods();
        $this->createMiddleware();
        $this->createAfter();
        $this->createDataType();
        return $this;
    }

    public function get(): Route
    {
        return new Route(
            $this->route[self::PATH_KEY],
            $this->methods,
            $this->route[self::CONTROLLER_KEY],
            $this->route[self::ACTION_KEY],
            $this->middleware,
            $this->after,
            $this->dataType
        );
    }

    public function createDataType(): void
    {
        $this->dataType = $this->route[self::DATA_TYPE_KEY] ?? null;
    }

    private function createMethods(): void
    {
        if (\is_array($this->route[self::METHODS_KEY])) {
            foreach ($this->route[self::METHODS_KEY] as $method) {
                $this->methods->push(strtoupper($method));
            }
        } else {
            $this->methods->push(strtoupper($this->route[self::METHODS_KEY]));
        }
    }

    private function createMiddleware(): void
    {
        if (!isset($this->route[self::MIDDLEWARE_KEY])) {
            return;
        }
        $this->middleware = new Vector();
        if (\is_array($this->route[self::MIDDLEWARE_KEY])) {
            foreach ($this->route[self::MIDDLEWARE_KEY] as $method) {
                $this->middleware->push($method);
            }
        } else {
            $this->middleware->push($this->route[self::MIDDLEWARE_KEY]);
        }
    }

    private function createAfter(): void
    {
        $this->after = new Vector();
        if (!isset($this->route[self::AFTER_KEY])) {
            return;
        }
        $this->after = new Vector();
        if (\is_array($this->route[self::AFTER_KEY])) {
            foreach ($this->route[self::AFTER_KEY] as $method) {
                $this->after->push($method);
            }
        } else {
            $this->after->push($this->route[self::AFTER_KEY]);
        }
    }

    public function set(array $params): self
    {
        $this->methods = new Vector();
        $this->route = $params;
        return $this;
    }
}
