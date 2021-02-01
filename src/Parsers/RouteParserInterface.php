<?php

namespace Frankie\Routing\Parsers;

use Frankie\Routing\Route;

interface RouteParserInterface
{
    public function set(array $params): self;

    public function create(): self;

    public function get(): Route;
}
