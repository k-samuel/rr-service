# Running tests

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
php ./vendor/bin/phpcs --standard=PSR12 ./src
```

PHP CS Fixer
```
php ./vendor/bin/php-cs-fixer fix src
```