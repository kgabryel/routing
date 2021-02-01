<?php

namespace Frankie\Routing;

interface ResourceRouteInterface extends RouteInterface
{

    public function getResourceName(): string;
}
