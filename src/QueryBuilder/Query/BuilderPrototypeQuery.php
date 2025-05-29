<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Query;

use Lmc\Cqrs\Solr\Query\AbstractSolrSelectQuery;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\ApplicatorInterface;
use Solarium\QueryType\Select\Query\Query;

final class BuilderPrototypeQuery extends AbstractSolrSelectQuery
{
    public const METADATA_KEY_ENTITY = 'entity';

    /**
     * @param ApplicatorInterface[] $applicators
     * @param array<string, string> $metadata
     */
    public function __construct(private array $applicators, private array $metadata = [])
    {
    }

    public function prepareSelect(Query $select): Query
    {
        foreach ($this->applicators as $applicator) {
            $applicator->applyOnQuery($select);
        }

        return $select;
    }

    public function setMetadata(string $key, string $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function getMetadata(string $key): ?string
    {
        return $this->metadata[$key] ?? null;
    }

    public function getProfilerData(): ?array
    {
        $data = [
            'Applicators' => array_map(get_class(...), $this->applicators),
        ];

        if (!empty($this->metadata)) {
            $data['Metadata'] = $this->metadata;
        }

        return parent::getProfilerData() + $data;
    }
}
