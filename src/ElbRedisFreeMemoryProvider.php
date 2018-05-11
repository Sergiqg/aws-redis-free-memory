<?php

namespace Sergiqg\AwsRedisFreeMemory;

use \Illuminate\Support\ServiceProvider;

class ElbRedisFreeMemoryProvider extends ServiceProvider
{
    /**
     * Register de Service Provider
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/aws_cloudwatch.php', 'aws_cloudwatch');
    }

    public function boot()
    {
        $configPath = __DIR__ . '/../config/aws_cloudwatch.php';
        $this->publishes([ $configPath => config_path('aws_cloudwatch.php') ]);
    }
}