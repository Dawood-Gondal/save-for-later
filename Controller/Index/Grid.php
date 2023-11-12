<?php
/**
 * @category    M2Commerce Enterprise
 * @package     M2Commerce_SaveForLater
 * @copyright   Copyright (c) 2023 M2Commerce Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

namespace M2Commerce\SaveForLater\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Grid
 */
class Grid implements \Magento\Framework\App\ActionInterface
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var Url
     */
    protected $customerUrl;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param ResultFactory $resultFactory
     * @param Session $customerSession
     * @param Url $customerUrl
     * @param RedirectFactory $redirectFactory
     */
    public function __construct(
        ResultFactory $resultFactory,
        Session       $customerSession,
        Url           $customerUrl,
        RedirectFactory $redirectFactory
    ) {
        $this->customerUrl = $customerUrl;
        $this->customerSession = $customerSession;
        $this->resultFactory = $resultFactory;
        $this->resultRedirectFactory = $redirectFactory;
    }

    /**
     * @return Redirect|ResultInterface
     */
    public function execute()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        } else {
            return $this->resultRedirectFactory->create()->setPath($this->customerUrl->getLoginUrl());
        }
    }
}
