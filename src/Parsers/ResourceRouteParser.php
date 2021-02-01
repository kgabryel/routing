<?php

namespace Frankie\Routing\Parsers;

use Ds\Collection;
use Ds\Queue;
use Ds\Vector;
use Frankie\Routing\DataProviderInterface;
use Frankie\Routing\ResourceRoute;
use InvalidArgumentException;

final class ResourceRouteParser implements ResourceRouteParserInterface
{
    private const RESOURCE_KEY = 'resource';
    private const CONTROLLER_KEY = 'controller';
    private const ACTIONS_KEY = 'actions';
    private const BASE_PATH_KEY = 'basePath';
    private const MIDDLEWARE_KEY = 'middleware';
    private const AFTER_KEY = 'after';
    private const PATH_KEY = 'path';
    private const DATA_TYPE_KEY = 'dataType';
    private const METHOD_KEY = 'method';
    private Collection $routes;
    private array $params;
    private DataProviderInterface $dataProvider;

    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
        $this->routes = new Queue();
    }

    public function setDataProvider(DataProviderInterface $dataProvider): self
    {
        $this->dataProvider = $dataProvider;
        return $this;
    }

    public function set(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    public function create(): self
    {
        if (!\is_array($this->params[self::ACTIONS_KEY])) {
            $this->params[self::ACTIONS_KEY] = [$this->params[self::ACTIONS_KEY]];
        }
        foreach ($this->params[self::ACTIONS_KEY] as $action) {
            if (!\array_key_exists($action, $this->dataProvider::get())) {
                throw new InvalidArgumentException("Key $action not found.");
            }
            $methods = new Vector();
            if (\is_array($this->dataProvider::get()[$action][self::METHOD_KEY])) {
                foreach ($this->dataProvider::get()[$action][self::METHOD_KEY] as $value) {
                    $methods->push($value);
                }
            } else {
                $methods->push($this->dataProvider::get()[$action][self::METHOD_KEY]);
            }
            $middleware = $this->createMiddleware($this->params, $action);
            $after = $this->createAfter($this->params, $action);
            $dataFormat = $this->params[$action][self::DATA_TYPE_KEY] ?? $this->params[self::DATA_TYPE_KEY] ?? null;
            $this->routes->push(
                new ResourceRoute(
                    $this->params[self::RESOURCE_KEY],
                    $this->params[self::BASE_PATH_KEY] . $this->dataProvider::get(
                    )[$action][self::PATH_KEY],
                    $methods,
                    $this->params[self::CONTROLLER_KEY],
                    $this->dataProvider::get()[$action]['action'],
                    $middleware,
                    $after,
                    $dataFormat
                )
            );
        }
        return $this;
    }

    public function get(): Collection
    {
        return $this->routes;
    }

    private function createMiddleware(array $route, string $action): ?Vector
    {
        $middleware = $this->middlewareFromResource($route);
        return $this->middlewareFromAction($route, $action, $middleware);
    }

    private function createAfter(array $route, string $action): ?Vector
    {
        $middleware = $this->afterFromResource($route);
        return $this->afterFromAction($route, $action, $middleware);
    }

    private function middlewareFromResource(array $route): ?Vector
    {
        if (!isset($route[self::MIDDLEWARE_KEY])) {
            return null;
        }
        $middleware = new Vector();
        if (!\is_array($route[self::MIDDLEWARE_KEY])) {
            $middleware->push($route[self::MIDDLEWARE_KEY]);
            return $middleware;
        }
        foreach ($route[self::MIDDLEWARE_KEY] as $value) {
            $middleware->push($value);
        }
        return $middleware;
    }

    private function afterFromResource(array $route): ?Vector
    {
        if (!isset($route[self::AFTER_KEY])) {
            return null;
        }
        $after = new Vector();
        if (!\is_array($route[self::AFTER_KEY])) {
            $after->push($route[self::AFTER_KEY]);
            return $after;
        }
        foreach ($route[self::AFTER_KEY] as $value) {
            $after->push($value);
        }
        return $after;
    }

    private function middlewareFromAction(array $route, string $action, ?Vector $middleware
    ): ?Vector
    {
        if (!isset($route[$action][self::MIDDLEWARE_KEY])) {
            return $middleware;
        }
        if ($middleware === null) {
            $middleware = new Vector();
        }
        if (!\is_array($route[$action][self::MIDDLEWARE_KEY])) {
            $middleware->push($route[$action][self::MIDDLEWARE_KEY]);
            return $middleware;
        }
        foreach ($route[$action][self::MIDDLEWARE_KEY] as $value) {
            $middleware->push($value);
        }
        return $middleware;
    }

    /**
     * @param array $route
     * @param string $action
     * @param Vector|null $after
     *
     * @return Vector|null
     */
    private function afterFromAction(array $route, string $action, ?Vector $after): ?Vector
    {
        if (!isset($route[$action][self::AFTER_KEY])) {
            return $after;
        }
        if ($after === null) {
            $after = new Vector();
        }
        if (!\is_array($route[$action][self::AFTER_KEY])) {
            $after->push($route[$action][self::AFTER_KEY]);
            return $after;
        }
        foreach ($route[$action][self::AFTER_KEY] as $value) {
            $after->push($value);
        }
        return $after;
    }
}
