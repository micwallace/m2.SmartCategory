<?php
/**
 * Copyright © Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Faonni\SmartCategory\Setup;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Faonni\SmartCategory\Model\Entity\Attribute\Source\StoreWithDefault;
use Magento\Framework\DB\AggregatedFieldDataConverter;
use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Upgrade data
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Field data converter
     *
     * @var \Magento\Framework\DB\AggregatedFieldDataConverter
     */
    protected $aggregatedFieldConverter;

    /**
     * EAV setup factory
     *
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * Initialize setup
     *
     * @param AggregatedFieldDataConverter $aggregatedFieldConverter
     */
    public function __construct(
        AggregatedFieldDataConverter $aggregatedFieldConverter,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->aggregatedFieldConverter = $aggregatedFieldConverter;
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrades DB data
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $this->convertSerializedDataToJson($setup);
        }

        if (version_compare($context->getVersion(), '2.3.2', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                Category::ENTITY,
                'attribute_value_store',
                [
                    'type' => 'int',
                    'label' => 'Attribute Value Store',
                    'input' => 'select',
                    'source' => StoreWithDefault::class,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'required' => false,
                    'sort_order' => 25,
                    'default' => '0',
                    'group' => 'Products in Category',
                    'note' => 'The store view used when loading product attribute values',
                ]
            );
        }

        $setup->endSetup();
    }

    /**
     * Convert metadata from serialized to JSON format
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    protected function convertSerializedDataToJson($setup)
    {
        $this->aggregatedFieldConverter->convert(
            [
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable('faonni_smartcategory_rule'),
                    'rule_id',
                    'conditions_serialized'
                ),
            ],
            $setup->getConnection()
        );
    }
}
