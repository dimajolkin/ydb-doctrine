<?php

namespace Dimajolkin\YdbDoctrine\Tests\Unit\Connect;

use Dimajolkin\YdbDoctrine\Parser\YdbUriParser;
use PHPUnit\Framework\TestCase;

class EnvParserTest extends TestCase
{
    public function testLocalAnonymous(): void
    {
        $parser = new YdbUriParser();

        $dbUrl = 'ydb://localhost:2135/local?discovery=false&iam_config[anonymous]=true&iam_config[insecure]=false';

        $this->assertEquals([
            'database' => '/local',
            // Database endpoint
            'endpoint' => 'localhost:2135',
            // Auto discovery (dedicated server only)
            'discovery' => false,
            // IAM config
            'iam_config' => [
                'anonymous' => true,
                // Allow insecure grpc connection, default false
                'insecure' => false,
            ],
        ], $parser->parse($dbUrl));
    }

    public function testOAuthToken(): void
    {
        $parser = new YdbUriParser();

        $dbUrl = 'ydb://ydb.serverless.yandexcloud.net:2135/ru-central1/b1glxxxxxxxxxxxxxxxx/etn0xxxxxxxxxxxxxxxx?discovery=false&iam_config[temp_dir]=/tmp&iam_config[root_cert_file]=/CA.pem&&iam_config[oauth_token]=AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';

        $this->assertEquals([
            // Database path
            'database' => '/ru-central1/b1glxxxxxxxxxxxxxxxx/etn0xxxxxxxxxxxxxxxx',

            // Database endpoint
            'endpoint' => 'ydb.serverless.yandexcloud.net:2135',

            // Auto discovery (dedicated server only)
            'discovery' => false,

            // IAM config
            'iam_config' => [
                'temp_dir' => '/tmp', // Temp directory
                'root_cert_file' => '/CA.pem', // Root CA file (dedicated server only!)

                // OAuth token authentication
                'oauth_token' => 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',
            ],
        ], $parser->parse($dbUrl));
    }
}
