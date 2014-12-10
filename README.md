# What is RAPL?

RAPL (RESTful API Persistence Layer) is a RESTful variant of [Doctrine's ORM](http://www.doctrine-project.org/projects/orm.html).
It implements the same interfaces, but allows you to store and retrieve entities from a remote (RESTful) API instead of from the database.

## Why use RAPL?

 * RAPL abstracts the REST architecture, the HTTP protocol and the serialization of objects for you. All you have to do
   is to create your entity classes and to map them to the API (using mapping configuration).
 * If you are maintaining an API and you want to provide a client library for it, you can simply build it on top of
   RAPL.

## Code Quality

[![Build Status](https://img.shields.io/travis/rapl/rapl.svg?style=flat)](https://travis-ci.org/rapl/rapl)
[![Coverage Status](https://img.shields.io/coveralls/rapl/rapl.svg?style=flat)](https://coveralls.io/r/rapl/rapl)
[![Code Quality](https://img.shields.io/scrutinizer/g/rapl/rapl.svg?style=flat)](https://scrutinizer-ci.com/g/rapl/rapl/)

## Installation

RAPL can be installed using [Composer](https://getcomposer.org/):

```bash
composer require rapl/rapl
```

This will add RAPL to the dependency list of your main project's composer.json.

## Setting up

```php
<?php

require_once 'vendor/autoload.php';

$connection = new \RAPL\RAPL\Connection\Connection('http://example.com/api/');
$connection->addSubscriber(new \Your\Authentication\Subscriber());

$configuration = new \RAPL\RAPL\Configuration();
$paths         = array(__DIR__ . '/config');
$driver        = new \RAPL\RAPL\Mapping\Driver\YamlDriver($paths, '.rapl.yml');
$configuration->setMetadataDriver($driver);

$manager = new \RAPL\RAPL\EntityManager($connection, $configuration);
```

## Usage

Once you have set everything up correctly, you can start using RAPL. This will feel very familiar if you have worked
with Doctrine before.

```php
$repository = $manager->getRepository('Your\Entity\Class');

// Get entity with id 3
$entity = $repository->find(3);

// Or get all of them
$entities = $repository->findAll();
