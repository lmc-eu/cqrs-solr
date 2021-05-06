<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FulltextBoostInterface;
use Solarium\QueryType\Select\Query\Query;

class FulltextBoostApplicator implements ApplicatorInterface
{
    private FulltextBoostInterface $entity;

    public function supportEntity(EntityInterface $entity): bool
    {
        return $entity instanceof FulltextBoostInterface;
    }

    /** @param FulltextBoostInterface $entity */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    public function applyOnQuery(Query $query): void
    {
        $boostQuery = $this->entity->getBoostQuery();
        $phraseSlop = $this->entity->getPhraseSlop();

        if (!empty($boostQuery)) {
            $query->getEDisMax()->setBoostQuery($boostQuery);
        }

        if ($phraseSlop !== null) {
            $query->getEDisMax()->setPhraseSlop($phraseSlop);
        }
    }
}
