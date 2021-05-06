<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\ValueObject;

use PHPUnit\Framework\TestCase;

class SolrFieldTest extends TestCase
{
    /**
     * @dataProvider fieldDataProvider
     *
     * @test
     */
    public function shouldAssembleSolrFieldString(string $field, string $localParameter, int $proximity, int $boost, string $expectedString): void
    {
        $solrField = new SolrField($field, $localParameter, $proximity, $boost);
        $this->assertEquals($expectedString, (string) $solrField);
    }

    public function fieldDataProvider(): array
    {
        return [
            'Only field' => [
                'field' => 'field_str',
                'localParameter' => '',
                'proximity' => 0,
                'boost' => 0,
                'expectedString' => 'field_str',
            ],
            'Keyword and proximity' => [
                'field' => 'Klíčové "slovo"',
                'localParameter' => '',
                'proximity' => 10,
                'boost' => 0,
                'expectedString' => '"Klíčové \"slovo\""~10',
            ],
            'Keyword with local parameter' => [
                'field' => 'Klíčové "slovo"',
                'localParameter' => '{!edismax qf=field}',
                'proximity' => 0,
                'boost' => 0,
                'expectedString' => '{!edismax qf=field}Klíčové \"slovo\"',
            ],
            'Field with boost' => [
                'field' => 'field_str',
                'localParameter' => '',
                'proximity' => 0,
                'boost' => 100,
                'expectedString' => 'field_str^100',
            ],
            'Keyword with local parameter and proximity and boost' => [
                'field' => 'Klíčové "slovo"',
                'localParameter' => '{!edismax qf=field}',
                'proximity' => 10,
                'boost' => 100,
                'expectedString' => '{!edismax qf=field}"Klíčové \"slovo\""~10^100',
            ],
        ];
    }
}
