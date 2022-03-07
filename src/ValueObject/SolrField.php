<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\ValueObject;

use Solarium\Core\Query\Helper;

/**
 * SolrField is a representation of any data passed by user to Solr (eg. in query or a list of fields returned by Solr)
 */
class SolrField implements \Stringable
{
    public function __construct(
        private string $value,
        private string $localParameter = '',
        private int $proximity = 0,
        private int $boost = 0
    ) {
        $this->value = $this->escapePhrase($value);
    }

    public function __toString(): string
    {
        $value = $this->value;

        if ($this->proximity) {
            $value = sprintf('"%s"~%d', $value, $this->proximity);
        }

        if ($this->boost) {
            $value .= '^' . $this->boost;
        }

        if ($this->localParameter) {
            $value = $this->localParameter . $value;
        }

        return $value;
    }

    private function escapePhrase(string $phrase): string
    {
        $phrase = (new Helper())->escapePhrase($phrase);

        // Remove unwanted double quotes added by the escapePhrase method
        if (mb_substr($phrase, 0, 1) === '"' && mb_substr($phrase, -1) === '"') {
            $phrase = mb_substr($phrase, 1); // remove first character
            $phrase = mb_substr($phrase, 0, -1); // remove last character
        }

        return $phrase;
    }
}
