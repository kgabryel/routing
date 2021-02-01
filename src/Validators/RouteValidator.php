<?php

namespace Frankie\Routing\Validators;

use InvalidArgumentException;
use OutOfBoundsException;

final class RouteValidator implements Validator
{
    private const PATH_KEY = 'path';
    private const METHODS_KEY = 'methods';
    private const CONTROLLER_KEY = 'controller';
    private const ACTION_KEY = 'action';
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
            self::PATH_KEY,
            self::METHODS_KEY,
            self::CONTROLLER_KEY,
            self::ACTION_KEY
        ];
        foreach ($keys as $val) {
            if (!isset($this->params[$val])) {
                throw new OutOfBoundsException("$val not exists in $this->key.");
            }
        }
    }

    private function checkWhiteSpace(): void
    {
        $keys = [
            self::PATH_KEY,
            self::CONTROLLER_KEY,
            self::ACTION_KEY
        ];
        foreach ($keys as $val) {
            if (preg_match('/\s/', $this->params[$val])) {
                throw new InvalidArgumentException("$val has invalid value in $this->key.");
            }
        }
        if (\is_array($this->params[self::METHODS_KEY])) {
            foreach ($this->params[self::METHODS_KEY] as $key => $val) {
                if (preg_match('/\s/', $this->params[self::METHODS_KEY][$key])) {
                    throw new InvalidArgumentException(
                        "Methods $key has invalid value in $this->key."
                    );
                }
            }
        } elseif (preg_match('/\s/', $this->params[self::METHODS_KEY])) {
            throw new InvalidArgumentException("Methods has invalid value in $this->key.");
        }
    }
}
