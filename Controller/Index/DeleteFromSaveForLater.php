<?php
/**
 * @category    M2Commerce Enterprise
 * @package     M2Commerce_SaveForLater
 * @copyright   Copyright (c) 2023 M2Commerce Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

namespace M2Commerce\SaveForLater\Controller\Index;

use M2Commerce\SaveForLater\Model\ResourceModel\SavedItem\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

/**
 * Class DeleteFromSaveForLater
 */
class DeleteFromSaveForLater implements \Magento\Framework\App\ActionInterface
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
     * @var CollectionFactory
     */
    protected $savedItemCollectionFactory;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @param RequestInterface $request
     * @param MessageManagerInterface $messageManager
     * @param CollectionFactory $saveForLaterCollectionFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        RequestInterface $request,
        MessageManagerInterface $messageManager,
        CollectionFactory $saveForLaterCollectionFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->savedItemCollectionFactory = $saveForLaterCollectionFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $saveForLaterId = $this->request->getParam('saveForLaterId');
        if (isset($saveForLaterId)) {
            $savedItemCollection = $this->savedItemCollectionFactory->create();
            $savedItemObj = $savedItemCollection->addFieldToFilter('entity_id', ['eq' => $saveForLaterId])->getFirstItem();
            $savedItemObj->delete();
            $response = true;
        } else {
            $this->messageManager->addErrorMessage('Invalid quote item id.');
            $response = false;
        }
        return $this->resultJsonFactory->create()->setData(['response' => $response]);
    }
}
