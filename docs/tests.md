# Running tests

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