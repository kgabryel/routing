<?php

namespace Frankie\Routing\Managers;

use Frankie\Routing\Parsers\ResourceRouteParserInterface;
use Frankie\Routing\ResourceRouteInterface;
use Frankie\Routing\Validators\Validator;

interface ResourceRouteManagerInterface
{
    public function __construct(
        Validator $validator, ResourceRouteParserInterface $parser, ?array $params
    );

    public function isEmpty(): bool;

    public function take(): self;

    public function validate(): self;

    public function parse(): self;

    public function get(): ResourceRouteInterface;

    public function hasRoute(): bool;
}
