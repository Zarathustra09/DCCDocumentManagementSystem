<?php

namespace Tests\Unit\DocumentRegistrationEntryFiles;

use App\Interfaces\DocumentRegistryFileServiceInterface;
use App\Models\DocumentRegistrationEntry;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class DocumentRegistryFileServiceInterfaceTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_interface_has_expected_methods(): void
    {
        $expectedMethods = [
            'download',
            'preview',
            'previewApi',
        ];

        $reflection = new ReflectionClass(DocumentRegistryFileServiceInterface::class);

        foreach ($expectedMethods as $method) {
            $this->assertTrue($reflection->hasMethod($method), "Method {$method} is missing");
        }
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_interface_can_be_mocked_with_download_method(): void
    {
        $mock = Mockery::mock(DocumentRegistryFileServiceInterface::class);
        $entry = Mockery::mock(DocumentRegistrationEntry::class);
        $fileId = 1;

        $mock->shouldReceive('download')
            ->once()
            ->with($entry, $fileId)
            ->andReturn('download response');

        $result = $mock->download($entry, $fileId);

        $this->assertEquals('download response', $result);
    }

    public function test_interface_can_be_mocked_with_preview_method(): void
    {
        $mock = Mockery::mock(DocumentRegistryFileServiceInterface::class);
        $entry = Mockery::mock(DocumentRegistrationEntry::class);
        $fileId = 1;

        $mock->shouldReceive('preview')
            ->once()
            ->with($entry, $fileId)
            ->andReturn('preview response');

        $result = $mock->preview($entry, $fileId);

        $this->assertEquals('preview response', $result);
    }

    public function test_interface_can_be_mocked_with_preview_api_method(): void
    {
        $mock = Mockery::mock(DocumentRegistryFileServiceInterface::class);
        $entry = Mockery::mock(DocumentRegistrationEntry::class);
        $fileId = 1;

        $mock->shouldReceive('previewApi')
            ->once()
            ->with($entry, $fileId)
            ->andReturn(['success' => true]);

        $result = $mock->previewApi($entry, $fileId);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
    }
}
