<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\ValueObject;

use Solarium\Core\Query\AbstractQuery;

class SolrRequest
{
    public function __construct(private AbstractQuery $query, private ?string $endpoint = null)
    {
    }

    public function getQuery(): AbstractQuery
    {
        return $this->query;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }
}
