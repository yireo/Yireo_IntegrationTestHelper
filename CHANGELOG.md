# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.0.24] - 25 November 2026
### Changed
- Supported PHP versions are now 8.2 to 8.5: PHP 8.5 is added, PHP 8.0 and 8.1 are dropped

### Fixed
- Enabling the tested module now also enables the modules it depends on (recursively), instead of leaving them disabled after `module:enable --all` when `module-map.json` is missing
- Type error thrown when `phpunit.xml` could not be found
- `integration_tests:generate-module-mapping` reports an error instead of failing silently when the module map cannot be written

### Security
- Shell arguments are escaped when reading the integration test install configuration, and `InstallConfigExec.php` refuses to run outside the CLI
- `integration_tests:check` masks the DB password unless `--show-secrets` is passed
- Constant names and values are XML-escaped when changing `phpunit.xml` constants
- Table name is quoted in `assertTableHasRecordCount()` to prevent SQL injection
- DB, Redis and search engine checks validate their DSN and use connection timeouts
- Invalid install and module configuration files are rejected, and file contents no longer leak into exception messages
- CI scripts and development files are excluded from the Composer dist archive

## [0.0.23] - 13 June 2026
### Fixed
- Bump constraints
- Remove AssertModuleIsRegisteredForReal usage

## [0.0.22] - 13 June 2026
### Fixed
- Add Symfony 7 compat
- Add debugging flag to `assertModuleIs*` calls

## [0.0.21] - 22 October 2025
### Fixed
- Add PHP 8.4 compat

## [0.0.20] - 03 September 2025
### Fixed
- Add support for custom MODULE.json file
- Add GitHub actions

## [0.0.19] - 24 July 2025
### Fixed
- Upgrade PHP 8.4
- Add "dev" composer keyword

## [0.0.18] - 24 July 2025
### Fixed
- Add type hint for PHP 8.4
- Allow server port to be either string or int
- Better detection of deps of deps
- New method addSearchEngine (instead of addElasticSearch)
- Add PHP 8.4

## [0.0.17] - 2022-2025
- Previous releases
