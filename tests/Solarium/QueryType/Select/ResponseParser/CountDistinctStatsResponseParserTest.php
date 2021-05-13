<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Solarium\QueryType\Select\ResponseParser;

use Lmc\Cqrs\Solr\Solarium\QueryType\Select\Result\CountDistinctStatsResult;
use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Stats\FacetValue;
use Solarium\QueryType\Select\Query\Query;

class CountDistinctStatsResponseParserTest extends TestCase
{
    /** @var CountDistinctStatsResponseParser */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new CountDistinctStatsResponseParser();
    }

    /**
     * @test
     */
    public function shouldParseData(): void
    {
        $data = [
            'stats' => [
                'stats_fields' => [
                    'fieldA' => [
                        'min' => 3,
                    ],
                    'fieldB' => [
                        'min' => 4,
                        'facets' => [
                            'fieldC' => [
                                'value1' => [
                                    'min' => 5,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->parser->parse($this->createMock(Query::class), new \stdClass(), $data);

        $result1 = $result->getResult('fieldA');
        $this->assertInstanceOf(CountDistinctStatsResult::class, $result1);
        $this->assertEquals(3, $result1->getMin());

        $result2 = $result->getResult('fieldB');
        $this->assertInstanceOf(CountDistinctStatsResult::class, $result2);
        $this->assertEquals(4, $result2->getMin());

        $facets = $result2->getFacets();

        /** @var FacetValue $facet */
        $facet = $facets['fieldC']['value1'];
        $this->assertInstanceOf(FacetValue::class, $facet);
        $this->assertEquals(5, $facet->getMin());
    }

    /**
     * @test
     */
    public function shouldParseNoData(): void
    {
        $result = $this->parser->parse($this->createMock(Query::class), new \stdClass(), []);
        $this->assertEmpty(count($result));
    }
}
