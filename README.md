[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fathomminds/php-rest-models/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fathomminds/php-rest-models/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/fathomminds/php-rest-models/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/fathomminds/php-rest-models/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/fathomminds/php-rest-models/badges/build.png?b=master)](https://scrutinizer-ci.com/g/fathomminds/php-rest-models/build-status/master) [![Code Climate](https://codeclimate.com/github/fathomminds/php-rest-models/badges/gpa.svg)](https://codeclimate.com/github/fathomminds/php-rest-models) [![Issue Count](https://codeclimate.com/github/fathomminds/php-rest-models/badges/issue_count.svg)](https://codeclimate.com/github/fathomminds/php-rest-models)

# PHP REST MODELS #

Framework independent PHP REST models with schema validation and multiple database engine support.

## Database engines supported ##

* [Clusterpoint](https://www.clusterpoint.com)
* [DynamoDB](https://aws.amazon.com/dynamodb)

## Usage ##

1. **DEFINE THE SCHEMA**: Define schema in a single location [How to create a schema?](./documentation/howto/schema.md)
2. **DEFINE THE REST OBJECT**: Set the corresponding Schema and define the database properties (table name, primary key, indexes) [How to create a REST Object?](./documentation/howto/object.md)
3. **DEFINE THE MODEL**: Simply set the corresponding REST Object [How to create a Model?](./documentation/howto/model.md)
4. **IMPLEMENT BUSINESS LOGIC**: the package implements the CRUD operations, so your Model is clean and you can focus on the application behaviour [How to implement the business logic?](./documentation/howto/business-logic.md)
5. **USE THE MODELS** in your application [How to use the Models?](./documentation/howto/use-models.md)

## Features ##

* REST operations (GET, POST, PUT, DELETE) implemented for the supported databases under the hood
* Basic model operations
    * Model::create()
    * Model::update()
    * Model::one($resourceId)
    * Model::all()
    * Model::delete()
    * Model::getProperty($propertyName)
    * Model::setProperty($propertyName, $propertyValue)
    * Model::createFromObject($stdClass)
    * Model::validate()
* ~~Filtering and pagination~~
* Easily customizable model behaviour
    * add any business logic
    * extend the schema validation with consistency validation
    * write complex queries in model methods directly with the database API to maximize performance
* Easily customizable schema validation
    * validators can implement any complex rules of your choice
    * use base validator classes from the package and extend them as you need
* JOINS AND RELATIONS BETWEEN MODELS ARE INTENTIONALLY NOT PART OF THE IMPLEMENTATION

## Example application ##

Todo Application: [REPOURL](REPOURL)

## Requirements ##

* PHP 7+
* Composer
* Use with Clusterpoint: [Clusterpoint V4 PHP client](https://github.com/clusterpoint/php-client-api)
* Use with DynamoDB: [AWS SDK for PHP](https://github.com/aws/aws-sdk-php)
* For generating code coverage report: [Xdebug PHP extension](https://xdebug.org)

## Install ##

`composer require fathomminds/php-rest-models`

You must install the Database Engine specific packages in your project. (Left out from dependencies intentionally to allow installing only the required one.)

If you use Clusterpoint: `composer require clusterpoint/php-client-api-v4`

If you use DynamoDb: `composer require aws/aws-sdk-php`

You can use both, in such case you need to install both DB package.

## Configuration ##

* Use with Clusterpoint: [Clusterpoint configuration](./documentation/clusterpoint-config.md)
* Use with DynamoDB: [DynamoDb configuration](./documentation/dynamodb-config.md)

## Contribution ##

* Fork the repository
* Make modifications in the code
* Create a pull request targeting develop branch
* Provide a meaningful description what the changes do and why they are needed
* Don't forget to write unit tests
* [Pull Request best practices](http://blog.ploeh.dk/2015/01/15/10-tips-for-better-pull-requests)

## Running tests ##

Clone the repository and install dependencies:

`composer install`

Run unit tests:

`vendor/bin/phpunit`

Test coverage report is logged to folder: `log`

Run integration tests:

`vendor/bin/phpunit --configuration phpunit-integration-test.xml`

Integration tests will interact with real databases. You need to follow the configuration steps to be able to run these tests. The tests use the example FooSchema class for both databases. Please make sure the required objects (tables and indexes) are created before executing integration tests.

## HOW TO ##

* [Create a Schema?](./documentation/howto/schema.md)
* [Create a Rest Object?](./documentation/howto/object.md)
* [Create a Model?](./documentation/howto/model.md)
* [Implement business logic?](./documentation/howto/business-logic.md)
* [Use the models?](./documentation/howto/use-models.md)

## License ##

Licensed under the MIT license. See [LICENSE](./LICENSE)
