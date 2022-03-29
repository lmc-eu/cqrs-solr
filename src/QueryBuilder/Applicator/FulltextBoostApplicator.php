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
        if (!$this->entity->isEDisMaxEnabled()) {
            return;
        }

        $boostQuery = $this->entity->getBoostQuery();
        $phraseSlop = $this->entity->getPhraseSlop();

        if (empty($boostQuery) && $phraseSlop === null) {
            return;
        }

        if ($this->entity->useEDisMaxGlobally()) {
            if (!empty($boostQuery)) {
                $query->getEDisMax()->setBoostQuery($boostQuery);
            }

            if ($phraseSlop !== null) {
                $query->getEDisMax()->setPhraseSlop($phraseSlop);
            }
        } else {
            if (!empty($boostQuery)) {
                $query->getLocalParameters()->offsetSet('bq', 'bq=$boostQuery');
                $query->addParam('boostQuery', $this->entity->getBoostQuery());
            }

            if ($phraseSlop !== null) {
                $query->getLocalParameters()->offsetSet('ps', 'ps=$phraseSlop');
                $query->addParam('phraseSlop', $this->entity->getPhraseSlop());
            }
        }
    }
}
