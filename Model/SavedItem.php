<?php
/**
 * @category    M2Commerce Enterprise
 * @package     M2Commerce_SaveForLater
 * @copyright   Copyright (c) 2023 M2Commerce Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

namespace M2Commerce\SaveForLater\Model;

/**
 * Class SaveForLater
 */
class SavedItem extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init(\M2Commerce\SaveForLater\Model\ResourceModel\SavedItem::class);
    }
}
