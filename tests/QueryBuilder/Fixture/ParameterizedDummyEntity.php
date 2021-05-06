<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Fixture;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\ParameterizedInterface;

class ParameterizedDummyEntity implements ParameterizedInterface
{
    public function getNumberOfRows(): int
    {
        return 20;
    }

    public function getFields(): array
    {
        return [];
    }

    public function getParams(): array
    {
        return [
            'paramField' => 'paramValue',
        ];
    }
}
