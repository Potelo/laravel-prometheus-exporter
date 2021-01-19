<?php

namespace Superbalist\LaravelPrometheusExporter;

use InvalidArgumentException;
use Prometheus\Storage\Adapter;
use Prometheus\Storage\APC;
use Prometheus\Storage\InMemory;
use Prometheus\Storage\Redis;
use Predis\Client;

class StorageAdapterFactory
{
    /**
     * Factory a storage adapter.
     *
     * @param string $driver
     * @param array $config
     *
     * @return Adapter
     */
    public function make($driver, array $config = [])
    {
        switch ($driver) {
            case 'memory':
                return new InMemory();
            case 'redis':
                return $this->makeRedisAdapter($config);
            case 'apc':
                return new APC();
        }

        throw new InvalidArgumentException(sprintf('The driver [%s] is not supported.', $driver));
    }

    /**
     * Factory a redis storage adapter.
     *
     * @param array $config
     *
     * @return Redis
     */
    protected function makeRedisAdapter(array $config)
    {
        $redis = Redis::usingPredis(new Client(['host' => config('prometheus.storage_adapters.redis.host'), 'database' => config('prometheus.storage_adapters.redis.database')]));

        if (isset($config['prefix'])) {
            $redis->setPrefix($config['prefix']);
        }

        return $redis;
    }
}
