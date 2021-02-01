<?php

namespace Frankie\Routing\Parts;

use Ds\Queue;

interface RoutePart
{
    public function __construct(string $part);

    public function compare(Queue $map, string $requestPart): bool;
}
