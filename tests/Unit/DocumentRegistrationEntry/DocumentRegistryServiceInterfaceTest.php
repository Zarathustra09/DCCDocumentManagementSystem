<?php

namespace Tests\Unit\DocumentRegistrationEntry;

use App\Interfaces\DocumentRegistryServiceInterface;
use App\Models\DocumentRegistrationEntry;
use Illuminate\Http\Request;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class DocumentRegistryServiceInterfaceTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_interface_can_be_mocked_with_create_method(): void
    {
        $mock = Mockery::mock(DocumentRegistryServiceInterface::class);
        $request = Mockery::mock(Request::class);
        $entry = Mockery::mock(DocumentRegistrationEntry::class);

        $mock->shouldReceive('create')
            ->once()
            ->with($request)
            ->andReturn($entry);

        $result = $mock->create($request);

        $this->assertInstanceOf(DocumentRegistrationEntry::class, $result);
    }

    public function test_interface_can_be_mocked_with_update_minimal_method(): void
    {
        $mock = Mockery::mock(DocumentRegistryServiceInterface::class);
        $request = Mockery::mock(Request::class);
        $entry = Mockery::mock(DocumentRegistrationEntry::class);

        $mock->shouldReceive('updateMinimal')
            ->once()
            ->with($request, $entry)
            ->andReturn($entry);

        $result = $mock->updateMinimal($request, $entry);

        $this->assertInstanceOf(DocumentRegistrationEntry::class, $result);
    }

    public function test_interface_can_be_mocked_with_update_full_method(): void
    {
        $mock = Mockery::mock(DocumentRegistryServiceInterface::class);
        $request = Mockery::mock(Request::class);
        $entry = Mockery::mock(DocumentRegistrationEntry::class);

        $mock->shouldReceive('updateFull')
            ->once()
            ->with($request, $entry)
            ->andReturn($entry);

        $result = $mock->updateFull($request, $entry);

        $this->assertInstanceOf(DocumentRegistrationEntry::class, $result);
    }

    public function test_interface_can_be_mocked_with_approve_method(): void
    {
        $mock = Mockery::mock(DocumentRegistryServiceInterface::class);
        $request = Mockery::mock(Request::class);
        $entry = Mockery::mock(DocumentRegistrationEntry::class);

        $mock->shouldReceive('approve')
            ->once()
            ->with($request, $entry)
            ->andReturnNull();

        $mock->approve($request, $entry);

        $this->assertTrue(true);
    }

    public function test_interface_can_be_mocked_with_reject_method(): void
    {
        $mock = Mockery::mock(DocumentRegistryServiceInterface::class);
        $request = Mockery::mock(Request::class);
        $entry = Mockery::mock(DocumentRegistrationEntry::class);

        $mock->shouldReceive('reject')
            ->once()
            ->with($request, $entry)
            ->andReturnNull();

        $mock->reject($request, $entry);

        $this->assertTrue(true);
    }

    public function test_interface_can_be_mocked_with_require_revision_method(): void
    {
        $mock = Mockery::mock(DocumentRegistryServiceInterface::class);
        $request = Mockery::mock(Request::class);
        $entry = Mockery::mock(DocumentRegistrationEntry::class);

        $mock->shouldReceive('requireRevision')
            ->once()
            ->with($request, $entry)
            ->andReturnNull();

        $mock->requireRevision($request, $entry);

        $this->assertTrue(true);
    }

    public function test_interface_can_be_mocked_with_withdraw_method(): void
    {
        $mock = Mockery::mock(DocumentRegistryServiceInterface::class);
        $entry = Mockery::mock(DocumentRegistrationEntry::class);

        $mock->shouldReceive('withdraw')
            ->once()
            ->with($entry)
            ->andReturnNull();

        $mock->withdraw($entry);

        $this->assertTrue(true);
    }

    public function test_interface_has_expected_methods(): void
    {
        $expectedMethods = [
            'create',
            'updateMinimal',
            'updateFull',
            'approve',
            'reject',
            'requireRevision',
            'withdraw',
        ];

        $reflection = new ReflectionClass(DocumentRegistryServiceInterface::class);

        foreach ($expectedMethods as $method) {
            $this->assertTrue($reflection->hasMethod($method), "Method {$method} is missing");
        }
    }
}
