<?php

namespace Dimajolkin\YdbDoctrine\Tests;

use Dimajolkin\YdbDoctrine\YdbTypes;
use PHPUnit\Framework\TestCase;
use YandexCloud\Ydb\Traits\TypeValueHelpersTrait;

class YdbTypesTest extends TestCase
{
    use TypeValueHelpersTrait;

    public function providerConsts(): array
    {
        return [
            [YdbTypes::BOOL, true],
//            [YdbTypes::INT8, 1],
            [YdbTypes::INT16, 1],
            [YdbTypes::INT32, 1],
            [YdbTypes::INT64, 1],
//            [YdbTypes::UINT8, 1],
//            [YdbTypes::UINT32, 1],
//            [YdbTypes::UINT64, 1],
            [YdbTypes::FLOAT, 1],
//            [YdbTypes::DOUBLE],
//            [YdbTypes::DECIMAL],
            [YdbTypes::STRING, ''],
            [YdbTypes::UTF8, ''],
            [YdbTypes::JSON, []],
//            [YdbTypes::JSON_DOCUMENT, []],
//            [YdbTypes::YSON, []],
//            [YdbTypes::UUID, ''], // as string
            [YdbTypes::DATE, new \DateTime()],
            [YdbTypes::DATETIME, new \DateTime()],
//            [YdbTypes::TIMESTAMP, time()], as datetime
//            [YdbTypes::INTERVAL],
        ];
    }

    /**
     * @dataProvider providerConsts
     */
    public function testConst(string $const, mixed $value): void
    {
        $typeObject = $this->valueOfType($value, $const);
        $this->assertEquals(strtolower($typeObject->getType()), $const);
    }
}
