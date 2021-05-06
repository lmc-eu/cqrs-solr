<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Query;

use Lmc\Cqrs\Solr\Feature\InjectSolrClientInterface;
use Lmc\Cqrs\Solr\ValueObject\SolrRequest;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Select\Query\Query;

abstract class AbstractSolrSelectQuery extends AbstractSolrQuery implements InjectSolrClientInterface
{
    protected ?Client $client = null;
    private ?int $offset = null;
    private ?int $limit = null;

    public function setSolrClient(Client $client): void
    {
        $this->client = $client;
    }

    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    final public function createRequest(): SolrRequest
    {
        if ($this->client === null) {
            throw new \LogicException('Client is not set.');
        }

        return new SolrRequest(
            $this->prepareQuery($this->client->createSelect()),
            $this->getEndpoint()
        );
    }

    private function prepareQuery(Query $select): Query
    {
        if ($this->offset !== null) {
            $select->setStart($this->offset);
        }

        if ($this->limit !== null) {
            $select->setRows($this->limit);
        }

        return $this->prepareSelect($select);
    }

    abstract public function prepareSelect(Query $select): Query;
}
