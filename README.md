RoadRunner services 
====

[Docs](docs/index.md)

Installation
```` 
// install PHP dependencies
composer install

// Road Runner instalaltion
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

###Tests

PHPStan
```
./vendor/bin/phpstan analyse src services tests
```

PHPUnit
```
./vendor/bin/phpunit
```

PHP CS
```
php ./vendor/bin/phpcs --standard=PSR12 ./src
```

PHP CS Fixer
```
php ./vendor/bin/php-cs-fixer fix src
```