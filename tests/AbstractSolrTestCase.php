<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Solarium\Client;
use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Select\Query\Query;

abstract class AbstractSolrTestCase extends TestCase
{
    /** @var Client|MockObject */
    protected Client $client;

    /** @before */
    protected function setUpClient(): void
    {
        $this->client = $this->createMock(Client::class);
    }

    protected function createSolrClient(): Client
    {
        return new Client(
            $this->createMock(AdapterInterface::class),
            $this->createMock(EventDispatcherInterface::class)
        );
    }

    /** @return ResultInterface|MockObject */
    protected function prepareResult(array $data): ResultInterface
    {
        $result = $this->createMock(ResultInterface::class);

        $result->expects($this->any())
            ->method('getData')
            ->willReturn($data);

        return $result;
    }

    /** @return Query|MockObject */
    protected function prepareSelect(?array $expectedFields = null): Query
    {
        $select = $this->createMock(Query::class);

        if ($expectedFields !== null) {
            $select->expects($this->any())
                ->method('addFields')
                ->with($expectedFields)
                ->willReturnSelf();
        }

        return $select;
    }

    protected function expectClientToCreateQueryOnce(Query $query): void
    {
        $this->client->expects($this->once())
            ->method('createSelect')
            ->willReturn($query);
    }

    protected function expectClientToExecuteSelectQueryOnce(
        Query $query,
        ?string $endpoint,
        ResultInterface $result
    ): void {
        $this->client->expects($this->once())
            ->method('execute')
            ->with($query, $endpoint)
            ->willReturn($result);
    }

    protected function assertQueryStringContainsPart(string $expected, string $queryString): void
    {
        $message = function () use ($expected, $queryString) {
            [$_, $queryString] = explode('?', $queryString);
            $parts = explode('&', $queryString);

            return sprintf(
                "Expected \"%s\" was not found in query string: \"%s\"\nwith parts:\n - %s",
                urldecode($expected),
                urldecode($queryString),
                implode("\n - ", array_map(urldecode(...), $parts))
            );
        };

        $this->assertStringContainsString(
            $expected,
            $queryString,
            $message()
        );
    }
}
