Hector Bundle
-------------

[![Build Status](https://travis-ci.org/Evaneos/HectorBundle.svg?branch=master)](https://travis-ci.org/Evaneos/HectorBundle)

Symfony Bundle for [Hector](https://github.com/Evaneos/Hector)


## Installation

```bash
composer require evaneos\hector-bundle
```

```php
    public function registerBundles()
    {
        $bundles = array(
			...
            new Evaneos\HectorBundle\HectorBundle()
        );
    }
```

## Configuration

```yaml
hector:
    connections:
        default:
            host: %amqp.host%
            port: %amqp.port%
            login: %amqp.login%
            password: %amqp.password%
            vhost: %amqp.vhost%
            read_timeout: %amqp.read_timeout%
            write_timeout: %amqp.write_timeout%
            connect_timeout: %amqp.connect_timeout%
    exchanges:
        bill:
            flags: 2 #durable
            type: "topic"
            queues:
                bill.process:
                    flags: 2 #durable
                    routing_key: "process"
                bill.process_dropped:
                    flags: 2 #durable
                    routing_key: "bill.dropped"
                bill.logger:
                    flags: 2 #durable
                    routing_key: "event_bus.bill.*"
        bill.delayed:
            flags: 2 #durable
            type: "topic"
            queues:
                bill.process_retry_5:
                    flags: 2 #durable
                    routing_key: "process.retry.5"
                    arguments:
                        x-message-ttl: 300000
                        x-dead-letter-exchange: "bill.delayed"
                        x-dead-letter-routing-key: "process.retry.10"
                bill.process_retry_10:
                    flags: 2 #durable
                    routing_key: "process.retry.10"
                    arguments:
                        x-message-ttl: 600000
                        x-dead-letter-exchange: "bill.delayed"
                        x-dead-letter-routing-key: "process.retry.30"
            	bill.process_retry_30:
                    flags: 2 #durable
                    routing_key: "process.retry.30"
                    arguments:
                        x-message-ttl: 1800000
                        x-dead-letter-exchange: "bill.delayed"
                        x-dead-letter-routing-key: "process.retry.60"
                bill.process_retry_60:
                    flags: 2 #durable
                    routing_key: "process.retry.60"
                    arguments:
                        x-message-ttl: 3600000
                        x-dead-letter-exchange: "bill.delayed"
                        x-dead-letter-routing-key: "process.retry.60"
                bill.process_retry_360:
                    flags: 2 #durable
                    routing_key: "process.retry.360"
                    arguments:
                        x-message-ttl: 21600000
                        x-dead-letter-exchange: "bill"
                        x-dead-letter-routing-key: "process.dropped"
```

## Publisher

```yaml
services:
    evaneos.bill.publisher:
        class: 'Evaneos\Hector\Publisher\Publisher'
        tags:
            - { name: "hector.publisher", exchange: "bill" }

    evaneos.bill_delayed.publisher:
        class: 'Evaneos\Hector\Publisher\Publisher'
        tags:
            - { name: "hector.publisher", exchange: "bill.delayed", routing_key_prefix: "bill.retry." }
```

```php
$publisher = $container->get('evaneos.bill.publisher');
$publisher->publish(json_encode(['bill']), 'process');
```

## Consumer

```yaml
services:
    evaneos.bill.consumer:
        class: 'Evaneos\Hector\Consumer\Consumer'
        tags:
            - { name: "hector.consumer", exchange: "bill", queue: "bill.process" }

    evaneos.webhook.consumer:
        class: 'Evaneos\Hector\Consumer\Consumer'
        tags:
            - { name: "hector.consumer", exchange: "bill", queue: "bill.logger" }
```

```php
$consumer = $container->get('evaneos.bill.consumer');
$consumer->initialize();

/* @var AMQPQueue $queue */
$queue = $this->consumer->getQueue()->getWrappedQueue();

//$queue->get();
//$queue->consume(function(AMQPEnveloppe $message){});
// $this->reactConsumer = new Consumer($queue, $this->loop, 0.1, 10);
```

## Utils

Run test

```bash
composer test
```

Run CS fixer

```bash
composer cs
```

