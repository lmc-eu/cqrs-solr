<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FulltextBigramInterface;
use Lmc\Cqrs\Solr\ValueObject\LocalParameter;
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
        if (!$this->entity->isEDisMaxEnabled()) {
            return;
        }

        if (!empty($phraseBigramFields = $this->entity->getPhraseBigramFields())) {
            if ($this->entity->useEDisMaxGlobally()) {
                $query->getEDisMax()->setPhraseBigramFields(implode(' ', $phraseBigramFields));
            } else {
                $query->getLocalParameters()->offsetSet('pf2', LocalParameter::withPlaceholder('pf2', '$phraseBigramFields'));
                $query->addParam('phraseBigramFields', implode(' ', $phraseBigramFields));
            }
        }
    }
}
