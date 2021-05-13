<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\Query\BuilderPrototypeQuery;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;

class ApplicatorTestCase extends TestCase
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
        $query->setSolrClient(new Client());

        return urldecode($query->__toString());
    }
}
