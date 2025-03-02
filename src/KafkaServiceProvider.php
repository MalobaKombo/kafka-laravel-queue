<?php

namespace Kafka;

use Illuminate\Support\ServiceProvider;

class KafkaServiceProvider extends ServiceProvider {
    public function boot(): void {
        $manager = $this->app->make('queue');
        $manager->addConnector('kafka', function () {
            return new KafkaConnector();
        });
    }
}
