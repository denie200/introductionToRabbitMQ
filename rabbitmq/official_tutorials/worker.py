#!/usr/bin/env python
import pika
import time
import logging

print("START CONSUMING")
time.sleep(3)

connection = pika.BlockingConnection(pika.ConnectionParameters('rabbit'))
channel = connection.channel()

channel.queue_declare(queue='hello')

def callback(ch, method, properties, body):
    logging.basicConfig(filename='queue.log', encoding='utf-8', level=logging.DEBUG)
    logging.info(" [x] Recieved from 1 at %r" % body)
    time.sleep(body.count(b'.'))
    ch.basic_ack(delivery_tag = method.delivery_tag)
    print(" [x] Done Worker 1")

channel.basic_qos(prefetch_count=1)
channel.basic_consume(queue='hello', on_message_callback=callback)

print(' [*] Waiting for messages. To exit press CTRL+C')
channel.start_consuming()

try :
    while True:
        time.sleep(1)
finally:
    logging.info('Closing worker1')
