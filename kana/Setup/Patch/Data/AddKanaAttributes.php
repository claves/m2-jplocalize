<?php
declare(strict_types=1);

namespace Veriteworks\Kana\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddKanaAttributes implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        try {
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
            $attributes = $this->getAttributes();
            foreach ($attributes as $attributeCode => $options) {
                $this->addAttributeIfMissing($eavSetup, Customer::ENTITY, $attributeCode, $options);
                $this->addAttributeIfMissing($eavSetup, 'customer_address', $attributeCode, $options);
            }
            $this->assignAttributesToForms($eavSetup, array_keys($attributes));
        } finally {
            $this->moduleDataSetup->getConnection()->endSetup();
        }

        return $this;
    }

    private function addAttributeIfMissing(
        EavSetup $eavSetup,
        string $entityType,
        string $attributeCode,
        array $options
    ): void {
        if (!$eavSetup->getAttributeId($entityType, $attributeCode)) {
            $eavSetup->addAttribute($entityType, $attributeCode, $options);
        }
    }

    private function assignAttributesToForms(EavSetup $eavSetup, array $attributeCodes): void
    {
        $formsByEntity = [
            Customer::ENTITY => ['customer_account_create', 'customer_account_edit', 'adminhtml_customer'],
            'customer_address' => [
                'customer_register_address',
                'customer_address_edit',
                'adminhtml_customer_address'
            ],
        ];
        $rows = [];
        foreach ($formsByEntity as $entityType => $formCodes) {
            foreach ($attributeCodes as $attributeCode) {
                $attributeId = (int) $eavSetup->getAttributeId($entityType, $attributeCode);
                foreach ($formCodes as $formCode) {
                    $rows[] = ['form_code' => $formCode, 'attribute_id' => $attributeId];
                }
            }
        }

        $this->moduleDataSetup->getConnection()->insertOnDuplicate(
            $this->moduleDataSetup->getTable('customer_form_attribute'),
            $rows
        );
    }

    private function getAttributes(): array
    {
        return [
            'firstnamekana' => [
                'type' => 'varchar', 'input' => 'text', 'visible' => true, 'required' => false,
                'system' => 0, 'sort_order' => 45,
                'validate_rules' => '{"max_text_length":255,"min_text_length":1}',
                'position' => 45, 'label' => 'First name kana',
            ],
            'lastnamekana' => [
                'type' => 'varchar', 'input' => 'text', 'visible' => true, 'required' => false,
                'system' => 0, 'sort_order' => 65,
                'validate_rules' => '{"max_text_length":255,"min_text_length":1}',
                'position' => 65, 'label' => 'Last name kana',
            ],
        ];
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
