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
php ./vendor/bin/phpcs --standard=PSR12 ./src ./services ./tests/unit
php ./vendor/bin/phpcs --standard=Squiz --sniffs=Squiz.NamingConventions.ValidVariableName ./src ./services ./tests/unit
```

PHP CS Fixer
```
php ./vendor/bin/php-cs-fixer fix src
```