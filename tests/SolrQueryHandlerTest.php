<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr;

use Lmc\Cqrs\Solr\Fixture\DummySolrQuery;
use Lmc\Cqrs\Solr\Handler\SolrQueryHandler;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\ApplicatorFactory;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\BaseDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\QueryBuilder;
use Lmc\Cqrs\Types\ValueObject\OnErrorCallback;
use Lmc\Cqrs\Types\ValueObject\OnSuccessCallback;
use Solarium\Core\Query\Result\ResultInterface;

class SolrQueryHandlerTest extends AbstractSolrTestCase
{
    private SolrQueryHandler $solrQueryHandler;

    protected function setUp(): void
    {
        $this->solrQueryHandler = new SolrQueryHandler($this->client);
    }

    /**
     * @test
     */
    public function shouldFetchSolrQuery(): void
    {
        $fields = ['field'];
        $data = ['data'];

        $query = new DummySolrQuery($fields);

        $solrSelect = $this->prepareSelect($fields);
        $result = $this->prepareResult($data);

        $this->expectClientToCreateQueryOnce($solrSelect);
        $this->expectClientToExecuteSelectQueryOnce($solrSelect, null, $result);

        $this->solrQueryHandler->prepare($query);
        $this->solrQueryHandler->handle(
            $query,
            new OnSuccessCallback(
                fn (ResultInterface $result) => $this->assertSame($data, $result->getData())
            ),
            new OnErrorCallback(fn (\Throwable $error) => $this->fail($error->getMessage()))
        );
    }

    /**
     * @test
     */
    public function shouldFetchBuiltSolrQueryWithCustomEndpoint(): void
    {
        $entity = new BaseDummyEntity('query');
        $endpoint = 'custom-endpoint';
        $data = ['data'];

        $queryBuilder = new QueryBuilder(new ApplicatorFactory([]));

        $query = $queryBuilder->buildQueryWithEndpoint($entity, $endpoint);

        $solrSelect = $this->prepareSelect();
        $result = $this->prepareResult($data);

        $this->expectClientToCreateQueryOnce($solrSelect);
        $this->expectClientToExecuteSelectQueryOnce($solrSelect, $endpoint, $result);

        $this->solrQueryHandler->prepare($query);
        $this->solrQueryHandler->handle(
            $query,
            new OnSuccessCallback(
                fn (ResultInterface $result) => $this->assertSame($data, $result->getData())
            ),
            new OnErrorCallback(fn (\Throwable $error) => $this->fail($error->getMessage()))
        );
    }
}
