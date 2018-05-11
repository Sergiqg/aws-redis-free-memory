<?php

namespace Sergiqg\AwsRedisFreeMemory\Models;

use Aws\Ec2\Ec2Client;
use Aws\CloudWatch\CloudWatchClient;
use Carbon\Carbon;

class Redis
{
    /**
     * @var Aws\Result
     */
    protected $response;

    /**
     * AWS acess config.
     *
     * @var array
     */
    protected $connection_configuration = [];

    /**
     * @var string
     */
    protected $names_space = 'AWS/ElastiCache';

    /**
     * @var string
     */
    protected $metric_name = 'FreeableMemory';

    /**
     * @var array
     */
    protected $statistics = [ 'Minimum' ];

    /**
     * @var Carbon\Carbon
     */
    protected $start_time;

    /**
     * @var Carbon\Carbon
     */
    protected $end_time;

    /**
     * @var int
     */
    protected $period = 3600;

    /**
     * @var array
     */
    protected $dimensions = [
        [
            'Name'  => 'CacheClusterId',
            'Value' => 'cache',
        ],
    ];

    /**
     * @param array $configuration
     *
     * @return \Sergiqg\AwsRedisFreeMemory\Models\Redis
     */
    public function setConnectionConfiguration(array $configuration = []): Redis
    {
        $this->connection_configuration = array_merge(
            [
                'version'     => config('aws_elb_instance_detector.version'),
                'region'      => config('aws_elb_instance_detector.region'),
                'credentials' => [
                    'key'    => config('aws_elb_instance_detector.credentials.key'),
                    'secret' => config('aws_elb_instance_detector.credentials.secret'),
                ],
            ],
            $configuration
        );

        return $this;
    }

    /**
     * Process the cloudwatch metric.
     *
     * @return \Sergiqg\AwsRedisFreeMemory\Models\Redis
     */
    public function processCloudWatch(): Redis
    {
        $this->setConnectionConfiguration();

        $this->start_time ? : Carbon::now()->subMinute(1)->format('c');
        $this->end_time ? : Carbon::now()->format('c');

        $cloud_watch    = new CloudWatchClient($this->connection_configuration);
        $this->response = $cloud_watch->getMetricStatistics(
            [
                'Namespace'  => 'AWS/ElastiCache',
                'MetricName' => 'FreeableMemory',
                'Statistics' => [ 'Minimum' ],
                'StartTime'  => $this->start_time->subMinute(1)->format('c'),
                'EndTime'    => $this->end_time->format('c'),
                'Period'     => 3600,
                'Dimensions' => [
                    [
                        'Name'  => 'CacheClusterId',
                        'Value' => 'cache',
                    ],
                ],
            ]
        );

        return $this;
    }

    /**
     * Gets the free memory in bytes.
     *
     * @return int
     */
    public function getFreeMemory(): int
    {
        if (!$this->response) $this->processCloudWatch();

        $total_free_memory = 0;
        $datapoints        = $this->response->get('Datapoints');
        foreach ($datapoints as $datapoint) {
            $total_free_memory += $datapoint[ 'Minimum' ];
        }

        return $total_free_memory;
    }

    /**
     * @param string $names_space
     *
     * @return \Sergiqg\AwsRedisFreeMemory\Models\Redis
     */
    public function setNamesSpace(string $names_space): Redis
    {
        $this->names_space = $names_space;

        return $this;
    }

    /**
     * @param string $metric_name
     *
     * @return \Sergiqg\AwsRedisFreeMemory\Models\Redis
     */
    public function setMetricName(string $metric_name): Redis
    {
        $this->metric_name = $metric_name;

        return $this;
    }

    /**
     * @param array $statistics
     *
     * @return \Sergiqg\AwsRedisFreeMemory\Models\Redis
     */
    public function setStatistics(array $statistics): Redis
    {
        $this->statistics = $statistics;

        return $this;
    }

    /**
     * @param \Carbon\Carbon $start_time
     *
     * @return \Sergiqg\AwsRedisFreeMemory\Models\Redis
     */
    public function setStartTime(Carbon $start_time): Redis
    {
        $this->start_time = $start_time;

        return $this;
    }

    /**
     * @param \Carbon\Carbon $end_time
     *
     * @return \Sergiqg\AwsRedisFreeMemory\Models\Redis
     */
    public function setEndTime(Carbon $end_time): Redis
    {
        $this->end_time = $end_time;

        return $this;
    }

    /**
     * @param int $period
     *
     * @return \Sergiqg\AwsRedisFreeMemory\Models\Redis
     */
    public function setPeriod(int $period): Redis
    {
        $this->period = $period;

        return $this;
    }

    /**
     * @param array $dimensions
     *
     * @return \Sergiqg\AwsRedisFreeMemory\Models\Redis
     */
    public function setDimensions(array $dimensions): Redis
    {
        $this->dimensions = $dimensions;

        return $this;
    }
}