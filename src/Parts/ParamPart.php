<?php

namespace Frankie\Routing\Parts;

use Ds\Queue;
use InvalidArgumentException;

final class ParamPart implements RoutePart
{
    private string $part;

    public function __construct(string $part)
    {
        $this->part = $part;
        $this->checkFormat();
    }

    public function compare(Queue $map, string $requestPart): bool
    {
        $map->push($requestPart);
        return true;
    }

    private function checkFormat(): void
    {
        if (!preg_match('/^[_a-zA-Z]+[_a-zA-Z0-9]*$/', $this->part)) {
            throw new InvalidArgumentException("Invalid format ({$this->part}).");
        }
    }
}
