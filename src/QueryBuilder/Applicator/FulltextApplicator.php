<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FulltextInterface;
use Solarium\QueryType\Select\Query\Query;

class FulltextApplicator implements ApplicatorInterface
{
    private FulltextInterface $entity;
    private Query $query;

    public function supportEntity(EntityInterface $entity): bool
    {
        return $entity instanceof FulltextInterface;
    }

    /**
     * @param FulltextInterface $entity
     */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    public function applyOnQuery(Query $query): void
    {
        $this->query = $query;

        $this
            ->addKeywords()
            ->setDefaults();

        if ($this->entity->isEDisMaxEnabled()) {
            $this->setEdisMax();
        }
    }

    private function addKeywords(): self
    {
        $keywords = $this->entity->getKeywords();

        if (!empty($keywords)) {
            $searchQuery = implode(' ', $keywords);

            $this->query->setQuery($searchQuery);
        }

        return $this;
    }

    private function setDefaults(): self
    {
        $this->query->setQueryDefaultOperator($this->entity->getDefaultQueryOperator());

        return $this;
    }

    private function setEdisMax(): self
    {
        $edismax = $this->query->getEDisMax();
        $edismax
            ->setQueryFields(implode(' ', $this->entity->getQueryFields()))
            ->setPhraseFields(implode(' ', $this->entity->getPhraseFields()))
            ->setQueryAlternative($this->entity->getQueryAlternative())
            ->setTie($this->entity->getTie());

        if (($minimumMatch = $this->entity->getMinimumMatch()) !== null) {
            $edismax->setMinimumMatch($minimumMatch);
        }

        return $this;
    }
}
