<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Solarium\QueryType\Select\Result;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CountDistinctStatsResultTest extends TestCase
{
    #[Test]
    #[DataProvider('provideCountDistinct')]
    public function shouldGetCountDistinct(array $stats, ?int $expectedCountDistinct): void
    {
        $result = new CountDistinctStatsResult('field_1', $stats);
        $this->assertSame($expectedCountDistinct, $result->getCountDistinct());
    }

    public static function provideCountDistinct(): array
    {
        return [
            // $stats, $expectedCountDistinct
            'countDistinct is set' => [['countDistinct' => '20'], 20],
            'countDistinct is not set' => [[], null],
        ];
    }
}
