<?php
namespace Veriteworks\Kana\Test\Unit\Helper;

use \Veriteworks\Kana\Helper\Data;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|MockObject
     */
    protected $scopeMock;

    /**
     * @var \Veriteworks\Kana\Helper\Data|MockObject
     */
    protected $helper;

    /**
     *
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->scopeMock =
            $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
                ->disableOriginalConstructor()
                ->getMock();
        $contextMock = $this->getMockBuilder('Magento\Framework\App\Helper\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects($this->any())
            ->method('getScopeConfig')
            ->willReturn($this->scopeMock);
        $this->helper =
            $objectManager->getObject('Veriteworks\Kana\Helper\Data',
                ['context'=>$contextMock]);
    }

    /**
     * @param $path
     * @param $expected
     * @param $result
     *
     */
    #[DataProvider('getLocaleProvider')]
    public function testGetLocale($path, $expected, $result)
    {
        $map = [
            [$path, ScopeInterface::SCOPE_STORE, null, $result]
        ];

        $this->scopeMock->expects(self::any())
            ->method('getValue')
            ->will($this->returnValueMap($map));

        $value = $this->helper->getLocale();

        $this->assertEquals($expected, $value);
    }

    /**
     * @return array
     */
    public static function getLocaleProvider(): array
    {
        return [
            ['general/locale/code', 'ja_JP', 'ja_JP'],
            ['general/locale/code','en_US', 'en_US'],
        ];
    }


    /**
     * @param $path
     * @param $expected
     * @param $result
     *
     */
    #[DataProvider('getElementOrderProvider')]
    public function testGetElementOrder($path, $expected, $result)
    {
        $map = [
            [$path, ScopeInterface::SCOPE_STORE, null, $result]
        ];

        $this->scopeMock->expects(self::any())
            ->method('getValue')
            ->will($this->returnValueMap($map));

        $value = $this->helper->getElementOrder();

        $this->assertEquals($expected, $value);
    }

    /**
     * @return array
     */
    public static function getElementOrderProvider(): array
    {
        return [
            ['localize/sort/',
                [
                    'lastname' => '1',
                    'firstname' => '2',
                ],
                [
                    'lastname' => '1',
                    'firstname' => '2',
                ]
            ],
        ];
    }


    /**
     * @param $path
     * @param $expected
     * @param $result
     *
     */
    #[DataProvider('getShowCountryProvider')]
    public function testGetShowCounry($path, $expected, $result)
    {
        $map = [
            [$path, ScopeInterface::SCOPE_STORE, null, $result]
        ];

        $this->scopeMock->expects(self::any())
            ->method('getValue')
            ->will($this->returnValueMap($map));

        $value = $this->helper->getShowCounry();

        $this->assertEquals($expected, $value);
    }

    /**
     * @return array
     */
    public static function getShowCountryProvider(): array
    {
        return [
            ['localize/address/hide_country', '1', '1'],
            ['localize/address/hide_country', '0', '0'],
        ];
    }


    /**
     * @param $path
     * @param $expected
     * @param $result
     *
     */
    #[DataProvider('getRequireKanaProvider')]
    public function testGetRequireKana($path, $expected, $result)
    {
        $map = [
            [$path, ScopeInterface::SCOPE_STORE, null, $result]
        ];

        $this->scopeMock->expects(self::any())
            ->method('getValue')
            ->will($this->returnValueMap($map));

        $value = $this->helper->getRequireKana();

        $this->assertEquals($expected, $value);
    }

    /**
     * @return array
     */
    public static function getRequireKanaProvider(): array
    {
        return [
            ['customer/address/require_kana', '1', '1'],
            ['customer/address/require_kana', '0', '0'],
        ];
    }

    /**
     * @param $path
     * @param $expected
     * @param $result
     *
     */
    #[DataProvider('getUseKanaProvider')]
    public function testGetUseKana($path, $expected, $result)
    {
        $map = [
            [$path, ScopeInterface::SCOPE_STORE, null, $result]
        ];

        $this->scopeMock->expects(self::any())
            ->method('getValue')
            ->will($this->returnValueMap($map));

        $value = $this->helper->getUseKana();

        $this->assertEquals($expected, $value);
    }

    /**
     * @return array
     */
    public static function getUseKanaProvider(): array
    {
        return [
            ['customer/address/use_kana', '1', '1'],
            ['customer/address/use_kana', '0', '0'],
        ];
    }


    /**
     * @param $path
     * @param $expected
     * @param $result
     *
     */
    #[DataProvider('getChangeFieldsOrderProvider')]
    public function testGetChangeFieldsOrder($path, $expected, $result)
    {
        $map = [
            [$path, ScopeInterface::SCOPE_STORE, null, $result]
        ];

        $this->scopeMock->expects(self::any())
            ->method('getValue')
            ->will($this->returnValueMap($map));

        $value = $this->helper->getChangeFieldsOrder();

        $this->assertEquals($expected, $value);
    }


    /**
     * @return array
     */
    public static function getChangeFieldsOrderProvider(): array
    {
        return [
            ['localize/address/change_fields_order', '1', '1'],
            ['localize/address/change_fields_order', '0', '0'],
        ];
    }
}
