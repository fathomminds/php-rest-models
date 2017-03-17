[![Scrutinizer Code Quality](https://scrutinizer-ci.com/b/fathomminds/php-rest-models/badges/quality-score.png?b=master&s=815eeac557c3f9f618b3eae4c18875aea4bbd178)](https://scrutinizer-ci.com/b/fathomminds/php-rest-models/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/b/fathomminds/php-rest-models/badges/coverage.png?b=master&s=3c2853b29fb2d31cab3f858963ba1f4ff073fdd8)](https://scrutinizer-ci.com/b/fathomminds/php-rest-models/?branch=master) [![Build Status](https://scrutinizer-ci.com/b/fathomminds/php-rest-models/badges/build.png?b=master&s=65abd3cee7e10d606760fe3674b8fe9a04099e65)](https://scrutinizer-ci.com/b/fathomminds/php-rest-models/build-status/master)

# PHP REST MODELS #

Framework independent PHP REST models with schema validation and multiple database engine support.

## Database engines supported ##

* [Clusterpoint](https://www.clusterpoint.com)
* ~~[DynamoDB](https://aws.amazon.com/dynamodb)~~

## Requirements ##

* PHP 7+
* Composer
* Use with Clusterpoint: [Clusterpoint V4 PHP client](https://github.com/clusterpoint/php-client-api)
* ~~Use with DynamoDB: [AWS SDK for PHP](https://github.com/aws/aws-sdk-php)~~
* For generating code coverage report: [Xdebug PHP extension](https://xdebug.org)

## Install ##

`composer require fathomminds/php-rest-models`

## Configuration ##

* Use with Clusterpoint
* ~~Use with DynamoDB~~

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

Run PHPUnit

`phpunit`

## License ##

Licensed under the MIT license. See [LICENSE](./LICENSE)