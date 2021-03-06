[![PHP Version](https://img.shields.io/badge/php-7.4%2B-blue.svg)](https://packagist.org/packages/k-samuel/rr-service)
[![Total Downloads](https://img.shields.io/packagist/dt/k-samuel/rr-service.svg?style=flat-square)](https://packagist.org/packages/k-samuel/rr-service)
![Build and Test](https://github.com/k-samuel/rr-service/workflows/Build%20and%20Test/badge.svg?branch=main&event=push)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/c92b0ab94f6f4fc8ae233372e9f4d351)](https://www.codacy.com/gh/k-samuel/rr-service/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=k-samuel/rr-service&amp;utm_campaign=Badge_Grade)

RoadRunner services (Beta Version)
====

Basic core for running high-performance services with PHP RoadRunner

[Docs](docs/index.md)

Installation
```` 
// install PHP dependencies
composer create-project k-samuel/rr-service

// Get RoadRunner binary
./vendor/bin/rr get

// start Road Runner (default) 127.0.0.1:8083 DEV
./rr serve -d    

// start Road Runner (default) на 127.0.0.1:8083 PROD
./rr serve   

````

Setup only production dependencies
```
composer install --no-dev
```

Rebuild class map
```
composer dump-autoload -o
```

### Tests

PHPStan
```
php ./vendor/bin/phpstan analyse src services tests
```

PHPUnit
```
php ./vendor/bin/phpunit
```

PHP CS
```
php ./vendor/bin/phpcs --standard=PSR12 ./src ./services ./tests/unit
php ./vendor/bin/phpcs --standard=Squiz --sniffs=Squiz.NamingConventions.ValidVariableName ./src ./services ./tests/unit
```

PHP CS Fixer
```
php ./vendor/bin/php-cs-fixer fix ./src
```

### Example requests

http://127.0.0.1:8083/example/api
```
php console.php /worker
```