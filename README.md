
AWS Forum API
========================

[![Build Status](https://travis-ci.org/aindenko/awsforumapi.svg?branch=master)](https://travis-ci.org/aindenko/awsforumapi)
[![Coverage Status](https://coveralls.io/repos/github/aindenko/awsforumapi/badge.svg?branch=code-review)](https://coveralls.io/github/aindenko/awsforumapi?branch=code-review)


Small REST API built on Symfony 3 project.
Requires PHP7.0 and S3 bucket.

Documentation: Swagger compatible docs/api.yml

Installation:

1. clone it
2. run composer to install it (I prefer global composer intallation) : composer install
3. cp app/config/parameters.yml.dist app/config/parameters.yml
3. create database and setup params in app/config/parameters.yml
3. migrate migrations : bin/console doctrine:migrations:migrate 
    
For testing just run ./vendor/bin/phpunit
