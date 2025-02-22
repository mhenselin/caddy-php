<?php

declare(strict_types=1);

namespace mattvb91\CaddyPhp\Config\Apps\Http\Server\Routes\Handle\Headers;

use mattvb91\CaddyPhp\Exceptions\CaddyClientException;
use mattvb91\CaddyPhp\Interfaces\Arrayable;

/**
 * https://caddyserver.com/docs/modules/http.handlers.headers
 */
class Request implements Arrayable
{
    /**
     * Names of HTTP header fields to delete.
     * @var string[]
     */
    private array $delete = [];

    /**
     * @var array<string, string[]>
     */
    private array $add = [];


    /**
     * @param string[] $values
     * @return $this
     * @throws CaddyClientException
     */
    public function addHeader(string $name, array $values): static
    {
        if (array_key_exists($name, $this->add)) {
            throw new CaddyClientException("header '{$name}' already exists");
        }
        $this->add[$name] = $values;

        return $this;
    }

    /**
     * @param string[] $values
     * @return $this
     * @throws CaddyClientException
     */
    public function setHeader(string $name, array $values): static
    {
        if (array_key_exists($name, $this->add)) {
            $this->add[$name] = $values;
            return $this;
        }
        return $this->addHeader($name, $values);
    }

    public function addDeleteHeader(string $header): static
    {
        $this->delete[] = $header;

        return $this;
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->delete !== []) {
            $array['delete'] = $this->delete;
        }

        if ($this->add !== []) {
            $array['add'] = $this->add;
        }

        return $array;
    }
}
