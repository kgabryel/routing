<?php

namespace Frankie\Routing;

use Ds\Sequence;

class ResourceRoute extends Route implements ResourceRouteInterface
{
    protected string $resourceName;

    public function __construct(
        string $resourceName, string $path, Sequence $methods, string $controllerName,
        string $action, ?Sequence $middleware = null, ?Sequence $after = null,
        ?string $defaultFormatType = null
    )
    {
        parent::__construct(
            $path,
            $methods,
            $controllerName,
            $action,
            $middleware,
            $after,
            $defaultFormatType
        );
        $this->resourceName = $resourceName;
    }

    public function getResourceName(): string
    {
        return $this->resourceName;
    }
}
