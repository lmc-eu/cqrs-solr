<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Solarium\QueryType\Select\Result;

use PHPUnit\Framework\TestCase;

class CountDistinctStatsResultTest extends TestCase
{
    /**
     * @dataProvider countDistinctProvider
     *
     * @test
     */
    public function shouldGetCountDistinct(array $stats, ?int $expectedCountDistinct): void
    {
        $result = new CountDistinctStatsResult('field_1', $stats);
        $this->assertSame($expectedCountDistinct, $result->getCountDistinct());
    }

    public function countDistinctProvider(): array
    {
        return [
            // $stats, $expectedCountDistinct
            'countDistinct is set' => [['countDistinct' => '20'], 20],
            'countDistinct is not set' => [[], null],
        ];
    }
}
