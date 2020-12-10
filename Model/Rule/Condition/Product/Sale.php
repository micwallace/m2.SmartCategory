<?php
/**
 * Copyright © Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Faonni\SmartCategory\Model\Rule\Condition\Product;

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

/**
 * SmartCategory Rule Sale model
 *
 * @method Sale setType($type)
 * @method Sale setValue($value)
 * @method Sale setValueOption($option)
 * @method Sale setOperatorOption($option)
 */
class Sale extends AbstractCondition
{
    /**
     * Defines which operators will be available for this condition
     *
     * @var string
     */
    protected $_inputType = 'string';

    /**
     * @var CatalogHelper
     */
    private $catalogHelper;

    /**
     * Initialize Condition Model
     *
     * @param Context $context
     * @param CatalogHelper $catalogHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CatalogHelper $catalogHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->catalogHelper = $catalogHelper;

        $this->setType(self::class);
    }

    /**
     * Get attribute element html.
     *
     * @return string
     */
    public function getAttributeElementHtml()
    {
        return "Special Price (incl. tax)";
    }

    /**
     * Validate product attribute value for condition
     *
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        $specialPrice = $model->getSpecialPrice();
        $isDateInterval = $this->_localeDate->isScopeDateInInterval(
            $model->getStore(),
            $model->getSpecialFromDate(),
            $model->getSpecialToDate()
        );

        if (!$isDateInterval || !$specialPrice){
            return false;
        }

        $specialPrice = $this->catalogHelper->getTaxPrice(
            $model,
            $specialPrice,
            true,
            null,
            null,
            null,
            $model->getStoreId()
        );

        return parent::validateAttribute($specialPrice);
    }

    /**
     * Collect validated attributes
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        $productCollection
            ->addAttributeToSelect('special_price', 'left')
            ->addAttributeToSelect('special_from_date', 'left')
            ->addAttributeToSelect('special_to_date', 'left')
            ->addAttributeToSelect('tax_class_id', 'left');

        return $this;
    }
}
