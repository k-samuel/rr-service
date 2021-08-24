# Caching

**Connection\Manager** - Connections manager accessible in your service base class

## Runtime Cache ##

Runtime level inmemory cache accessible in your service base class

Runtime cache TTL option runtime_reset_timeout can be changed in server.php
```
$this->connectionManager->getRuntimeCache();
```
Note that runtime cache stores independently in php workers memory (not shared)

**There is a reasonable limit on the use of memory in processes, you should not allow it to litter, otherwise the workers will start leaking.**

**Simple decision logic: If the data is needed in all workers and does not change based on the context of the request,
we store in runtime, otherwise Memcached.**

**Store user related data - only in external storages like Memcached. Runtime cache of user related data can cause memory leaks**


## Cache Connection ##
Caching adapters (memcached by default)

```
// configuration file at config/[dev/production]/connection/cache/connection_name.php
$this->connectionManager->getCache('connection_name');
```
