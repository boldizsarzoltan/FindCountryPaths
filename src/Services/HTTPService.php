<?php

namespace App\Services;

use App\Services\Exceptions\RequestFailedException;
use GuzzleHttp\Client;

class HTTPService
{
    public function __construct(
        private readonly Client $client,
        private readonly string $dataUrl
    )
    {
    }

    public function getData(): array
    {
        try {
            $response = $this->client->get($this->dataUrl);
            if($response->getStatusCode() != 200) {
                throw new RequestFailedException();
            }
            return json_decode($response->getBody()->getContents(), true);
        }
        catch (\Throwable $throwable) {
            throw new RequestFailedException();
        }
    }
}