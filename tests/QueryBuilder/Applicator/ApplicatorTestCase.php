<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\AbstractSolrTestCase;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\Query\BuilderPrototypeQuery;

class ApplicatorTestCase extends AbstractSolrTestCase
{
    protected function getApplicator(string $applicatorClass, EntityInterface $entity): ApplicatorInterface
    {
        /** @var ApplicatorInterface $applicator */
        $applicator = new $applicatorClass();
        $this->assertTrue($applicator->supportEntity($entity));
        $applicator->setEntity($entity);

        return $applicator;
    }

    /**
     * @param ApplicatorInterface[] $applicators
     */
    protected function getCustomQueryUri(array $applicators = []): string
    {
        $query = new BuilderPrototypeQuery($applicators);
        $query->setSolrClient($this->createSolrClient());

        return urldecode($query->__toString());
    }

    protected function parseQueryUriParameters(string $queryUri): array
    {
        $parameters = [];
        $pairs = explode('&', (string) parse_url($queryUri, PHP_URL_QUERY));

        foreach ($pairs as $pair) {
            [$key, $value] = explode('=', $pair, 2);

            $parameters[$key] = $value;
        }

        return $parameters;
    }

    protected function assertParameterExists(string $expectedKey, array $parameters): string
    {
        $this->assertArrayHasKey(
            $expectedKey,
            $parameters,
            sprintf(
                'Parameter "%s" does not exists in [%s].',
                $expectedKey,
                implode(', ', array_keys($parameters))
            )
        );

        return $parameters[$expectedKey];
    }
}
