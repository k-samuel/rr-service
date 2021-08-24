# Installation
```` 
// intstall composer libraries
composer create project k-samuel/rr-service

// load Road Runner binary for your OS
./vendor/bin/rr get

// start Road Runner in debug mode (default 127.0.0.1:8083)
./rr serve -v -d    
````

Remove development dependencies from vendor directory
```
composer install --no-dev
```

Update composer classmap 
```
composer dump-autoload -o
```

Test request 

http://127.0.0.1:8083/ for RoadRunner  
http://myservice.local/ for Web Server