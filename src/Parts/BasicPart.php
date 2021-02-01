<?php

namespace Frankie\Routing\Parts;

use Ds\Queue;

final class BasicPart implements RoutePart
{
    private string $part;

    public function __construct(string $part)
    {
        $this->part = $part;
    }

    public function compare(Queue $map, string $requestPart): bool
    {
        return $this->part === $requestPart;
    }
}
