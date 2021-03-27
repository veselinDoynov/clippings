<?php

namespace App\Logging;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Formatter\JsonFormatter;
use Monolog\Logger;

class CloudWatchLoggerFactory
{
    /**
     * @param array $config
     * @return Logger
     * @throws \Exception
     */
    public function __invoke(array $config)
    {
        $cloudwatchConfig = config('logging.channels.cloudwatch');

        // Instantiate AWS SDK CloudWatch Logs Client
        $client = new CloudWatchLogsClient($cloudwatchConfig['sdk']);

        // Log group name, will be created if none
        $groupName = $cloudwatchConfig['group'];

        // Log stream name, will be created if none
        $streamName = $cloudwatchConfig['stream'];

        // Days to keep logs, 14 by default. Set to `null` to allow indefinite retention.
        $retentionDays = $cloudwatchConfig['retention'];

        // Minimum log level
        $level = (int)$cloudwatchConfig['level'];

        // Instantiate handler (tags are optional)
        $handler = new CloudWatch($client, $groupName, $streamName, $retentionDays, 10000, [], $level);

        // Optionally set the JsonFormatter to be able to access your log messages in a structured way
        $handler->setFormatter(new JsonFormatter());

        // Create a log channel
        $logger = new Logger('name');

        // Set handler
        $logger->pushHandler($handler);

        return $logger;
    }
}