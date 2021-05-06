<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder;

use Lmc\Cqrs\Solr\QueryBuilder\Applicator\ApplicatorFactory;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\Query\BuilderPrototypeQuery;

class QueryBuilder
{
    private ApplicatorFactory $applicatorFactory;

    public function __construct(ApplicatorFactory $applicatorFactory)
    {
        $this->applicatorFactory = $applicatorFactory;
    }

    public function buildQuery(EntityInterface $entity): BuilderPrototypeQuery
    {
        return new BuilderPrototypeQuery($this->applicatorFactory->getApplicators($entity));
    }

    public function buildQueryWithEndpoint(EntityInterface $entity, string $endpoint): BuilderPrototypeQuery
    {
        $query = $this->buildQuery($entity);
        $query->setEndpoint($endpoint);

        return $query;
    }
}
