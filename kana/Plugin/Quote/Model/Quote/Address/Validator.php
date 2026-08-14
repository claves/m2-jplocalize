<?php
namespace Veriteworks\Kana\Plugin\Quote\Model\Quote\Address;

use \Magento\Quote\Model\QuoteAddressValidator;
use Magento\Quote\Api\Data\AddressInterface;
use \Magento\Customer\Model\AddressFactory;
use \Veriteworks\Kana\Helper\Data;

class Validator
{
    /**
     * @var \Veriteworks\Kana\Helper\Data
     */
    private $helper;

    /**
     * @var AddressFactory
     */
    private $factory;

    /**
     * Validator constructor.
     * @param Data $helper
     * @param AddressFactory $factory
     */
    public function __construct(
        Data $helper,
        AddressFactory $factory
    ) {
        $this->helper = $helper;
        $this->factory = $factory;
    }

    /**
     * @param QuoteAddressValidator $subject
     * @param AddressInterface $address
     * @return null
     */
    public function beforeValidate(
        QuoteAddressValidator $subject,
        AddressInterface $address
    ) {
        if($this->helper->getRequireKana()) {
            $this->checkKana($address);
        }

        return null;
    }


    /**
     * @param AddressInterface $address
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    private function checkKana(AddressInterface $address)
    {
        $fkana = $this->getKanaValue($address, 'firstnamekana');
        $lkana = $this->getKanaValue($address, 'lastnamekana');

        if ((!$fkana || !$lkana) && ($addressId = $address->getCustomerAddressId())) {
            $customerAddress = $this->factory->create()->load($addressId);

            if (!$fkana && ($fkana = $customerAddress->getFirstnamekana())) {
                $address->setCustomAttribute('firstnamekana', $fkana);
            }
            if (!$lkana && ($lkana = $customerAddress->getLastnamekana())) {
                $address->setCustomAttribute('lastnamekana', $lkana);
            }
        }

        if (!$fkana) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __("Firstname kana is required field. Your address doesn't have it.")
            );
        }

        if (!$lkana) {
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
