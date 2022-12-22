#!/usr/bin/env python
import pika
import time
import sys

print("START PRODUCING")
time.sleep(2)

connection = pika.BlockingConnection(pika.ConnectionParameters('rabbit'))
channel = connection.channel()

result = channel.queue_declare(queue='hello')

message = ' '.join(sys.argv[1:]) or "Hello World!"

channel.basic_publish(
    exchange='',
    routing_key='hello',
    body=message,
    properties=pika.BasicProperties(
        delivery_mode = pika.spec.PERSISTENT_DELIVERY_MODE
    ))

connection.close()
print("CONNECTION CLOSED")
