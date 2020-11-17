<?php

namespace Faonni\SmartCategory\Model\Entity\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\Store;

class StoreWithDefault extends Store {

    /**
     * Retrieve Full Option values array
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = true)
    {
        if ($this->_options === null) {
            /* @var $storeCol \Magento\Store\Model\ResourceModel\Store\Collection */
            $storeCol = $this->_storeCollectionFactory->create();
            $storeCol->setLoadDefault(true);
            $this->_options = $storeCol->load()->toOptionArray();
        }
        return $this->_options;
    }
}
