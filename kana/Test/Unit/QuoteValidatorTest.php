<?php
declare(strict_types=1);

namespace Veriteworks\Kana\Test\Unit;

use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Veriteworks\Kana\Helper\Data;
use Veriteworks\Kana\Plugin\Quote\Model\Quote\Validator;

class QuoteValidatorTest extends TestCase
{
    /** @var Data|MockObject */
    private $helper;

    private Validator $plugin;

    protected function setUp(): void
    {
        $this->helper = $this->createMock(Data::class);
        $this->plugin = new Validator($this->helper);
    }

    public function testSkipsValidationWhenKanaIsNotRequired(): void
    {
        $this->helper->method('getRequireKana')->willReturn(false);
        $quote = $this->createMock(Quote::class);

        self::assertNull(
            $this->plugin->beforeValidateBeforeSubmit($this->createMock(QuoteValidator::class), $quote)
        );
    }

    public function testValidatesBillingAndShippingAddresses(): void
    {
        $this->helper->method('getRequireKana')->willReturn(true);
        $billingAddress = $this->createAddressWithKana();
        $shippingAddress = $this->createAddressWithKana();
        $quote = $this->createMock(Quote::class);
        $quote->method('getBillingAddress')->willReturn($billingAddress);
        $quote->method('isVirtual')->willReturn(false);
        $quote->method('getShippingAddress')->willReturn($shippingAddress);

        self::assertNull(
            $this->plugin->beforeValidateBeforeSubmit($this->createMock(QuoteValidator::class), $quote)
        );
    }

    public function testRejectsAddressWithoutFirstnameKana(): void
    {
        $this->helper->method('getRequireKana')->willReturn(true);
        $lastnameKana = $this->createMock(AttributeInterface::class);
        $lastnameKana->method('getValue')->willReturn('ヤマダ');
        $address = $this->createMock(AddressInterface::class);
        $address->method('getCustomAttribute')->willReturnMap([
            ['firstnamekana', null],
            ['lastnamekana', $lastnameKana],
        ]);
        $quote = $this->createMock(Quote::class);
        $quote->method('getBillingAddress')->willReturn($address);

        $this->expectException(ValidatorException::class);
        $this->plugin->beforeValidateBeforeSubmit($this->createMock(QuoteValidator::class), $quote);
    }

    private function createAddressWithKana(): AddressInterface
    {
        $firstnameKana = $this->createMock(AttributeInterface::class);
        $firstnameKana->method('getValue')->willReturn('タロウ');
        $lastnameKana = $this->createMock(AttributeInterface::class);
        $lastnameKana->method('getValue')->willReturn('ヤマダ');
        $address = $this->createMock(AddressInterface::class);
        $address->method('getCustomAttribute')->willReturnMap([
            ['firstnamekana', $firstnameKana],
            ['lastnamekana', $lastnameKana],
        ]);

        return $address;
    }
}
