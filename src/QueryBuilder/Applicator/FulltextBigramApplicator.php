<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FulltextBigramInterface;
use Solarium\QueryType\Select\Query\Query;

class FulltextBigramApplicator implements ApplicatorInterface
{
    private FulltextBigramInterface $entity;

    public function supportEntity(EntityInterface $entity): bool
    {
        return $entity instanceof FulltextBigramInterface;
    }

    /** @param FulltextBigramInterface $entity */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    public function applyOnQuery(Query $query): void
    {
        $phraseBigramFields = $this->entity->getPhraseBigramFields();

        if (!empty($phraseBigramFields)) {
            $query->getEDisMax()->setPhraseBigramFields(implode(' ', $phraseBigramFields));
        }
    }
}
