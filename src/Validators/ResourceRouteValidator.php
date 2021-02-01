<?php

namespace Frankie\Routing\Validators;

use InvalidArgumentException;

final class ResourceRouteValidator implements Validator
{
    private const CONTROLLER_KEY = 'controller';
    private const ACTIONS_KEY = 'actions';
    private const BASE_PATH_KEY = 'basePath';
    private const RESOURCE_KEY = 'resource';
    private array $params;
    private string $key;

    public function set(string $key, array $params): Validator
    {
        $this->key = $key;
        $this->params = $params;
        return $this;
    }

    public function validate(): void
    {
        if ($this->key === null || $this->params === null) {
            throw new InvalidArgumentException('Key or params not found.');
        }
        $this->checkRequired();
        $this->checkWhiteSpace();
    }

    private function checkRequired(): void
    {
        $keys = [
            self::CONTROLLER_KEY,
            self::BASE_PATH_KEY,
            self::ACTIONS_KEY,
            self::RESOURCE_KEY
        ];
        foreach ($keys as $val) {
            if (!isset($this->params[$val])) {
                throw new InvalidArgumentException("$val not exists in $this->key.");
            }
        }
        if (!class_exists($this->params[self::RESOURCE_KEY])) {
            throw new InvalidArgumentException(
                'Resource ' . $this->params[self::RESOURCE_KEY] . " doesn't exists."
            );
        }
    }

    private function checkWhiteSpace(): void
    {
        $keys = [
            self::CONTROLLER_KEY,
            self::BASE_PATH_KEY
        ];
        foreach ($keys as $val) {
            if (preg_match('/\s/', $this->params[$val])) {
                throw new InvalidArgumentException("$val has invalid value in $this->key.");
            }
        }
    }
}
