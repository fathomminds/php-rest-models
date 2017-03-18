[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fathomminds/php-rest-models/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fathomminds/php-rest-models/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/fathomminds/php-rest-models/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/fathomminds/php-rest-models/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/fathomminds/php-rest-models/badges/build.png?b=master)](https://scrutinizer-ci.com/g/fathomminds/php-rest-models/build-status/master) [![Code Climate](https://codeclimate.com/github/fathomminds/php-rest-models/badges/gpa.svg)](https://codeclimate.com/github/fathomminds/php-rest-models) [![Issue Count](https://codeclimate.com/github/fathomminds/php-rest-models/badges/issue_count.svg)](https://codeclimate.com/github/fathomminds/php-rest-models)

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
