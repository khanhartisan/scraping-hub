<?php

namespace App\Concerns;

use App\Utils\Json;
use UnexpectedValueException;

trait Serializable
{
    /**
     * Convert the object to a JSON string representation.
     *
     * @param  int  $flags
     * @return string
     */
    public function toJson(int $flags = 0): string
    {
        return Json::encode($this->toArray(), $flags);
    }

    /**
     * Create an instance from a JSON string representation.
     *
     * @param  string  $json
     * @param  int  $flags
     * @return static
     */
    public static function fromJson(string $json, int $flags = 0): static
    {
        try {
            $data = Json::decode($json, true);
        } catch (UnexpectedValueException $e) {
            throw new \InvalidArgumentException('Invalid JSON data provided: '.$e->getMessage(), 0, $e);
        }

        if (! is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON data provided: decoded value is not an array');
        }

        return static::fromArray($data);
    }
}
