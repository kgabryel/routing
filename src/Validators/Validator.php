<?php

namespace Frankie\Routing\Validators;

interface Validator
{

    public function set(string $key, array $params): self;

    public function validate(): void;
}
