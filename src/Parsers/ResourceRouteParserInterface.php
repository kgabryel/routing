<?php

namespace Frankie\Routing\Parsers;

use Ds\Collection;
use Frankie\Routing\DataProviderInterface;

interface ResourceRouteParserInterface
{
    public function __construct(DataProviderInterface $parser);

    public function setDataProvider(DataProviderInterface $dataProvider): self;

    public function set(array $params): self;

    public function create(): self;

    public function get(): Collection;
}
