<?php

namespace Frankie\Routing\Managers;

use Frankie\Routing\Parsers\RouteParserInterface;
use Frankie\Routing\RouteInterface;
use Frankie\Routing\Validators\Validator;

interface RouteManagerInterface
{
    public function __construct(
        Validator $validator, RouteParserInterface $parser, ?array $params
    );

    public function isEmpty(): bool;

    public function take(): self;

    public function validate(): self;

    public function parse(): self;

    public function get(): RouteInterface;
}
