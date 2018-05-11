<?php

namespace Sergiqg\AwsRedisFreeMemory\Models;

use Aws\Ec2\Ec2Client;
use Aws\CloudWatch\CloudWatchClient;

class Redis
{
    /**
     * AWS acess config.
     *
     * @var array
     */
    protected $config = [];

    public function test()
    {
        dd("hi");
    }

    /**
     * Sets the configuration for ELB access.
     *
     * @param array $configuration
     */
    protected function setConfiguration(array $configuration = [])
    {
        $this->config = array_merge(
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
    }
}