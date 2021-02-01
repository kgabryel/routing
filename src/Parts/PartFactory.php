<?php

namespace Frankie\Routing\Parts;

use InvalidArgumentException;

final class PartFactory
{
    public static function createPart(string $part): RoutePart
    {
        if ($part === '') {
            return new BasicPart($part);
        }
        if (strncmp($part, '{', 1) === 0 && substr($part, -1) === '}') {
            return new ParamPart(substr($part, 1, -1));
        }
        if ($part[0] !== '{' && substr($part, -1) !== '}') {
            return new BasicPart($part);
        }
        throw new InvalidArgumentException("Unknown RoutePart($part).");
    }
}
