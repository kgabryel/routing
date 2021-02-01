<?php

namespace Frankie\Routing;

use Ds\Queue;
use Frankie\Routing\Managers\RouteManagerInterface;
use Frankie\Routing\Managers\ResourceRouteManagerInterface;
use InvalidArgumentException;

final class Router
{
    /** @var Queue */
    private $customRoutes;

    /** @var Queue */
    private $resourceRoutes;

    /** @var RouteManagerInterface */
    private $customManager;

    /** @var ResourceRouteManagerInterface */
    private $resourceManager;

    /**
     * Router constructor.
     *
     * @param RouteManagerInterface $customRouteManager
     * @param ResourceRouteManagerInterface $resourceRoutesManager
     * @param int $mode
     */
    public function __construct(
        RouteManagerInterface $customRouteManager,
        ResourceRouteManagerInterface $resourceRoutesManager, int $mode = 1
    )
    {
        if (!($mode === 1 || $mode === 2)) {
            throw new InvalidArgumentException('Invalid mode value');
        }
        $this->customRoutes = new Queue();
        $this->resourceRoutes = new Queue();
        $this->customManager = $customRouteManager;
        $this->resourceManager = $resourceRoutesManager;
        if($mode===1){
            $this->createCustomRoutes();
            $this->createResourceRoutes();
        } else {
            $this->createResourceRoutes();
            $this->createCustomRoutes();
        }
    }

    private function createCustomRoutes(): void
    {
        while (!$this->customManager->isEmpty()) {
            $this->customRoutes->push(
                $this->customManager->take()
                    ->validate()
                    ->parse()
                    ->get()
            );
        }
    }

    private function createResourceRoutes(): void
    {
        while (!$this->resourceManager->isEmpty()) {
            $this->resourceManager->take()
                ->validate()
                ->parse();
            while ($this->resourceManager->hasRoute()) {
                $this->resourceRoutes->push(
                    $this->resourceManager->get()
                );
            }
        }
    }

    /**
     * @return Queue
     */
    public function getCustomRoutes(): Queue
    {
        return $this->customRoutes;
    }

    /**
     * @return Queue
     */
    public function getResourceRoutes(): Queue
    {
        return $this->resourceRoutes;
    }
}
