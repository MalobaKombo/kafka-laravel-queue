<?php

namespace Kafka;

use Illuminate\Queue\Connectors\ConnectorInterface;

use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Producer;

class KafkaConnector implements ConnectorInterface {
    public function connect(array $config) {
        $conf = new Conf();

        // Kafka Bootstrap Servers
        $conf->set('bootstrap.servers', $config['bootstrap_servers']);

        if ($config['kafka_enviroment'] != 'internal') {
            $conf->set('security.protocol', $config['security_protocol']);
            $conf->set('sasl.mechanisms', $config['sasl_mechanisms']);
            $conf->set('sasl.username', $config['sasl_username']);
            $conf->set('sasl.password', $config['sasl_password']);
        } else {
            // No Security Configuration because it is local/INTERNAL
            $conf->set('security.protocol', $config['security_protocol']);
        }

        // Create Kafka producer
        $producer = new Producer($conf);

        // Consumer Group and Offset Settings
        $conf->set('group.id', $config['group_id']);
        $conf->set('auto.offset.reset', 'earliest');

        // Create Kafka consumer
        $consumer = new KafkaConsumer($conf);

        return new KafkaQueue($producer, $consumer);
    }
}
