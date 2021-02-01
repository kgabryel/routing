<?php

namespace Frankie\Routing;

use Ds\Queue;
use Ds\Sequence;
use Frankie\Request\RequestInterface;

interface RouteInterface
{
    public function getPath(): string;

    public function getMethods(): Sequence;

    public function getController(): string;

    public function getAction(): string;

    public function getParts(): Sequence;

    public function getMiddleware(): Sequence;

    public function getAfter(): Sequence;

    public function getDataFormat(): ?string;

    public function compare(Queue $requestPart, RequestInterface $request, Queue $params): bool;
}
