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
     * @var string
     */
    protected $cluster_id = 'my-cluster-id';

    /**
     * @param array $configuration
     *
     * @return \Sergiqg\AwsRedisFreeMemory\Models\Redis
     */
    public function setConnectionConfiguration(array $configuration = []): Redis
    {
        $this->connection_configuration = array_merge(
            [
                'version'     => config('aws_cloudwatch.version'),
                'region'      => config('aws_cloudwatch.region'),
                'credentials' => [
                    'key'    => config('aws_cloudwatch.credentials.key'),
                    'secret' => config('aws_cloudwatch.credentials.secret'),
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
     * @throws \Exception
     */
    public function processCloudWatch(): Redis
    {

        try {
            $this->setConnectionConfiguration();
            $this->start_time = $this->start_time ? : Carbon::now()->subMinute(1);
            $this->end_time   = $this->end_time ? : Carbon::now();
            $cloud_watch      = new CloudWatchClient($this->connection_configuration);
            $request_params   = [
                'Namespace'  => 'AWS/ElastiCache',
                'MetricName' => 'FreeableMemory',
                'Statistics' => [ 'Minimum' ],
                'StartTime'  => $this->start_time->format('c'),
                'EndTime'    => $this->end_time->format('c'),
                'Period'     => $this->period,
                'Dimensions' => [
                    [
                        'Name'  => 'CacheClusterId',
                        'Value' => $this->cluster_id,
                    ],
                ],
            ];
            \Log::debug('Request: ' . var_export($request_params, true));
            $this->response = $cloud_watch->getMetricStatistics($request_params);
            \Log::debug('Response: ' . var_export($this->response, true));

            return $this;
        } catch (\Exception $e) {
            throw new \Exception("Unable to connect. " . $e->getMessage());
        }
    }

    /**
     * Gets the free memory in bytes.
     *
     * @return int
     * @throws \Exception
     */
    public function getFreeMemory(): int
    {

        try {
            if (!$this->response) $this->processCloudWatch();
            $total_free_memory = 0;
            $datapoints        = $this->response->get('Datapoints');
            foreach ($datapoints as $datapoint) {
                $total_free_memory += $datapoint[ 'Minimum' ];
            }

            return $total_free_memory;
        } catch (\Exception $e) {
            throw new \Exception('Unable to resolve getFreememory. ' . $e->getMessage());
        }
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
     * @param string $cluster_id
     *
     * @return Redis
     */
    public function setClusterId(string $cluster_id): Redis
    {
        $this->cluster_id = $cluster_id;

        return $this;
    }
}