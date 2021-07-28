<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder;

use Lmc\Cqrs\Solr\Query\AbstractSolrQuery;
use Lmc\Cqrs\Solr\Query\AbstractSolrSelectQuery;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\ApplicatorFactory;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\EntityApplicator;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\FacetsApplicator;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\FilterApplicator;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\FiltersApplicator;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\FulltextApplicator;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\FulltextBigramApplicator;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\FulltextBoostApplicator;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\GroupingApplicator;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\GroupingFacetApplicator;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\ParameterizedApplicator;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\SortApplicator;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\BaseDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\DisabledEDisMaxDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FacetsDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FilterDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FiltersDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextBigramDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextBoostDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\GroupingDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\GroupingFacetDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\ParameterizedDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\SortDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\StatsDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Query\BuilderPrototypeQuery;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Endpoint;

class QueryBuilderTest extends TestCase
{
    private QueryBuilder $queryBuilderWithoutApplicators;
    private QueryBuilder $queryBuilderWithApplicators;

    protected function setUp(): void
    {
        $applicatorFactoryMock = $this->getMockBuilder(ApplicatorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $applicatorFactoryMock->expects($this->any())
            ->method('getApplicators')
            ->willReturn([]);

        $this->queryBuilderWithoutApplicators = new QueryBuilder($applicatorFactoryMock);

        $applicatorFactory = new ApplicatorFactory([
            new EntityApplicator(),
            new FulltextApplicator(),
            new FulltextBigramApplicator(),
            new FulltextBoostApplicator(),
            new FilterApplicator(),
            new FiltersApplicator(),
            new GroupingApplicator(),
            new SortApplicator(),
            new FacetsApplicator(),
            new GroupingFacetApplicator(),
            new ParameterizedApplicator(),
        ]);

        $this->queryBuilderWithApplicators = new QueryBuilder($applicatorFactory);
    }

    /**
     * @dataProvider provideEntity
     *
     * @test
     */
    public function shouldBuildQuery(EntityInterface $entity): void
    {
        $query = $this->queryBuilderWithoutApplicators->buildQuery($entity);

        $this->assertInstanceOf(AbstractSolrQuery::class, $query);
        $this->assertInstanceOf(AbstractSolrSelectQuery::class, $query);
        $this->assertInstanceOf(BuilderPrototypeQuery::class, $query);
    }

    public function provideEntity(): array
    {
        $defaultEmptyQuery = 'q=' . urlencode('*:*');

        return [
            'Base Entity' => [
                new BaseDummyEntity(),
                [
                    'start=0',
                    'rows=10',
                    'fl=jds',
                    $defaultEmptyQuery,
                ],
            ],
            'Base Entity with query' => [
                new BaseDummyEntity('query'),
                [
                    'start=0',
                    'rows=10',
                    'fl=jds',
                    'q=query',
                ],
            ],
            'Facets Entity' => [
                new FacetsDummyEntity(),
                [
                    'start=0',
                    'rows=20',
                    'fl=&',
                    $defaultEmptyQuery,
                    'facet.field=' . urlencode('{!key=facet-field ex=global-exclude,facet-field-exclude}field'),
                    'f.field.facet.limit=60',
                    'facet.pivot=' . urlencode('{!key=pivot-field ex=global-exclude}pivot-field'),
                    'facet.query=' . urlencode('{!key=100-199}interval-field:[100 TO 199]'),
                    'facet.query=' . urlencode('{!key=200-300}interval-field:[200 TO 300]'),
                    'facet=true',
                    'facet.sort=index',
                    'facet.mincount=5',
                    'facet.limit=100',
                ],
            ],
            'Filter Entity' => [
                new FilterDummyEntity(),
                [
                    'start=0',
                    'rows=10',
                    'fl=&',
                    $defaultEmptyQuery,
                    'fq=' . urlencode('{!tag=filter-tag}filter:query'),
                ],
            ],
            'Filters Entity' => [
                new FiltersDummyEntity(),
                [
                    'start=0',
                    'rows=20',
                    'fl=&',
                    $defaultEmptyQuery,
                    'fq=' . urlencode('{!tag=global-tags,filter-1-tag}fitler1:"value"'),
                    'fq=' . urlencode('{!tag=global-tags}fitler2:"value'),
                ],
            ],
            'Fulltext Entity' => [
                new FulltextDummyEntity(),
                [
                    'start=0',
                    'rows=20',
                    'fl=&',
                    'q=' . urlencode('key word'),
                    'defType=edismax',
                    'q.alt=' . urlencode('*:*'),
                    'qf=field',
                    'mm=2',
                    'pf=field',
                    'tie=0.2',
                    'q.op=AND',
                ],
            ],
            'Fulltext bigram Entity' => [
                new FulltextBigramDummyEntity(),
                [
                    'start=0',
                    'rows=20',
                    'fl=&',
                    'q=' . urlencode('key word'),
                    'defType=edismax',
                    'q.alt=' . urlencode('*:*'),
                    'qf=field',
                    'mm=2',
                    'pf=field',
                    'pf2=' . urlencode('bigram-field~5'),
                    'tie=0.2',
                    'q.op=AND',
                ],
            ],
            'Fulltext boost Entity' => [
                new FulltextBoostDummyEntity(),
                [
                    'start=0',
                    'rows=20',
                    'fl=&',
                    'q=' . urlencode('key word'),
                    'defType=edismax',
                    'q.alt=' . urlencode('*:*'),
                    'qf=field',
                    'mm=2',
                    'pf=field',
                    'ps=2',
                    'tie=0.2',
                    'q.op=AND',
                    'bq=' . urlencode('boosted-field:value^5.0'),
                ],
            ],
            'Grouping Entity' => [
                new GroupingDummyEntity(),
                [
                    'start=0',
                    'rows=20',
                    'fl=&',
                    $defaultEmptyQuery,
                    'group=true',
                    'group.field=grouping-field',
                    'group.limit=50',
                    'group.main=true',
                    'group.ngroups=true',
                ],
            ],
            'Grouping facet Entity' => [
                new GroupingFacetDummyEntity(),
                [
                    'start=0',
                    'rows=20',
                    'fl=&',
                    $defaultEmptyQuery,
                    'group=true',
                    'group.field=grouping-field',
                    'group.limit=50',
                    'group.main=true',
                    'group.ngroups=true',
                    'group.facet=true',
                    'facet.field=' . urlencode('{!key=facet-field ex=global-exclude,facet-field-exclude}field'),
                    'f.field.facet.limit=100',
                    'facet.pivot=' . urlencode('{!key=pivot-field ex=global-exclude}pivot-field'),
                    'facet.query=' . urlencode('{!key=100-199}interval-field:[100 TO 199]'),
                    'facet.query=' . urlencode('{!key=200-300}interval-field:[200 TO 300]'),
                    'facet=true',
                    'facet.sort=index',
                    'facet.mincount=5',
                    'facet.limit=100',
                ],
            ],
            'Sort Entity' => [
                new SortDummyEntity(),
                [
                    'start=0',
                    'rows=20',
                    'fl=&',
                    $defaultEmptyQuery,
                    'sort=' . urlencode('score desc,field asc'),
                ],
            ],
            'Parametrized Entity' => [
                new ParameterizedDummyEntity(),
                [
                    'start=0',
                    'rows=20',
                    $defaultEmptyQuery,
                    'paramField=paramValue',
                ],
            ],
            'Disabled edismax Entity' => [
                new DisabledEDisMaxDummyEntity(),
                [
                    'start=0',
                    'rows=20',
                    'fl=&',
                    'q=' . urlencode('key word'),
                    'q.op=AND',
                ],
            ],
            'Stats Entity' => [
                new StatsDummyEntity(),
                [
                    'start=0',
                    'rows=42',
                    $defaultEmptyQuery,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideEntity
     *
     * @test
     */
    public function shouldBuildQueryWithApplicators(EntityInterface $entity, array $expectedParts): void
    {
        $expectedParts[] = 'select?omitHeader=true';
        $expectedParts[] = 'wt=json';
        $expectedParts[] = 'json.nl=flat';

        $query = $this->queryBuilderWithApplicators->buildQuery($entity);
        $query->setSolrClient(new Client());

        $this->assertInstanceOf(AbstractSolrQuery::class, $query);
        $this->assertInstanceOf(BuilderPrototypeQuery::class, $query);

        $queryString = $query->__toString();

        foreach ($expectedParts as $expected) {
            $this->assertStringContainsString($expected, $queryString);
        }
    }

    /**
     * @dataProvider provideQueryOptions
     *
     * @test
     */
    public function shouldSetQueryOptions(array $queryOptionsParameters, string $expectedFullQuery): void
    {
        $entity = new BaseDummyEntity((string) $queryOptionsParameters['query']);

        $query = $this->queryBuilderWithApplicators->buildQuery($entity);
        $query->setSolrClient(new Client());

        $this->assertSame($expectedFullQuery, $query->__toString());
    }

    public function provideQueryOptions(): array
    {
        return [
            'query with q parameter' => [
                'queryOptionsParameters' => ['query' => 'hello'],
                'expectedFullQuery' => 'select?omitHeader=true&wt=json&json.nl=flat&q=hello&start=0&rows=10&fl=jds%2C%2A%2Cscore&q.op=AND',
            ],
            'query without q parameter' => [
                'queryOptionsParameters' => ['query' => null],
                'expectedFullQuery' => 'select?omitHeader=true&wt=json&json.nl=flat&q=%2A%3A%2A&start=0&rows=10&fl=jds%2C%2A%2Cscore&q.op=AND',
            ],
        ];
    }

    /**
     * @test
     */
    public function shouldProfileUsedValues(): void
    {
        $client = new Client();
        $entity = new BaseDummyEntity('query');

        $query = $this->queryBuilderWithApplicators->buildQuery($entity);
        $query->setSolrClient($client);

        $profilerData = $query->getProfilerData();

        $this->assertIsArray($profilerData);

        $this->assertArrayHasKey('Endpoint', $profilerData);
        $this->assertSame('Default', $profilerData['Endpoint']);

        $this->assertArrayHasKey('Endpoint.details', $profilerData);
        $this->assertInstanceOf(Endpoint::class, $profilerData['Endpoint.details']);

        $this->assertNotSame(
            $client->getEndpoint($query->getEndpoint()),
            $profilerData['Endpoint.details'],
            'Profiled endpoint should not be a real instance of endpoint, but it should be cloned, so it remains unchanged in time of profiling.'
        );
    }
}
