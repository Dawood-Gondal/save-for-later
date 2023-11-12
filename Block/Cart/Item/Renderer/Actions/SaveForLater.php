<?php
/**
 * @category    M2Commerce Enterprise
 * @package     M2Commerce_SaveForLater
 * @copyright   Copyright (c) 2023 M2Commerce Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

namespace M2Commerce\SaveForLater\Block\Cart\Item\Renderer\Actions;

use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;

/**
 * Class SaveForLater
 */
class SaveForLater extends Generic
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Template\Context $context
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Session $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function getLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * @return int
     */
    public function getQouteItemId()
    {
        return $this->getItem()->getId();
    }

    /**
     * @return mixed
     */
    public function getQouteItemProductType(){
        return $this->getItem()->getProductType();
    }

    /**
     * @return mixed
     */
    public function getSaveForLaterEnable(){
        return $this->_scopeConfig->getValue('saveForLater/general/isEnable');
    }
}
