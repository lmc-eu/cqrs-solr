<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\ValueObject;

use Solarium\Core\Query\AbstractQuery;

class SolrRequest
{
    private AbstractQuery $query;
    private ?string $endpoint;

    public function __construct(AbstractQuery $query, ?string $endpoint = null)
    {
        $this->query = $query;
        $this->endpoint = $endpoint;
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
