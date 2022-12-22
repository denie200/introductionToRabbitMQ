#!/usr/bin/env python
import pika
import sys

print("START LOGGING")

severity = sys.argv[1] if len(sys.argv) > 1 else 'info'

message = ' '.join(sys.argv[2:]) or "No log message given"

connection = pika.BlockingConnection(pika.ConnectionParameters('rabbit'))
channel = connection.channel()

channel.basic_publish(
    exchange='logs',
    routing_key=severity,
    body=message
)

print("Logs sent with log message %r:%r" % (severity, message))
connection.close()

