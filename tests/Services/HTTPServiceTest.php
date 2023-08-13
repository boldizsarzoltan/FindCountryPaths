<?php

namespace App\Tests\Services;


use App\Services\Exceptions\RequestFailedException;
use App\Services\FileSystemService;
use App\Services\HTTPService;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;


class HTTPServiceTest extends KernelTestCase
{
    private Filesystem|\PHPUnit\Framework\MockObject\MockObject $client;

    protected function setUp(): void
    {
        $_ENV["DATA_URL"] = "test_url";
        parent::setUp();
        $this->client = $this->createMock(Client::class);
        self::bootKernel();
    }

    public function test_getData_worksCorrectlyIfRequestFailed()
    {
        $this->expectException(RequestFailedException::class);
        $container = static::getContainer();
        $container->set(Client::class, $this->client);
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects(self::once())
            ->method("getStatusCode")
            ->willReturn(300);
        $this->client
            ->expects(self::once())
            ->method("get")
            ->with("test_url")
            ->willReturn($response);
        $container->get(HTTPService::class)->getData();
    }

    public function test_getData_worksCorrectlyIfRequestWorks()
    {
        $container = static::getContainer();
        $container->set(Client::class, $this->client);
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects(self::once())
            ->method("getStatusCode")
            ->willReturn(200);
        $streamInterface = $this->createMock(StreamInterface::class);
        $contents = json_encode(["contests"]);
        $streamInterface
            ->expects(self::once())
            ->method("getContents")
            ->willReturn($contents);
        $response
            ->expects(self::once())
            ->method("getBody")
            ->willReturn($streamInterface);
        $this->client
            ->expects(self::once())
            ->method("get")
            ->with("test_url")
            ->willReturn($response);
        $this->assertEquals(
            ["contests"],
            $container->get(HTTPService::class)->getData()
        );
    }
}
