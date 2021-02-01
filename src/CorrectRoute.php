<?php

namespace Frankie\Routing;

use Ds\Queue;
use Ds\Sequence;

class CorrectRoute
{
    protected ?RouteInterface $route;
    protected ?Sequence $params;
    protected ?string $responseMimeType;

    public function __construct(RouteInterface $route, ?Queue $params)
    {
        $this->route = $route;
        $this->params = $params;
        $this->responseMimeType = null;
    }

    public function getControllerName(): string
    {
        return $this->route->getController();
    }

    public function getActionName(): string
    {
        return $this->route->getAction();
    }

    public function getDataFormat(): ?string
    {
        return $this->route->getDataFormat();
    }

    public function getMiddleware(): Sequence
    {
        return $this->route->getMiddleware();
    }

    public function getAfter(): Sequence
    {
        return $this->route->getAfter();
    }

    public function getParams(): Queue
    {
        return $this->params ?? new Queue();
    }

    public function getResponseMimeType(): ?string
    {
        return $this->responseMimeType;
    }

    public function setDataType(string $dataType): void
    {
        $this->responseMimeType = $dataType;
    }
}
