FROM python:3.12.0a1-bullseye

RUN mkdir -p /usr/src/tuts

WORKDIR /usr/src/tuts

RUN apt-get update &&  \
    apt-get install -y vim && \
    apt-get install -y coreutils

RUN touch queue.log

RUN pip install pika

CMD ["python", "./sendHelloMessage.py"]
