<?php
/**
 * @category    M2Commerce Enterprise
 * @package     M2Commerce_SaveForLater
 * @copyright   Copyright (c) 2023 M2Commerce Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

namespace M2Commerce\SaveForLater\Controller\Index;

use M2Commerce\SaveForLater\Model\ResourceModel\SavedItem\CollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\CartFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

/**
 * Class RetrieveToCart
 */
class RetrieveToCart implements \Magento\Framework\App\ActionInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var CartFactory
     */
    protected $cartFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var CollectionFactory
     */
    protected $savedItemCollectionFactory;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @param RequestInterface $request
     * @param JsonFactory $resultJsonFactory
     * @param CartFactory $cartFactory
     * @param ProductFactory $productFactory
     * @param CollectionFactory $saveForLaterCollectionFactory
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        RequestInterface $request,
        JsonFactory $resultJsonFactory,
        CartFactory $cartFactory,
        ProductFactory $productFactory,
        CollectionFactory $saveForLaterCollectionFactory,
        MessageManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->savedItemCollectionFactory = $saveForLaterCollectionFactory;
        $this->cartFactory = $cartFactory;
        $this->productFactory = $productFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $saveForLaterId = $this->request->getParam('saveForLaterId');
        if ($saveForLaterId) {
            $savedItemCollection = $this->savedItemCollectionFactory->create();
            $savedItemObj = $savedItemCollection->addFieldToFilter('entity_id', ['eq' => $saveForLaterId])->getFirstItem();
            $unSerializedBuyInfoRequest = json_decode($savedItemObj->getQouteItemRequestInfo(), true);

            if ($savedItemObj->getGroupProductId()) {
                $productId = $savedItemObj->getGroupProductId();
            } else {
                $productId = $unSerializedBuyInfoRequest['product'];
            }

            $product = $this->productFactory->create()->load($productId);
            /**
             * @var \Magento\Checkout\Model\Cart $cart
             */
            $cart = $this->cartFactory->create();
            $cart->addProduct($product, $unSerializedBuyInfoRequest);
            $cart->save();
            $savedItemObj->delete();
            $response = true;
        } else {
            $this->messageManager->addErrorMessage('Invalid quote item id.');
            $response = false;
        }
        return $this->resultJsonFactory->create()->setData(['response' => $response]);
    }
}
