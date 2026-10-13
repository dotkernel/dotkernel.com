<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Middleware;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Light\App\Middleware\IpAddressMiddleware;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class IpAddressMiddlewareTest extends UnitTest
{
    /**
     * @param array<non-empty-string, string> $headers
     */
    private function request(?string $remoteAddr, array $headers = []): ServerRequest
    {
        $serverParams = $remoteAddr === null ? [] : ['REMOTE_ADDR' => $remoteAddr];

        return new ServerRequest($serverParams, [], null, 'GET', 'php://memory', $headers);
    }

    /**
     * @throws Exception
     */
    public function testProcessStoresTheIpAddressAsARequestAttribute(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(
                fn (ServerRequestInterface $request): bool => $request->getAttribute('ip_address') === '198.51.100.7'
            ))
            ->willReturn(new Response());

        (new IpAddressMiddleware())->process($this->request('198.51.100.7'), $handler);
    }

    public function testHeadersAreIgnoredWithoutTrustedProxies(): void
    {
        $request = $this->request('198.51.100.7', ['X-Forwarded-For' => '6.6.6.6']);

        $this->assertSame('198.51.100.7', (new IpAddressMiddleware())->determineIpAddress($request));
    }

    public function testHeadersAreIgnoredWhenRemoteAddrIsNotATrustedProxy(): void
    {
        $request = $this->request('198.51.100.7', ['X-Forwarded-For' => '6.6.6.6']);

        $this->assertSame('198.51.100.7', (new IpAddressMiddleware(['10.0.0.1']))->determineIpAddress($request));
    }

    public function testMissingRemoteAddrGivesNull(): void
    {
        $this->assertNull((new IpAddressMiddleware(['10.0.0.1']))->determineIpAddress($this->request(null)));
    }

    public function testInvalidRemoteAddrGivesNull(): void
    {
        $this->assertNull((new IpAddressMiddleware())->determineIpAddress($this->request('not-an-ip')));
    }

    /**
     * @return array<string, array{list<string>, string, array<non-empty-string, string>, string}>
     */
    public static function trustedProxyProvider(): array
    {
        return [
            'exact proxy'                               => [
                ['10.0.0.1'],
                '10.0.0.1',
                ['X-Forwarded-For' => '198.51.100.7'],
                '198.51.100.7',
            ],
            'IPv4 CIDR proxy'                           => [
                ['173.245.48.0/20'],
                '173.245.60.1',
                ['X-Forwarded-For' => '198.51.100.7'],
                '198.51.100.7',
            ],
            'IPv6 CIDR proxy'                           => [
                ['2400:cb00::/32'],
                '2400:cb00:2049::1',
                ['X-Forwarded-For' => '2001:db8::1'],
                '2001:db8::1',
            ],
            'spoofed address left of the proxy'         => [
                ['10.0.0.1'],
                '10.0.0.1',
                ['X-Forwarded-For' => '6.6.6.6, 198.51.100.7'],
                '198.51.100.7',
            ],
            'chain of trusted proxies'                  => [
                ['10.0.0.0/8'],
                '10.0.0.1',
                ['X-Forwarded-For' => '198.51.100.7, 10.0.0.2'],
                '198.51.100.7',
            ],
            'only trusted proxies in the chain'         => [
                ['10.0.0.0/8'],
                '10.0.0.1',
                ['X-Forwarded-For' => '10.0.0.3, 10.0.0.2'],
                '10.0.0.3',
            ],
            'IPv4 with port'                            => [
                ['10.0.0.1'],
                '10.0.0.1',
                ['X-Forwarded-For' => '198.51.100.7:4711'],
                '198.51.100.7',
            ],
            'Forwarded header'                          => [
                ['10.0.0.1'],
                '10.0.0.1',
                ['Forwarded' => 'for=198.51.100.7;proto=https;by=10.0.0.1'],
                '198.51.100.7',
            ],
            'Forwarded quoted IPv6 with port'           => [
                ['10.0.0.1'],
                '10.0.0.1',
                ['Forwarded' => 'for="[2001:db8:cafe::17]:4711"'],
                '2001:db8:cafe::17',
            ],
            'Forwarded wins over X-Forwarded-For'       => [
                ['10.0.0.1'],
                '10.0.0.1',
                ['Forwarded' => 'for=198.51.100.7', 'X-Forwarded-For' => '203.0.113.9'],
                '198.51.100.7',
            ],
            'X-Real-IP'                                 => [
                ['10.0.0.1'],
                '10.0.0.1',
                ['X-Real-IP' => '198.51.100.7'],
                '198.51.100.7',
            ],
            'invalid hop falls back to the next header' => [
                ['10.0.0.1'],
                '10.0.0.1',
                ['X-Forwarded-For' => '198.51.100.7, garbage', 'X-Real-IP' => '203.0.113.9'],
                '203.0.113.9',
            ],
            'no usable header keeps REMOTE_ADDR'        => [
                ['10.0.0.1'],
                '10.0.0.1',
                ['X-Forwarded-For' => 'garbage'],
                '10.0.0.1',
            ],
        ];
    }

    /**
     * @param list<string> $trustedProxies
     * @param array<non-empty-string, string> $headers
     */
    #[DataProvider('trustedProxyProvider')]
    public function testTrustedProxyHeadersGiveTheClientAddress(
        array $trustedProxies,
        string $remoteAddr,
        array $headers,
        string $expected,
    ): void {
        $middleware = new IpAddressMiddleware($trustedProxies);

        $this->assertSame($expected, $middleware->determineIpAddress($this->request($remoteAddr, $headers)));
    }

    public function testOnlyTheConfiguredHeadersAreRead(): void
    {
        $middleware = new IpAddressMiddleware(['10.0.0.1'], ['CF-Connecting-IP']);
        $request    = $this->request('10.0.0.1', [
            'X-Forwarded-For'  => '6.6.6.6',
            'CF-Connecting-IP' => '198.51.100.7',
        ]);

        $this->assertSame('198.51.100.7', $middleware->determineIpAddress($request));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidProxyProvider(): array
    {
        return [
            'mask too long'     => ['10.0.0.0/33'],
            'mask not a number' => ['10.0.0.0/abc'],
            'not an address'    => ['proxy.example'],
            'other IP family'   => ['::/0'],
        ];
    }

    #[DataProvider('invalidProxyProvider')]
    public function testInvalidTrustedProxyEntriesTrustNothing(string $proxy): void
    {
        $request = $this->request('10.0.0.1', ['X-Forwarded-For' => '198.51.100.7']);

        $this->assertSame('10.0.0.1', (new IpAddressMiddleware([$proxy]))->determineIpAddress($request));
    }
}
