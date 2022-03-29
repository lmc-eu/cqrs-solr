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
            if ($this->entity->useEDisMaxGlobally()) {
                $this->setGlobalEdisMax();
            } else {
                $this->setLocalEdisMax();
            }
        }
    }

    private function addKeywords(): self
    {
        if ($this->entity->isEDisMaxEnabled() && !$this->entity->useEDisMaxGlobally()) {
            return $this;
        }

        if (!empty($keywords = $this->entity->getKeywords())) {
            $searchQuery = implode(' ', $keywords);

            $this->query->setQuery($searchQuery);
        }

        return $this;
    }

    private function setDefaults(): self
    {
        if (!empty($defaultOperator = $this->entity->getDefaultQueryOperator())) {
            $this->query->setQueryDefaultOperator($defaultOperator);
        }

        return $this;
    }

    private function setGlobalEdisMax(): self
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

    private function setLocalEdisMax(): self
    {
        $this->query->setQuery('');

        $localParameters = $this->query->getLocalParameters();
        $localParameters->setType('edismax');

        if (!empty($keyWords = $this->entity->getKeywords())) {
            $localParameters->setLocalValue('$userQuery');
            $this->query->addParam('userQuery', implode(' ', $keyWords));
        }

        if (!empty($queryFields = $this->entity->getQueryFields())) {
            $localParameters->setQueryField('$queryFields');
            $this->query->addParam('queryFields', implode(' ', $queryFields));
        }

        if (!empty($phraseFields = $this->entity->getPhraseFields())) {
            $localParameters['pf'] = 'pf=$phraseFields';
            $this->query->addParam('phraseFields', implode(' ', $phraseFields));
        }

        $localParameters['tie'] = 'tie=' . $this->entity->getTie();
        $localParameters['mm'] = 'mm=' . $this->entity->getMinimumMatch();

        $this->query->addParam('q.alt', $this->entity->getQueryAlternative());

        return $this;
    }
}
