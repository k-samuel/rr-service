Application manifest
===
An application is a set of independent services that are launched by a server.

The project is optimized for running with RoadRunner, in development mode it can be launched under apache / nginx

At startup, all the code is initialized even before requests are received, the caches are warmed up


#### Share Nothing #### 
Each route is an independent service that has no shared resources other than a pool of connections to the database and cache.

Class objects should not store state across multiple requests.

If you use external DI containers and builders, factories, make sure that they do not store states.
Or they are dropped on every request.

The basic implementation of the DI container is available in the project code.  [Example](add_service.md)

If you need a configured adapter for connecting a database / cache, etc. as a dependency in the container,
then the container must be re-created for each request, otherwise the connections that were dropped by the connection manager will leak.
