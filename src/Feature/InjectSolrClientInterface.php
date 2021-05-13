<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Feature;

use Solarium\Core\Client\Client;

interface InjectSolrClientInterface
{
    public function setSolrClient(Client $client): void;
}
