# **Kafka Laravel Queue**

A Laravel package for integrating Apache Kafka as a queue driver in Laravel applications.

## **🚀 Features**

- **Custom Kafka Queue Driver** for Laravel.
- **Push & Consume Jobs** via Kafka.
- **Supports Laravel Queues** with `queue:work` integration.
- **Lightweight & Efficient** implementation for event-driven architecture.
- **Microservice-Friendly** for decoupled applications.
- **Compatible with Laravel 11+**

## **📦 Installation**

### **1️⃣ Update Your `composer.json`**

Modify your `composer.json` to include the package repository:

```json
"require": {
    "mk/kafka-laravel-queue": "dev-main",
    "php": "^8.2"
},
"autoload": {
    "psr-4": {
        "Kafka\\": "vendor/mk/kafka-laravel-queue/src/"
    }
},
"repositories": [
   {
      "type": "vcs",
      "url": "https://github.com/MalobaKombo/kafka-laravel-queue.git"
   }
]
```

### **2️⃣ Install via Composer**

After updating `composer.json`, install the package:

```sh
composer update mk/kafka-laravel-queue
```

### **3️⃣ Register the Service Provider**

In `./src/bootstrap/providers.php`, add:

```php
<?php

return [
    Kafka\KafkaServiceProvider::class,
];
```

## **⚙️ Configuration**

Update your `.env` file with Kafka settings:

```ini
KAFKA_QUEUE=default_topic
; For KAFKA_ENVIRONMENT use "internal" or "external" depending on your Kafka environment
KAFKA_ENVIRONMENT=internal
BOOTSTRAP_SERVERS=kafka-1:9092,kafka-2:9092
; For KAFKA_SECURITY_PROTOCOL use SASL_PLAINTEXT for external Kafka environments
SECURITY_PROTOCOL=PLAINTEXT
SASL_MECHANISMS=PLAIN
; Use Credentials for external Kafka environments
KAFKA_SASL_USERNAME=myuser
KAFKA_SASL_PASSWORD=mypassword
GROUP_ID=default_group
QUEUE_CONNECTION=kafka
```

Then, update `config/queue.php`:

```php
'connections' => [
    'kafka' => [
        'driver' => 'kafka',
        'kafka_environments' => env('KAFKA_ENVIRONMENT', 'internal'),
        'queue' => env('KAFKA_QUEUE'),
        'bootstrap_servers' => env('BOOTSTRAP_SERVERS'),
        'security_protocol' => env('SECURITY_PROTOCOL'),
        'sasl_mechanisms' => env('SASL_MECHANISMS'),
        'sasl_username' => env('SASL_USERNAME'),
        'sasl_password' => env('SASL_PASSWORD'),
        'group_id' => env('GROUP_ID'),
    ],
],
```

## **🛠️ Usage**

### **1️⃣ Dispatching Jobs**

You can push jobs to Kafka like any Laravel queue:

```php
use App\Jobs\SendMessageJob;

SendMessageJob::dispatch(['message' => 'Hello from Laravel Kafka!'])
    ->onQueue('default_topic');
```

### **2️⃣ Consuming Jobs**

To start processing jobs from Kafka:

```sh
php artisan queue:work --queue=default_topic
```

### **3️⃣ Microservices Namespace & Data Format**

- The **jobs producing and receiving** in each microservice **must be in the same namespace**.
- The **data must be an array** when dispatching and receiving messages.

## **🎯 Done!**

You have now successfully integrated Kafka as a queue in Laravel 11+. 🚀
