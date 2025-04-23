<?php

namespace App\Tests\BinProvider;

use App\BinProvider\BinProvider;
use App\Http\ApiClient;
use JsonException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class BinProviderTest extends TestCase
{
    /**
     * @throws Exception|JsonException
     */
    public function testGetBinDetailsSuccess()
    {
        $mockApiClient = $this->createMock(ApiClient::class);
        $mockApiClient->expects($this->once())
            ->method('get')
            ->with(
                'https://lookup.binlist.net/123456',
                ['Authorization' => 'Bearer api-key']
            )
            ->willReturn([
                'country' => [
                    'alpha2' => 'DE',
                ],
            ]);

        $provider = new BinProvider('https://lookup.binlist.net/', 'api-key', $mockApiClient);
        $result = $provider->getBinDetails('123456');

        $this->assertEquals('DE', $result['country']['alpha2']);
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testGetBinDetailsFailure()
    {
        $mockApiClient = $this->createMock(ApiClient::class);
        $mockApiClient->expects($this->once())
            ->method('get')
            ->with(
                'https://lookup.binlist.net/123456',
                ['Authorization' => 'Bearer api-key']
            )
            ->willThrowException(new RuntimeException('Error'));

        $provider = new BinProvider('https://lookup.binlist.net/', 'api-key', $mockApiClient);

        $this->expectException(RuntimeException::class);
        $provider->getBinDetails('123456');
    }
}
