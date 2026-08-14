<?php
/**
 * Price format Plugin Test
 *
 * PHP version 5, 7
 *
 * @category Test
 * @package  Veriteworks\Price\Test\Unit\Model\Directory\Plugin
 * @author   Veriteworks Inc. <info@veriteworks.co.jp>
 * @license  Open Software License 3.0
 * @link     https://principle-works.jp/
 */
namespace Veriteworks\Price\Test\Unit\Model\Directory\Plugin;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Veriteworks\Price\Helper\Data;

/**
 * Class FormatTest
 *
 * @category Test
 * @package  Veriteworks\Price\Test\Unit\Model\Directory\Plugin
 * @author   Veriteworks Inc. <info@veriteworks.co.jp>
 * @license  Open Software License 3.0
 * @link     https://principle-works.jp/
 */
class FormatTest extends TestCase
{
    /**
     * Price Format Plugin
     *
     * @var \Veriteworks\Price\Model\Directory\Plugin\Format
     */
    protected $formatPlugin;

    /**
     * Price Currency
     *
     * @var \Magento\Directory\Model\PriceCurrency
     */
    protected $priceCurrency;

    /**
     * Price container closure
     *
     * @var \Closure
     */
    protected $closure;

    /**
     * Setup
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $helper = $this->createMock(Data::class);
        $helper->method('getRoundMethod')->willReturn('floor');
        $this->formatPlugin = $objectManager->getObject(
            'Veriteworks\Price\Model\Directory\Plugin\Format',
            ['helper' => $helper]
        );

        $this->priceCurrency = $this->getMockBuilder(
            'Magento\Directory\Model\PriceCurrency'
        )->disableOriginalConstructor()->getMock();

        $this->closure = function () {
            return '<span class="price">￥100.49</span>';
        };
    }

    /**
     * Test for aroundFormat to JPY
     *
     * @return void
     */
    public function testJpyAroundFormat()
    {
        $currency = $this->getMockBuilder('Magento\Directory\Model\Currency')
            ->disableOriginalConstructor()
            ->getMock();
        $currency->expects($this->atLeastOnce())
            ->method('getCode')->willReturn('JPY');
        $currency->expects($this->atLeastOnce())
            ->method('formatPrecision')
            ->willReturn('<span class="price">￥100</span>');

        $this->priceCurrency->expects($this->atLeastOnce())
            ->method('getCurrency')->willReturn($currency);

        $this->assertEquals(
            '<span class="price">￥100</span>',
            $this->formatPlugin->aroundFormat(
                $this->priceCurrency,
                $this->closure,
                100.49,
                true,
                2,
                null,
                null
            )
        );
    }

    /**
     * Test for aroundFormat to others
     *
     * @return void
     */
    public function testNonJpyAroundFormat()
    {
        $currency = $this->getMockBuilder('Magento\Directory\Model\Currency')
            ->disableOriginalConstructor()
            ->getMock();
        $currency->expects($this->atLeastOnce())
            ->method('getCode')->willReturn('USD');

        $this->priceCurrency->expects($this->atLeastOnce())
            ->method('getCurrency')->willReturn($currency);

        $this->assertNotEquals(
            '<span class="price">￥100</span>',
            $this->formatPlugin->aroundFormat(
                $this->priceCurrency,
                $this->closure,
                100.49,
                true,
                2,
                null,
                null
            )
        );
    }
}
