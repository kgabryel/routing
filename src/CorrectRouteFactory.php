<?php

namespace Frankie\Routing;

use Ds\Collection;
use Ds\Queue;
use Frankie\Request\RequestInterface;

final class CorrectRouteFactory
{
    private Collection $customRoutes;
    private Collection $resourceRoutes;
    private RequestInterface $request;
    private RouteInterface $correctRoute;
    private Collection $routeParams;

    public function __construct(
        Collection $customRoutes, Collection $resourceRoutes, RequestInterface $request
    )
    {
        $this->customRoutes = $customRoutes;
        $this->resourceRoutes = $resourceRoutes;
        $this->request = $request;
        $this->routeParams = new Queue();
    }

    public function find(): self
    {
        /** @var Route $route */
        foreach ($this->resourceRoutes as $route) {
            if (
            $route->compare(
                new Queue(
                    $this->request->getPathInfo()
                        ->getURIParts()
                ),
                clone $this->request,
                $this->routeParams
            )
            ) {
                $this->correctRoute = $route;
                break;
            }
        }
        if ($this->correctRoute !== null) {
            return $this;
        }
        /** @var RouteInterface $route */
        foreach ($this->customRoutes as $route) {
            if (
            $route->compare(
                new Queue(
                    $this->request->getPathInfo()
                        ->getURIParts()
                ),
                clone $this->request,
                $this->routeParams
            )
            ) {
                $this->correctRoute = $route;
                break;
            }
        }
        return $this;
    }

    public function get(): ?CorrectRoute
    {
        if ($this->correctRoute === null) {
            return null;
        }
        return new CorrectRoute($this->correctRoute, $this->routeParams);
    }

    public static function compareMimeType(string $mimeType, string $mimeType2): bool
    {
        if ($mimeType === $mimeType2) {
            return true;
        }
        $tmp = explode('/', $mimeType);
        $tmp[0] = $tmp[0] ?? '';
        $tmp[1] = $tmp[1] ?? '';
        $tmp2 = explode('/', $mimeType2);
        $tmp2[0] = $tmp2[0] ?? '';
        $tmp2[1] = $tmp2[1] ?? '';
        if ($tmp[1] === '*' && $tmp[0] === $tmp2[0]) {
            return true;
        }
        if ($tmp[0] === '*' && $tmp[1] === '*') {
            return true;
        }
        return ($tmp[0] === $tmp2[0]) && strrpos($tmp[1], '+') && substr(
                $tmp[1],
                strrpos($tmp[1], '+') + 1
            ) === $tmp2[1];
    }
}
