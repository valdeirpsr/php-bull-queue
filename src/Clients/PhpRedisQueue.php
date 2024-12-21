<?php

declare(strict_types=1);

namespace Ilzrv\PhpBullQueue\Clients;

use Ilzrv\PhpBullQueue\DTOs\RedisConfig;
use Redis;

class PhpRedisQueue implements RedisQueue
{
    protected Redis $client;

    public function __construct(RedisConfig $config, ?Redis $redis = null)
    {
        if (!is_null($redis)) {
            $this->client = $redis;
        } else {
            $this->client = new Redis();

            $this->client->connect($config->host, $config->port);

            $auth = [];

            if ($config->username && $config->password) {
                $auth = [
                    'user' => $config->username,
                    'pass' => $config->password,
                ];
            } elseif ($config->password) {
                $auth['pass'] = $config->password;
            }

            if (!empty($auth)) {
                $this->client->auth($auth);
            }
        }
    }

    public function add(string $script, array $args, int $numKeys)
    {

        return $this->client->eval($script, $args, $numKeys);
    }
}
