<?php
namespace Veriteworks\Kana\Plugin\Quote\Model\Quote;

use \Magento\Quote\Model\QuoteValidator;
use Magento\Quote\Api\Data\AddressInterface;
use \Magento\Quote\Model\Quote;
use \Veriteworks\Kana\Helper\Data;

class Validator
{
    /**
     * @var \Veriteworks\Kana\Helper\Data
     */
    private $helper;

    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param QuoteValidator $subject
     * @param \Magento\Quote\Model\Quote $arguments
     * @return bool
     */
    public function beforeValidateBeforeSubmit(
        QuoteValidator $subject,
        Quote $quote
    ) {
        if($this->helper->getRequireKana()) {
            $this->validateAddress($quote->getBillingAddress());
            if (!$quote->isVirtual()) {
                $this->validateAddress($quote->getShippingAddress());
            }
        }

        return null;
    }

    private function validateAddress(AddressInterface $address): void
    {
        if (!$this->getKanaValue($address, 'firstnamekana')) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __("Firstname kana is required field. Your address doesn't have it.")
            );
        }

        if (!$this->getKanaValue($address, 'lastnamekana')) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __("Lastname kana is required field. Your address doesn't have it.")
            );
        }
    }

    private function getKanaValue(AddressInterface $address, string $attributeCode): ?string
    {
        if (method_exists($address, 'getData')) {
            $value = $address->getData($attributeCode);
            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }

        $attribute = $address->getCustomAttribute($attributeCode);

        return $attribute ? (string) $attribute->getValue() : null;
    }
}
