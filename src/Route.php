<?php

namespace Frankie\Routing;

use Ds\Queue;
use Ds\Sequence;
use Ds\Vector;
use Frankie\Request\RequestInterface;
use Frankie\Routing\Parts\PartFactory;
use Frankie\Routing\Parts\RoutePart;

class Route implements RouteInterface
{
    protected string $path;
    protected Sequence $methods;
    protected string $controllerName;
    protected string $action;
    protected Sequence $routeParts;
    protected Sequence $middleware;
    protected Sequence $after;
    protected ?string $defaultFormatType;

    public function __construct(
        string $path, Sequence $methods, string $controllerName, string $action,
        ?Sequence $middleware = null, ?Sequence $after = null, ?string $defaultFormatType = null
    )
    {
        $this->path = trim($path, '/');
        $this->methods = $methods;
        $this->controllerName = $controllerName;
        $this->action = $action;
        $this->routeParts = new Vector();
        $this->generateParts();
        $this->middleware = $middleware ?? new Vector();
        $this->after = $after ?? new Vector();
        $this->defaultFormatType = $defaultFormatType;
    }

    private function generateParts(): void
    {
        $parts = explode('/', $this->path);
        foreach ($parts as $part) {
            $this->routeParts->push(PartFactory::createPart($part));
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethods(): Sequence
    {
        return $this->methods;
    }

    public function getController(): string
    {
        return $this->controllerName;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getParts(): Sequence
    {
        return $this->routeParts;
    }

    public function getMiddleware(): Sequence
    {
        return $this->middleware;
    }

    public function getAfter(): Sequence
    {
        return $this->after;
    }

    public function getDataFormat(): ?string
    {
        return $this->defaultFormatType;
    }

    public function compare(Queue $requestPart, RequestInterface $request, Queue $params): bool
    {
        $params->clear();
        if (!$this->check($request)) {
            return false;
        }
        foreach ($this->routeParts as $part) {
            if (!$this->checkPart($requestPart, $params, $part)) {
                return false;
            }
        }
        return true;
    }

    private function checkMethod(string $requestMethod): bool
    {
        foreach ($this->methods as $method) {
            if ($requestMethod === $method) {
                return true;
            }
        }
        return false;
    }

    protected function check(RequestInterface $request): bool
    {
        if (
        !$this->checkMethod(
            $request->getHttpInfo()
                ->getHttpMethod()
        )
        ) {
            return false;
        }
        return $this->routeParts->count() === \count(
                $request->getPathInfo()
                    ->getURIParts()
            );
    }

    protected function checkPart(Queue $partsRequest, Queue $params, RoutePart $part): bool
    {
        if ($partsRequest->isEmpty()) {
            $params->clear();
            return false;
        }
        if (!$part->compare($params, $partsRequest->pop())) {
            $params->clear();
            return false;
        }
        return true;
    }
}
