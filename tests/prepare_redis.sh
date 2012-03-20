#!/bin/bash

/etc/init.d/redis_6379  stop

rm -rf /var/lib/redis/6379/dump.rdb

/etc/init.d/redis_6379  start 
