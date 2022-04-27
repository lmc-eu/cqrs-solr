<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Handler;

use Lmc\Cqrs\Solr\Feature\InjectSolrClientInterface;
use Lmc\Cqrs\Solr\ValueObject\SolrRequest;
use Lmc\Cqrs\Types\Base\AbstractQueryHandler;
use Lmc\Cqrs\Types\QueryInterface;
use Lmc\Cqrs\Types\ValueObject\OnErrorInterface;
use Lmc\Cqrs\Types\ValueObject\OnSuccessInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Query\Result\ResultInterface;

/**
 * @phpstan-extends AbstractQueryHandler<SolrRequest, ResultInterface>
 */
class SolrQueryHandler extends AbstractQueryHandler
{
    public function __construct(private Client $client)
    {
    }

    /** @phpstan-param QueryInterface<mixed> $query */
    public function supports(QueryInterface $query): bool
    {
        return $query->getRequestType() === SolrRequest::class;
    }

    public function prepare(QueryInterface $query): QueryInterface
    {
        if ($query instanceof InjectSolrClientInterface) {
            $query->setSolrClient($this->client);
        }

        return $query;
    }

    /**
     * @phpstan-param QueryInterface<SolrRequest> $query
     * @phpstan-param OnSuccessInterface<ResultInterface> $onSuccess
     */
    public function handle(QueryInterface $query, OnSuccessInterface $onSuccess, OnErrorInterface $onError): void
    {
        if (!$this->assertIsSupported(SolrRequest::class, $query, $onError)) {
            return;
        }

        try {
            /** @var SolrRequest $solrRequest */
            $solrRequest = $query->createRequest();
            $onSuccess($this->client->execute($solrRequest->getQuery(), $solrRequest->getEndpoint()));
        } catch (\Throwable $e) {
            $onError($e);
        }
    }
}
