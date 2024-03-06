<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Solarium\QueryType\Select\Query\Component;

use Lmc\Cqrs\Solr\Solarium\QueryType\Select\ResponseParser\CountDistinctStatsResponseParser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CountDistinctStatsComponentTest extends TestCase
{
    #[Test]
    public function shouldGetCorrectResponseParser(): void
    {
        $this->assertInstanceOf(
            CountDistinctStatsResponseParser::class,
            (new CountDistinctStatsComponent())->getResponseParser()
        );
    }
}
