<?php
namespace SnootBeest\Tantrum\Test\Core;

use Noodlehaus\ConfigInterface;
use SnootBeest\Tantrum\Core\Config;
use SnootBeest\Tantrum\Test\TestCase;

/**
 * @coversDefaultClass SnootBeest\Tantrum\Core\Config
 */
class ConfigTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function constructSucceeds()
    {
        $values = [
            uniqid() => uniqid(),
            uniqid() => uniqid(),
        ];
        $config = new Config($values);
        self::assertInstanceOf(ConfigInterface::class, $config);
        foreach($values as $key => $value) {
            self::assertTrue($config->has($key));
            self::assertEquals($value, $config->get($key));
        }
    }
}