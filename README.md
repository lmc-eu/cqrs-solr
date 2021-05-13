LMC CQRS Solr extension
=======================

[![cqrs-types](https://img.shields.io/badge/cqrs-types-purple.svg)](https://github.com/lmc-eu/cqrs-types)
[![Latest Stable Version](https://img.shields.io/packagist/v/lmc/cqrs-solr.svg)](https://packagist.org/packages/lmc/cqrs-solr)
[![Tests and linting](https://github.com/lmc-eu/cqrs-solr/actions/workflows/tests.yaml/badge.svg)](https://github.com/lmc-eu/cqrs-solr/actions/workflows/tests.yaml)
[![Coverage Status](https://coveralls.io/repos/github/lmc-eu/cqrs-solr/badge.svg?branch=main)](https://coveralls.io/github/lmc-eu/cqrs-solr?branch=main)

> A library containing base implementations to help with Solr Queries and Commands.
> This library is an extension for [CQRS/Bundle](https://github.com/lmc-eu/cqrs-bundle) and adds support for `Solarium` requests.

## Table of contents
- [Installation](#installation)
- Queries
    - [Query](#query)
    - [Query Handlers](#query-handlers)
- Commands
    - [Send Command Handlers](#send-command-handlers)
    - [Command](#command)
- [Response Decoders](#response-decoders)
- [Profiler Formatters](#profiler-formatters)
- [Value Objects](#value-objects)
- [Query Builder](#query-builder)

## Installation
```shell
composer require lmc/cqrs-solr
```

**NOTE**: It also requires [Solarium](https://github.com/solariumphp/solarium) and [Solarium bundle](https://github.com/nelmio/NelmioSolariumBundle), which are directly required by this library.

## Query
Query is a request which fetch a data without changing anything. [See more here](https://github.com/lmc-eu/cqrs-types#query-interface)

It is allowed and recommended to use a `InjectSolrClientInterface` with a Solr queries, so you don't need to worry about a `Solarium Client`, you will simply get it automatically.

### AbstractSolrQuery
A base Solr Query, it abstracts and predefine some of a most used features with this kind of a Query.

It implements a `ProfileableInterface` and `CacheableInterface` features.

| Method | Type | Description |
| ---    | ---  | ---         |
| `getRequestType` | **final** | It declares a request type of a query to be a `SolrRequest` |
| `createRequest` | abstract | It declares a base `QueryInterface` method to return a `SolrRequest` type. |
| `getRequestUrl` | *base* | It's a predefined method which creates a request and build a solarium request to return a final url. |
| `getCacheTime` | *base* | It returns a default value for a cache time of 30 minutes. |
| `getCacheKey` | *base* | It creates a `CacheKey` out of an `Solr endpoint`, a static class name (*your implementation class name*) and a `md5` representation of a final `url`, which should create a unique enough cache key for most queries. |
| `getProfilerId` | *base* | It's a predefined creating of profiler id for a Solr query. It creates a profiler id based on a final `url`. |
| `getProfilerData` | *base* | If you overwrite this method, you can specify additional profiler data. Default is `null` (*no data*). |
| `getEndpoint` | *base* | It returns a `Solr endpoint` for your Query. |
| `__toString` | *base* | It's a predefined casting a Query into string, it returns a string representation of a final `url`. |

### AbstractSolrSelectQuery
A base Solr **Select** Query, it abstracts and predefine some of a most used features with this kind of a Query.

It extends a base `AbstractSolrQuery` and predefine some abstract methods. It also adds `InjectSolrClientInterface` feature, since it needs a `Solarium Client` to create a `Select Request`.

| Method | Type | Description |
| ---    | ---  | ---         |
| `setOffset` | *base* | It simply sets an offset (*where should select start*) for a Solr select request. |
| `setLimit` | *base* | It simply sets a limit (*number of rows*) for a Solr select request. |
| `createRequest` | **final** | It creates a `SolrRequest` with a prepared `Solarium Select`. |
| `prepareSelect` | abstract | It requires you prepare a Solr select request. What do you actually wants to select - set of fields and other select properties. |

### BuilderPrototypeQuery
This is a special type of a `SolrSelectQuery` it also extends an `AbstractSolrSelectQuery` and implement `prepareSelect` method with a usage of [QueryBuilder applicators](#query-builder).

It is a Query, which `QueryBuilder` creates for you.

## Query Handlers
It is responsible for handling a specific Query request and passing a result into `OnSuccess` callback. [See more here](https://github.com/lmc-eu/cqrs-types#query-handler-interface).

### Solr Query Handler
This handler supports `Lmc\Cqrs\Solr\ValueObject\SolrRequest` (see [SolrRequest](#solrrequest)) and handles it into `Solarium\Core\Query\Result\ResultInterface`.

It also prepares a Query implementing a `InjectSolrClientInterface` by injecting a `Solarium Client` into a Query so it is not required for you to inject a `Solarium Client` into a query by yourself.

---

## Value Objects

### SolrField
It is a representation of any data passed by user to Solr (eg. in query or a list of fields returned by Solr)

### SolrRequest
It is a simple Value object containing an `Abstract Solarium Query` and optionally a `Solr endpoint`.

---

## Query Builder
> Query builder is an abstraction above a Solarium Select Query set up.

The idea is that you have just a data, used in select, stored in an entity. The entity stands for what you want to select and how. And according to interfaces, that this entity implements, the data is passed into a Select Query.

Query Builder builds a [BuilderPrototypeQuery](#builderprototypequery) which is an instance of `QueryInterface` and is useable in [CQRS/QueryFetcher](https://github.com/lmc-eu/cqrs-types#query-fetcher-interface) with all supported features.

### Example
Imagine you need to select 30 `Persons` with fields `Name` and `Age` by `search input`, stored in a Solr, you would have something like:
```php
$searchInput = $_GET['search'];

$selectPersons = $client->createSelect();
$selectPersons->getEDisMax()->setQueryFields('name^100 age^50');
$selectPersons->setQuery($searchInput);
$selectPersons->setNumberOfRows(30);

$result = $client->execute($selectPersons);
```

With direct Solarium usage you need to create a Select Query yourself and remember all setters and stuff.

Now Query Builder offers a predefined applicators, which knows how the Select query is built and just need a data for the select query.

Example above using a Query Builder would look something like:
```php
class PersonSearch implements FulltextInterface
{
    private string $searchInput;

    public function __construct(string $searchInput)
    {
        $this->searchInput = $searchInput;
    }

    public function getKeywords(): array
    {
        return explode(' ', $this->searchInput);
    }

    public function getNumberOfRows(): int
    {
        return 30;
    }

    public function getQueryFields(): array
    {
        return [
            'name^100',
            new SolrField('age', '', 0, 50),     // you can also use a SolrField value object, so you don't need to remember how is a prioritized value built
        ];
    }

    public function isEDisMaxEnabled(): bool
    {
        return true;
    }

    // Note: there are more methods, you need to implement, but we want to keep this example simple as possible. If you don't need other functionality, simply return null or empty variant from a method.
}

$searchInput = $_GET['search'];

$selectPersonsEntity = new PersonSearch($searchInput);
$selectPersonsQuery = $queryBuilder->buildQuery($selectPersonsEntity);

$result = $queryFetcher->fetchAndReturn($selectPersonsQuery);
```

### Entity Interface
It is a definition interface for a specific feature set you want a query to have.

**Note**: It is not a complete set of Solr/Solarium Select features, it is just our most used features.

- `EntityInterface`
    - A base interface for all features, which adds a `getFields` and `getNumberOfRows` base methods. |
- `FacetsInterface`
- `FilterInterface`
- `FiltersInterface`
- `FulltextBigramInterface`
- `FulltextBoostInterface`
- `FulltextInterface`
- `GroupingFacetInterface`
- `GroupingInterface`
- `ParameterizedInterface`
- `SortInterface`
- `StatsInterface`

### Applicators
Applicator is a service which can apply a specific set of data into a Solarium select query based on implemented Interface.
It must implement a `ApplicatorInterface`.

You can implement your own applicator if you want to mix features or simply use a feature, which does not have an applicator yet.

#### ApplicatorInterface
It is an interface, which all applicators must implement.
It specifies which Entity is current applicator supporting and it can apply its data into Solarium request.
It should be able to skip setting a value, if it is empty.

#### Applicator Factory
It is a service with all defined applicators, its purpose is to return all applicators supporting a given Entity.
It is used inside a `QueryBuilder` to get a list of applicators, which needs to be applied on Select Query.

#### List of all applicators
- `EntityApplicator`
- `FacetsApplicator`
- `FilterApplicator`
- `FiltersApplicator`
- `FulltextApplicator`
- `FulltextBigramApplicator`
- `FulltextBoostApplicator`
- `GroupingApplicator`
- `GroupingFacetApplicator`
- `ParameterizedApplicator`
- `SortApplicator`
- `StatsApplicator`
