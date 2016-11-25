https://travis-ci.org/aindenko/awsforumapi.svg?branch=master

AWS Forum API
========================

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
