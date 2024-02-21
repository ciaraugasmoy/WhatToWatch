#!/bin/bash

RABBITMQ_HOST="" #DB IP
RABBITMQ_PORT="15672"
RABBITMQ_USER="guest"
RABBITMQ_PASS="guest"
EXCHANGE_NAME="broker_to_db_exchange"
QUEUE_NAME="broker_to_db_queue"
ROUTING_KEY="*"

# Declare Exchange with topic type
curl -i -u $RABBITMQ_USER:$RABBITMQ_PASS -H "content-type:application/json" \
    -X PUT -d "{\"type\":\"topic\",\"auto_delete\":false,\"durable\":true}" \
    http://$RABBITMQ_HOST:$RABBITMQ_PORT/api/exchanges/%2F/$EXCHANGE_NAME

# Declare Queue
curl -i -u $RABBITMQ_USER:$RABBITMQ_PASS -H "content-type:application/json" \
    -X PUT -d "{\"auto_delete\":false,\"durable\":true}" \
    http://$RABBITMQ_HOST:$RABBITMQ_PORT/api/queues/%2F/$QUEUE_NAME

# Bind Queue to Exchange with Routing Key
curl -i -u $RABBITMQ_USER:$RABBITMQ_PASS -H "content-type:application/json" \
    -X POST -d "{\"routing_key\":\"$ROUTING_KEY\"}" \
    http://$RABBITMQ_HOST:$RABBITMQ_PORT/api/bindings/%2F/e/$EXCHANGE_NAME/q/$QUEUE_NAME

echo "RabbitMQ setup completed. Exchange: $EXCHANGE_NAME, Queue: $QUEUE_NAME, Routing Key: $ROUTING_KEY"
