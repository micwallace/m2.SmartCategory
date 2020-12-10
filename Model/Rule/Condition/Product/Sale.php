<?php
/**
 * Copyright © Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Faonni\SmartCategory\Model\Rule\Condition\Product;

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
     * Initialize Condition Model
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->setType(self::class);
    }

    /**
     * Get attribute element html.
     *
     * @return string
     */
    public function getAttributeElementHtml()
    {
        return "Special Price";
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

        if (!$isDateInterval){
            return false;
        }

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
            ->addAttributeToSelect('special_to_date', 'left');

        return $this;
    }
}
