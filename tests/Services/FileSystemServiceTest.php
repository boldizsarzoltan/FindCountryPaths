<?php

namespace App\Tests\Services;


use App\Services\FileSystemService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;


class FileSystemServiceTest extends KernelTestCase
{
    private Filesystem|\PHPUnit\Framework\MockObject\MockObject $filesystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filesystem = $this->createMock(Filesystem::class);
        self::bootKernel();
    }

    public function test_fileExists()
    {
        $_ENV["JSON_STORAGE_FILE_PATH"] = "test_file_name";
        $container = static::getContainer();
        $container->set(Filesystem::class, $this->filesystem);
        $result = false;
        $this->filesystem
            ->expects(self::once())
            ->method("exists")
            ->with("test_file_name")
            ->willReturn($result);
        $this->assertEquals(
            $result,
            $container->get(FileSystemService::class)->fileExists()
        );
    }

    public function test_canBeCreated()
    {
        $_ENV["JSON_STORAGE_FILE_PATH"] = "./parent/test_file_name";
        $container = static::getContainer();
        $container->set(Filesystem::class, $this->filesystem);
        $result = false;
        $this->filesystem
            ->expects(self::once())
            ->method("exists")
            ->with("./parent")
            ->willReturn($result);
        $this->assertEquals(
            $result,
            $container->get(FileSystemService::class)->canBeCreated()
        );
    }

    public function test_writeData_worksCorrectlyIfExists()
    {
        $_ENV["JSON_STORAGE_FILE_PATH"] = "./parent/test_file_name";
        $container = static::getContainer();
        $container->set(Filesystem::class, $this->filesystem);
        $this->filesystem
            ->expects(self::once())
            ->method("exists")
            ->with("./parent/test_file_name")
            ->willReturn(true);
        $data = ["data"];
        $this->filesystem
            ->expects(self::once())
            ->method("exists")
            ->with("./parent/test_file_name")
            ->willReturn(true);
        $this->filesystem
            ->expects(self::once())
            ->method("appendToFile")
            ->with("./parent/test_file_name", json_encode($data))
            ->willReturn(true);
        $container->get(FileSystemService::class)->writeData($data);
    }

    public function test_writeData_worksCorrectlyIfDoesntExists()
    {
        $_ENV["JSON_STORAGE_FILE_PATH"] = "./parent/test_file_name";
        $container = static::getContainer();
        $container->set(Filesystem::class, $this->filesystem);
        $this->filesystem
            ->expects(self::once())
            ->method("exists")
            ->with("./parent/test_file_name")
            ->willReturn(false);
        $data = ["data"];
        $this->filesystem
            ->expects(self::once())
            ->method("exists")
            ->with("./parent/test_file_name")
            ->willReturn(true);
        $this->filesystem
            ->expects(self::once())
            ->method("touch")
            ->with("./parent/test_file_name");
        $this->filesystem
            ->expects(self::once())
            ->method("appendToFile")
            ->with("./parent/test_file_name", json_encode($data))
            ->willReturn(true);
        $container->get(FileSystemService::class)->writeData($data);
    }

    public function test_readData_returnsEmptyIfDoesntExist()
    {
        $_ENV["JSON_STORAGE_FILE_PATH"] = "./parent/test_file_name";
        $container = static::getContainer();
        $container->set(Filesystem::class, $this->filesystem);
        $this->filesystem
            ->expects(self::once())
            ->method("exists")
            ->with("./parent/test_file_name")
            ->willReturn(false);
        $this->assertEquals(
            [],
            $container->get(FileSystemService::class)->readData()
        );
    }
}
