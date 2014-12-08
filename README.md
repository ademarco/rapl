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

You need [Composer](https://getcomposer.org/) in order to install RAPL:

```bash
composer require rapl/rapl
```
