<?php

namespace App\Support;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Http;

class UpstashRestStore implements Store
{
    private string $url;
    private string $token;
    private string $prefix;

    public function __construct(string $prefix = '')
    {
        $this->url = config('services.upstash.url', '');
        $this->token = config('services.upstash.token', '');
        $this->prefix = $prefix;
    }

    public function get($key)
    {
        $response = $this->http()->get($this->url . "/get/{$this->prefix}{$key}");

        if ($response->failed()) {
            return null;
        }

        $result = $response->json('result');
        if ($result === null || $result === false) {
            return null;
        }

        return @unserialize($result) ?? $result;
    }

    public function many(array $keys)
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }
        return $results;
    }

    public function put($key, $value, $seconds)
    {
        $serialized = serialize($value);

        if ($seconds > 0) {
            $response = $this->http()
                ->withBody($serialized, 'text/plain')
                ->post($this->url . "/setex/{$this->prefix}{$key}/{$seconds}");
        } else {
            $response = $this->http()
                ->withBody($serialized, 'text/plain')
                ->post($this->url . "/set/{$this->prefix}{$key}");
        }

        return $response->successful();
    }

    public function putMany(array $values, $seconds)
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value, $seconds);
        }
        return true;
    }

    public function increment($key, $value = 1)
    {
        $this->http()->asForm()->post($this->url . "/incrby/{$this->prefix}{$key}/{$value}");
        $result = $this->http()->get($this->url . "/get/{$this->prefix}{$key}");
        return (int) ($result->json('result') ?? 0);
    }

    public function decrement($key, $value = 1)
    {
        $this->http()->asForm()->post($this->url . "/decrby/{$this->prefix}{$key}/{$value}");
        $result = $this->http()->get($this->url . "/get/{$this->prefix}{$key}");
        return (int) ($result->json('result') ?? 0);
    }

    public function forever($key, $value)
    {
        return $this->put($key, $value, 0);
    }

    public function forget($key)
    {
        $this->http()->asForm()->post($this->url . "/del/{$this->prefix}{$key}");
        return true;
    }

    public function flush()
    {
        $cursor = '0';
        do {
            $response = $this->http()->asForm()->post($this->url . "/scan/{$cursor}/MATCH/{$this->prefix}*/COUNT/100");
            $data = $response->json();
            $cursor = $data['result'][0] ?? '0';
            $keys = $data['result'][1] ?? [];

            foreach ($keys as $key) {
                $this->http()->asForm()->post($this->url . "/del/{$key}");
            }
        } while ($cursor !== '0');

        return true;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    private function http()
    {
        return Http::withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->timeout(5);
    }
}