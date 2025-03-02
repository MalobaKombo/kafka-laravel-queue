<?php

namespace Kafka;

use Illuminate\Queue\Queue;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Exception;

class KafkaQueue extends Queue implements QueueContract {

    protected $producer, $consumer;

    public function __construct($producer, $consumer) {
        $this->producer = $producer;
        $this->consumer = $consumer;
    }

    public function size($queue = null) {
        // TODO: Implement size() method.
    }

    public function push($job, $data = '', $queue = null) {
        $topic = $this->producer->newTopic($queue ?? env('KAFKA_QUEUE'));
        $payload = serialize($job); // Serialize job for transport

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $payload);
        $this->producer->flush(1000);
    }

    public function pushRaw($payload, $queue = null, array $options = []) {
        // TODO: Implement pushRaw() method.
    }

    public function later($delay, $job, $data = '', $queue = null) {
        // TODO: Implement later() method.
    }

    public function pop($queue = null) {
        $topic = $queue ?? env('KAFKA_QUEUE');

        // Subscribe to the topic
        $this->consumer->subscribe([$topic]);

        echo "👂 Listening for messages on topic: $topic...\n";

        while (true) {
            try {
                $message = $this->consumer->consume(1000); // Poll every second

                switch ($message->err) {
                    case RD_KAFKA_RESP_ERR_NO_ERROR:
                        echo "📩 Received message...\n";

                        if (!empty($message->payload)) {
                            $job = unserialize($message->payload);

                            if ($job instanceof \Illuminate\Contracts\Queue\ShouldQueue) {
                                echo "✅ Job is valid. Executing `handle()`...\n";
                                $job->handle();
                            } else {
                                echo "❌ Received invalid job data!\n";
                            }
                        } else {
                            echo "⚠️ Empty payload received!\n";
                        }
                        break;

                    case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                        echo "ℹ️ No more messages in partition...\n";
                        break;

                    case RD_KAFKA_RESP_ERR__TIMED_OUT:
                        echo "⚠️ Kafka timeout...\n";
                        break;

                    default:
                        echo "❌ Kafka error: " . $message->errstr() . "\n";
                        sleep(3);
                        break;
                }
            } catch (Exception $e) {
                echo "🚨 Exception occurred: " . $e->getMessage() . "\n";
            }

            usleep(500000); // Sleep for 0.5 seconds to prevent high CPU usage
        }
    }
}
