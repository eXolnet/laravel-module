# Changelog

This project follows [Semantic Versioning 2.0.0](http://semver.org/).

## <a name="unreleased"></a>Unreleased 

## <a name="v1.1.0"></a>[v1.1.0](https://github.com/exolnet/laravel-module/tree/v1.1.0) (2015-10-28)
[Full Changelog](https://github.com/exolnet/laravel-module/compare/v1.0.0...v1.1.0)

### Added
* Translatable trait
* Unique locale validator (validate that a translatable model has a unique property in a target locale)
* TestCase, TestCaseFunctional and TestCaseUnit classes
* An initial TestCaseFunctionalResource targeted at testing resource controllers
* Initial implementation of a generic repository class
* ValidationException::fromValidator to create a ValidationException from a validator object
* Generic /health page to check if the application is alive

### Changed
* Moved modules from / to src/Exolnet
* Rulebuilder will now replace $lang in the value of the given $key => $value array
* The test database migrator will now run the TestSeeder seeder instead of the default DatabaseSeeder seeder

### Removed
* Session package (it was dead code)

## <a name="v1.0.0"></a>[v1.0.0](https://github.com/exolnet/laravel-module/tree/v1.0.0) (2015-07-09)

Initial release
