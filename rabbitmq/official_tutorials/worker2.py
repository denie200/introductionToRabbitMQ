#!/usr/bin/env python
import pika
import time
import logging
import sys

print("START CONSUMING LOGS")
connection = pika.BlockingConnection(pika.ConnectionParameters('rabbit'))
channel = connection.channel()
#
# channel.exchange_declare(exchange='logs', exchange_type='fanout')

result = channel.queue_declare(queue='', exclusive=True)
queue_name = result.method.queue

severities = sys.argv[1:]
if not severities:
    sys.stderr.write("Usage: %s [info] [warning] [error]\n" % sys.argv[0])
    sys.exit(1)

for severity in severities:
    channel.queue_bind(
        exchange='logs', queue=queue_name, routing_key=severity)

print(' [*] Waiting for messages. To exit press CTRL+C')


def callback(ch, method, properties, body):
    print(" [x] %r" % body)

channel.basic_consume(
    queue=queue_name,
    auto_ack=True,
    on_message_callback=callback)

channel.start_consuming()

try :
    while True:
        time.sleep(1)
finally:
    logging.info('Closing worker2')
