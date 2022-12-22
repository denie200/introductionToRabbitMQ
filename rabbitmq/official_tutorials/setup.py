#!/usr/bin/env python
import pika
import time

# Wait for RabbitMq container to get online
time.sleep(5)

print("START SETUP RABBITMQ")

# Setup the rabbitmq exchanges
connection = pika.BlockingConnection(pika.ConnectionParameters('rabbit'))
channel = connection.channel()

channel.exchange_declare(exchange='logs', exchange_type='direct')

connection.close()
print("Finished setup process")
