<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Query;

use Lmc\Cqrs\Solr\Query\AbstractSolrSelectQuery;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\ApplicatorInterface;
use Solarium\QueryType\Select\Query\Query;

final class BuilderPrototypeQuery extends AbstractSolrSelectQuery
{
    /** @var ApplicatorInterface[] */
    private array $applicators;

    /**
     * @param ApplicatorInterface[] $applicators
     */
    public function __construct(array $applicators)
    {
        $this->applicators = $applicators;
    }

    public function prepareSelect(Query $select): Query
    {
        foreach ($this->applicators as $applicator) {
            $applicator->applyOnQuery($select);
        }

        return $select;
    }
}
