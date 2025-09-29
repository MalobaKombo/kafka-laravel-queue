# **Kafka Laravel Queue**

A Laravel package for integrating Apache Kafka as a queue driver in Laravel applications.

---

## 🚀 Features

- Custom Kafka Queue Driver for Laravel
- Push & Consume Jobs via Kafka
- Supports Laravel Queues with `queue:work` integration
- Lightweight & Efficient for event-driven architecture
- Microservice-Friendly for decoupled applications
- Compatible with Laravel 11 and 12

---

## 📦 Installation

### 1️⃣ Update Your `composer.json`

```json
"require": {
    "mk/kafka-laravel-queue": "dev-main",
    "php": "^8.2"
},
"autoload": {
    "psr-4": {
        "Kafka\": "vendor/mk/kafka-laravel-queue/src/"
    }
},
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/MalobaKombo/kafka-laravel-queue.git"
    }
]
```

### 2️⃣ Install via Composer

```bash
composer update mk/kafka-laravel-queue
```

### 3️⃣ Register the Service Provider

```php
// ./src/bootstrap/providers.php

return [
    Kafka\KafkaServiceProvider::class,
];
```

---

## ⚙️ Configuration

### .env Settings

```ini
KAFKA_QUEUE=default_topic
KAFKA_ENVIRONMENT=internal
BOOTSTRAP_SERVERS=kafka-1:9092,kafka-2:9092
SECURITY_PROTOCOL=PLAINTEXT
SASL_MECHANISMS=PLAIN
KAFKA_SASL_USERNAME=myuser
KAFKA_SASL_PASSWORD=mypassword
GROUP_ID=default_group
QUEUE_CONNECTION=kafka
```

### queue.php Configuration

```php
'connections' => [
    'kafka' => [
        'driver' => 'kafka',
        'kafka_environments' => env('KAFKA_ENVIRONMENT', 'internal'),
        'queue' => env('KAFKA_QUEUE'),
        'bootstrap_servers' => env('BOOTSTRAP_SERVERS'),
        'security_protocol' => env('SECURITY_PROTOCOL'),
        'sasl_mechanisms' => env('SASL_MECHANISMS'),
        'sasl_username' => env('KAFKA_SASL_USERNAME'),
        'sasl_password' => env('KAFKA_SASL_PASSWORD'),
        'group_id' => env('GROUP_ID'),
    ],
],
```

---

## 🛠️ Usage

### 1️⃣ Dispatching Jobs

```php
use App\Jobs\SendMessageJob;

SendMessageJob::dispatch(['message' => 'Hello from Laravel Kafka!'])
    ->onQueue('default_topic');
```

### 2️⃣ Consuming Jobs

```bash
php artisan queue:work --queue=default_topic
```

---

## ✅ Kafka Job Namespace Example

Kafka uses the job class namespace to resolve the consumer job.

If the consumer cannot find a matching FQCN, you’ll see:

```
❌ Received invalid job data!
```

### 📌 Rule

- Producer and Consumer jobs must use the **same namespace and class name**.

---

## ✅ Example 1: School Verification Job

### 🎯 Producer (IAM Service)

```php
// File: app/Jobs/Web/Verification/VerifySchoolJob.php

namespace App\Jobs\Web\Verification;

use Illuminate\Contracts\Queue\ShouldQueue;

class VerifySchoolJob implements ShouldQueue {
    public array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function handle(): void {
        // This will NOT run in the producer service
    }
}
```

### 📥 Consumer (School Service)

```php
// File: app/Jobs/Web/Verification/VerifySchoolJob.php

namespace App\Jobs\Web\Verification;

use Illuminate\Contracts\Queue\ShouldQueue;

class VerifySchoolJob implements ShouldQueue {
    public array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function handle(): void {
        // ✅ Create DB, trigger events, mark as verified, etc.
    }
}
```

---

## ✅ Example 2: Send Notification Job

### 🎯 Producer

```php
// File: app/Jobs/Web/Notifications/SendNotificationJob.php

namespace App\Jobs\Web\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;

class SendNotificationJob implements ShouldQueue {
    public array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function handle(): void {}
}
```

### 📥 Consumer

```php
// File: app/Jobs/Web/Notifications/SendNotificationJob.php

namespace App\Jobs\Web\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;

class SendNotificationJob implements ShouldQueue {
    public array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function handle(): void {
        // 🔔 Send SMS, email or push notification
    }
}
```

---

## ✅ Summary

- Use **identical namespaces and class names** for Kafka jobs across microservices.
- Ensure **data is always passed as an array**.
- Register consumers with `queue:work` using the correct topic.

```bash
php artisan queue:work --queue=default_topic
```

You’re now Kafka-ready in Laravel! 🚀
