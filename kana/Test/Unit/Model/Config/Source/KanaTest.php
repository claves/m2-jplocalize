<?php
namespace Veriteworks\Kana\Test\Unit\Model\Config\Source;
use Veriteworks\Kana\Model\Config\Source\Kana;
use PHPUnit\Framework\TestCase;


/**
 * Class KanaTest
 * @package Veriteworks\Kana\Test\Unit\Model\Config\Source
 */
class KanaTest extends TestCase
{
    /**
     * @var \Veriteworks\Kana\Model\Config\Source\Kana
     */
    protected $model;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->model = new Kana();
    }

    /**
     *
     */
    public function testToOptionArray()
    {
        $this->assertArrayHasKey('1', $this->model->toOptionArray());
    }
}
