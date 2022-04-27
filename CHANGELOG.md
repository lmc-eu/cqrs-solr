# Changelog

<!-- There should always be "Unreleased" section at the beginning. -->

## Unreleased

## 3.0.0 - 2022-04-27
- Require php 8.1
    - [**BC**] Use new language features and change method signatures

## 2.0.0 - 2022-03-30
- Allow setting an edismax as local parameters in query
  - [**BC**] Add method `useEDisMaxGlobally` to `FulltextInterface`
- Set `phraseBigramFields`, `boostQuery`, `phraseSlop` only when `eDisMax` is enabled

## 1.3.0 - 2022-03-29
- Use `*` as a placeholder for all fields in `EntityApplicator`

## 1.2.0 - 2021-08-10
- Add an `$initiator` to `ResponseDecoders` `supports` method

## 1.1.0 - 2021-07-28
- Profile used endpoint details for a query

## 1.0.0 - 2021-05-13
- Initial implementation
