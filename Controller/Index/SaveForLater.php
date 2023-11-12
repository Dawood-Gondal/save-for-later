<?php
/**
 * @category    M2Commerce Enterprise
 * @package     M2Commerce_SaveForLater
 * @copyright   Copyright (c) 2023 M2Commerce Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

namespace M2Commerce\SaveForLater\Controller\Index;

use M2Commerce\SaveForLater\Model\SavedItemFactory;
use M2Commerce\SaveForLater\Model\SaveForLaterFactory;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

/**
 * Class SaveForLater
 */
class SaveForLater implements \Magento\Framework\App\ActionInterface
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var SaveForLaterFactory
     */
    protected $savedItemFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @param RequestInterface $request
     * @param Session $checkoutSession
     * @param CustomerSession $customerSession
     * @param SavedItemFactory $savedItemFactory
     * @param JsonFactory $resultJsonFactory
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        RequestInterface $request,
        Session          $checkoutSession,
        CustomerSession  $customerSession,
        SavedItemFactory $savedItemFactory,
        JsonFactory      $resultJsonFactory,
        MessageManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->savedItemFactory = $savedItemFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $quoteItemId = $this->request->getParam('qouteItemId');
        if (isset($quoteItemId))
        {
            $quote = $this->checkoutSession->getQuote();
            $quoteItem = $quote->getItemById($quoteItemId);
            $buyInfoRequest = $quoteItem->getBuyRequest();
            //if buy request available, delete original quote item
            if ($buyInfoRequest)
            {
                $serializedBuyInfoRequest = json_encode($buyInfoRequest->getData(), true);
                $savedItem = $this->savedItemFactory->create();
                $savedItem->setQouteItemRequestInfo($serializedBuyInfoRequest);
                $savedItem->setCustomerId($this->customerSession->getCustomerId());
                if ($quoteItem->getProductType() == "grouped") {
                    $savedItem->setGroupProductId($quoteItem->getProductId());
                }
                $savedItem->save();
                $quote->removeItem($quoteItemId);
                $quote->setTotalsCollectedFlag(false);
                $quote->collectTotals();
                $quote->save();
            }

            $response = true;
        } else {
            $this->messageManager->addErrorMessage('Invalid quote item id.');
            $response = false;
        }
        return $this->resultJsonFactory->create()->setData(['response' => $response]);
    }
}
