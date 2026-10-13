<?php

declare(strict_types=1);

namespace Light\App\Middleware;

use Dot\DependencyInjection\Attribute\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_reverse;
use function ctype_digit;
use function explode;
use function filter_var;
use function inet_pton;
use function intdiv;
use function is_string;
use function ord;
use function str_contains;
use function str_starts_with;
use function strcasecmp;
use function strlen;
use function strncmp;
use function strpos;
use function strrpos;
use function substr;
use function substr_count;
use function trim;

use const FILTER_VALIDATE_IP;

/**
 * Determines the client IP address and stores it in the `ip_address` request attribute,
 * the same way akrabat/ip-address-middleware does.
 *
 * Forwarding headers are read only when REMOTE_ADDR is a trusted proxy, because any client can
 * send them with any value. With no trusted proxies configured, the attribute is REMOTE_ADDR.
 */
class IpAddressMiddleware implements MiddlewareInterface
{
    public const string ATTRIBUTE = 'ip_address';

    /**
     * @param list<string> $trustedProxies IP addresses or CIDR ranges, e.g. `10.0.0.1` or `173.245.48.0/20`
     * @param list<string> $headers checked in order; the first one that gives an address wins
     */
    #[Inject(
        'config.ip_address.trusted_proxies',
        'config.ip_address.headers',
    )]
    public function __construct(
        private readonly array $trustedProxies = [],
        private readonly array $headers = ['Forwarded', 'X-Forwarded-For', 'X-Real-IP'],
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request->withAttribute(self::ATTRIBUTE, $this->determineIpAddress($request)));
    }

    public function determineIpAddress(ServerRequestInterface $request): ?string
    {
        $remoteAddr = $this->normalize($request->getServerParams()['REMOTE_ADDR'] ?? null);
        if ($remoteAddr === null || ! $this->isTrustedProxy($remoteAddr)) {
            return $remoteAddr;
        }

        foreach ($this->headers as $header) {
            $ipAddress = $this->fromHeader($request, $header);
            if ($ipAddress !== null) {
                return $ipAddress;
            }
        }

        return $remoteAddr;
    }

    private function fromHeader(ServerRequestInterface $request, string $header): ?string
    {
        $line = $request->getHeaderLine($header);
        if ($line === '') {
            return null;
        }

        $hops = [];
        foreach (explode(',', $line) as $hop) {
            $hops[] = $this->normalize(strcasecmp($header, 'Forwarded') === 0 ? $this->forwardedFor($hop) : $hop);
        }

        /**
         * Each proxy appends the address it received the request from, so walk from the right: the first
         * address that is not one of our own proxies is the client. Anything left of it could be forged.
         */
        foreach (array_reverse($hops) as $hop) {
            if ($hop === null) {
                return null;
            }
            if (! $this->isTrustedProxy($hop)) {
                return $hop;
            }
        }

        return $hops[0];
    }

    /**
     * Returns the `for=` value of one RFC 7239 element, e.g. `for=192.0.2.60;proto=https;by=203.0.113.43`.
     */
    private function forwardedFor(string $element): string
    {
        foreach (explode(';', $element) as $pair) {
            $parts = explode('=', $pair, 2);
            if (isset($parts[1]) && strcasecmp(trim($parts[0]), 'for') === 0) {
                return $parts[1];
            }
        }

        return '';
    }

    /**
     * Strips quotes, IPv6 brackets and ports; returns null for anything that is not an IP address.
     */
    private function normalize(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim(trim($value), '"');
        if (str_starts_with($value, '[')) {
            $end   = strpos($value, ']');
            $value = $end === false ? '' : substr($value, 1, $end - 1);
        } elseif (substr_count($value, ':') === 1) {
            $value = substr($value, 0, (int) strrpos($value, ':'));
        }

        return filter_var($value, FILTER_VALIDATE_IP) === false ? null : $value;
    }

    private function isTrustedProxy(string $ipAddress): bool
    {
        foreach ($this->trustedProxies as $proxy) {
            if ($this->matches($ipAddress, $proxy)) {
                return true;
            }
        }

        return false;
    }

    private function matches(string $ipAddress, string $proxy): bool
    {
        $subnet = $proxy;
        $bits   = null;
        if (str_contains($proxy, '/')) {
            [$subnet, $bits] = explode('/', $proxy, 2);
        }

        $address = @inet_pton($ipAddress);
        $network = @inet_pton($subnet);
        if ($address === false || $network === false || strlen($address) !== strlen($network)) {
            return false;
        }

        if ($bits === null) {
            return $address === $network;
        }

        if (! ctype_digit($bits) || (int) $bits > strlen($address) * 8) {
            return false;
        }

        $bytes = intdiv((int) $bits, 8);
        if (strncmp($address, $network, $bytes) !== 0) {
            return false;
        }

        $remainder = (int) $bits % 8;
        if ($remainder === 0) {
            return true;
        }

        $mask        = (0xFF << (8 - $remainder)) & 0xFF;
        $addressBits = ord($address[$bytes]) & $mask;
        $networkBits = ord($network[$bytes]) & $mask;

        return $addressBits === $networkBits;
    }
}
