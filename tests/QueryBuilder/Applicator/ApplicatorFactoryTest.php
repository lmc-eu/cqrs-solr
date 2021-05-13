<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\BaseDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FacetsDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FilterDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FiltersDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextBigramBoostDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextBigramDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextBoostDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\GroupingDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\GroupingFacetDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\ParameterizedDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\SortDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\StatsDummyEntity;
use PHPUnit\Framework\TestCase;

class ApplicatorFactoryTest extends TestCase
{
    private ApplicatorFactory $applicatorFactory;
    private EntityApplicator $entityApplicator;
    private FacetsApplicator $facetsApplicator;
    private FilterApplicator $filterApplicator;
    private FiltersApplicator $filtersApplicator;
    private FulltextApplicator $fulltextApplicator;
    private FulltextBigramApplicator $fulltextBigramApplicator;
    private FulltextBoostApplicator $fulltextBoostApplicator;
    private GroupingApplicator $groupingApplicator;
    private GroupingFacetApplicator $groupingFacetApplicator;
    private SortApplicator $sortApplicator;
    private ParameterizedApplicator $parameterizedApplicator;
    private StatsApplicator $statsApplicator;

    protected function setUp(): void
    {
        $this->entityApplicator = new EntityApplicator();
        $this->facetsApplicator = new FacetsApplicator();
        $this->filterApplicator = new FilterApplicator();
        $this->filtersApplicator = new FiltersApplicator();
        $this->fulltextApplicator = new FulltextApplicator();
        $this->fulltextBigramApplicator = new FulltextBigramApplicator();
        $this->fulltextBoostApplicator = new FulltextBoostApplicator();
        $this->groupingApplicator = new GroupingApplicator();
        $this->groupingFacetApplicator = new GroupingFacetApplicator();
        $this->sortApplicator = new SortApplicator();
        $this->parameterizedApplicator = new ParameterizedApplicator();
        $this->statsApplicator = new StatsApplicator();

        $this->applicatorFactory = new ApplicatorFactory([
            $this->entityApplicator,
            $this->facetsApplicator,
            $this->filterApplicator,
            $this->filtersApplicator,
            $this->fulltextApplicator,
            $this->fulltextBigramApplicator,
            $this->fulltextBoostApplicator,
            $this->groupingApplicator,
            $this->groupingFacetApplicator,
            $this->sortApplicator,
            $this->parameterizedApplicator,
            $this->statsApplicator,
        ]);
    }

    /**
     * @param string[] $expected
     *
     * @dataProvider provideEntities
     *
     * @test
     */
    public function shouldGetFixturesForEntity(EntityInterface $entity, array $expected): void
    {
        $applicators = $this->applicatorFactory->getApplicators($entity);

        $this->assertCount(count($expected), $applicators, sprintf('Unexpected count for %s', get_class($entity)));

        $i = 0;
        foreach ($applicators as $applicator) {
            $this->assertInstanceOf($expected[$i], $applicator);
            $i++;
        }
    }

    public function provideEntities(): array
    {
        return [
            [
                'entity' => new BaseDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    FulltextApplicator::class,
                ],
            ],
            [
                'entity' => new FacetsDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    FacetsApplicator::class,
                ],
            ],
            [
                'entity' => new FilterDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    FilterApplicator::class,
                ],
            ],
            [
                'entity' => new FiltersDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    FiltersApplicator::class,
                ],
            ],
            [
                'entity' => new FulltextDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    FulltextApplicator::class,
                ],
            ],
            [
                'entity' => new FulltextBigramDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    FulltextApplicator::class,
                    FulltextBigramApplicator::class,
                ],
            ],
            [
                'entity' => new FulltextBoostDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    FulltextApplicator::class,
                    FulltextBoostApplicator::class,
                ],
            ],
            [
                'entity' => new FulltextBigramBoostDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    FulltextApplicator::class,
                    FulltextBigramApplicator::class,
                    FulltextBoostApplicator::class,
                ],
            ],
            [
                'entity' => new GroupingDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    GroupingApplicator::class,
                ],
            ],
            [
                'entity' => new GroupingFacetDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    FacetsApplicator::class,
                    GroupingApplicator::class,
                    GroupingFacetApplicator::class,
                ],
            ],
            [
                'entity' => new SortDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    SortApplicator::class,
                ],
            ],
            [
                'entity' => new ParameterizedDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    ParameterizedApplicator::class,
                ],
            ],
            [
                'entity' => new StatsDummyEntity(),
                'expected' => [
                    EntityApplicator::class,
                    StatsApplicator::class,
                ],
            ],
        ];
    }
}
