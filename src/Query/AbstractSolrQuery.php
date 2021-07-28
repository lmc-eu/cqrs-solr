<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Query;

use Lmc\Cqrs\Solr\ValueObject\SolrRequest;
use Lmc\Cqrs\Types\Feature\CacheableInterface;
use Lmc\Cqrs\Types\Feature\ProfileableInterface;
use Lmc\Cqrs\Types\QueryInterface;
use Lmc\Cqrs\Types\ValueObject\CacheKey;
use Lmc\Cqrs\Types\ValueObject\CacheTime;

/**
 * @phpstan-implements QueryInterface<SolrRequest>
 */
abstract class AbstractSolrQuery implements QueryInterface, CacheableInterface, ProfileableInterface
{
    private ?string $endpoint = null;

    final public function getRequestType(): string
    {
        return SolrRequest::class;
    }

    abstract public function createRequest(): SolrRequest;

    public function getCacheKey(): CacheKey
    {
        return new CacheKey(
            sprintf('%s:%s:%s:%s', 'solr', $this->getEndpoint(), static::class, md5($this->getRequestUrl()))
        );
    }

    public function getRequestUrl(): string
    {
        $query = $this->createRequest()->getQuery();

        return $query->getRequestBuilder()->build($query)->getUri();
    }

    public function getCacheTime(): CacheTime
    {
        return CacheTime::thirtyMinutes();
    }

    public function getProfilerId(): string
    {
        return sprintf('SOLR:%s', $this->getRequestUrl());
    }

    public function getProfilerData(): ?array
    {
        return [
            'Endpoint' => $this->getEndpoint() ?? 'Default',
        ];
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function setEndpoint(?string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function __toString(): string
    {
        return $this->getRequestUrl();
    }
}
