<?php
/**
 * @category    M2Commerce Enterprise
 * @package     M2Commerce_SaveForLater
 * @copyright   Copyright (c) 2023 M2Commerce Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

namespace M2Commerce\SaveForLater\Block\SavedItems;

use M2Commerce\SaveForLater\Model\ResourceModel\SavedItem\Collection;
use M2Commerce\SaveForLater\Model\ResourceModel\SavedItem\CollectionFactory;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Magento\Checkout\Helper\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class Grid
 */
class Grid extends \Magento\Checkout\Block\Cart
{
    /**
     * @var CollectionFactory
     */
    protected $savedItemCollection;

    /**
     * @var ProductCollection
     */
    protected $productCollectionFactory;

    /**
     * @var ImageBuilder
     */
    protected $imageBuilder;

    /**
     * @var AttributeFactory
     */
    protected $eavAttribute;

    /**
     * @var Data
     */
    protected $pricingHelper;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @param TemplateContext $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Session $checkoutSession
     * @param Url $catalogUrlBuilder
     * @param Cart $cartHelper
     * @param Context $httpContext
     * @param CollectionFactory $savedItemCollection
     * @param ProductCollection $productCollectionFactory
     * @param ImageBuilder $imageBuilder
     * @param AttributeFactory $eavAttribute
     * @param Data $pricingHelper
     * @param ProductRepository $productRepository
     * @param array $data
     */
    public function __construct(
        TemplateContext                 $context,
        \Magento\Customer\Model\Session $customerSession,
        Session                         $checkoutSession,
        Url                             $catalogUrlBuilder,
        Cart                            $cartHelper,
        Context                         $httpContext,
        CollectionFactory               $savedItemCollection,
        ProductCollection               $productCollectionFactory,
        ImageBuilder                    $imageBuilder,
        AttributeFactory                $eavAttribute,
        Data                            $pricingHelper,
        ProductRepository                                $productRepository,
        array                                            $data = []
    ) {
        $this->pricingHelper = $pricingHelper;
        $this->savedItemCollection = $savedItemCollection;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageBuilder = $imageBuilder;
        $this->eavAttribute = $eavAttribute;
        $this->productRepository = $productRepository;
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $catalogUrlBuilder,
            $cartHelper,
            $httpContext,
            $data
        );
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_customerSession->getCustomerId();
    }

    /**
     * @return Collection
     */
    public function getSaveForLaterItems()
    {
        /** @var Collection $savedItemCollection */
        $savedItemCollection = $this->savedItemCollection->create();
        $savedItemCollection->addFieldToFilter('customer_id', ['eq' => $this->getCustomerId()]);
        return $savedItemCollection;
    }

    /**
     * @param int $productId
     * @return Product
     */
    public function getProduct(int $productId)
    {
        $productCollection = $this->productCollectionFactory->create();
        /** @var Product $productItem */
        $productItem = $productCollection->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['eq' => $productId])
            ->getFirstItem();
        return $productItem;
    }

    /**
     * @param Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage(Product $product, string $imageId, array $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * @param Product $productItem
     * @param int $key
     * @param string $value
     * @return string
     */
    public function getConfiguration(Product $productItem, int $key, string $value)
    {
        $eavAttribute = $this->eavAttribute->create()->load($key);
        $isAttributeExist = $productItem->getResource()->getAttribute($eavAttribute->getAttributeCode());
        $optionText = "<strong>" . $eavAttribute->getFrontendLabel() . "</strong>";
        if ($isAttributeExist && $isAttributeExist->usesSource()) {
            $optionText .= ': ' . $isAttributeExist->getSource()->getOptionText($value);
        }
        return $optionText;
    }


    /**
     * @return bool
     */
    public function getCustomerSession()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * @param float $value
     * @return float|string
     */
    public function getPrice(float $value)
    {
        return $this->pricingHelper->currency($value, true, false);
    }

    /**
     * @param Product $product
     * @param int $key
     * @param string $value
     * @return array
     */
    public function getBundleProductOption(Product $product, int $key, string $value)
    {
        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection
        ($product->getTypeInstance(true)->getOptionsIds($product), $product);
        foreach ($selectionCollection as $proselection) {
            $selectionArray = [];
            $selectionArray['selection_product_name'] = $proselection->getName();
            $selectionArray['selection_product_price'] = $proselection->getFinalPrice();
            $selectionArray['selection_product_qty'] = intval($proselection->getSelectionQty());
            $productsArray[$proselection->getOptionId()][$proselection->getSelectionId()] = $selectionArray;
        }
        //get all options of product
        $optionsCollection = $product->getTypeInstance(true)->getOptionsCollection($product);
        foreach ($optionsCollection as $options) {
            $optionArray[$options->getOptionId()]['option_title'] = $options->getDefaultTitle();
            $optionArray[$options->getOptionId()]['option_type'] = $options->getType();
        }

        $bundleOption = [];
        $bundleOption['option_title'] = "<strong>" . $optionArray[$key]['option_title'] . "</strong>";
        $bundleOption['value'] = $productsArray[$key][intval($value)]['selection_product_name'];
        $bundleOption['price'] = $productsArray[$key][intval($value)]['selection_product_price'];
        $bundleOption['selection_qty'] = $productsArray[$key][intval($value)]['selection_product_qty'];
        return $bundleOption;
    }

    /**
     * @return \Magento\Checkout\Block\Cart
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set('Saved For Later');
        return parent::_prepareLayout();
    }

    /**
     * @param $sku
     * @param $supperAttribut
     * @return \Magento\Catalog\Api\Data\ProductInterface|Product|mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductBySku($sku, $supperAttribut)
    {
        $product = $this->productRepository->get($sku);
        if ($product->getTypeId() == 'configurable') {
            $child = $product->getTypeInstance()->getUsedProducts($product);
            $matchedChildProduct = null;
            foreach ($child as $children) {
                $match = true;
                foreach ($supperAttribut as $key => $value) {
                    $attr = $this->eavAttribute->create()->load($key);
                    $attributeValue = $children->getData($attr->getAttributeCode());
                    if ($value != $attributeValue) {
                        $match = false;
                        break;
                    }
                }
                if ($match) {
                    $matchedChildProduct = $children;
                    break;
                }
            }
        }
        return $matchedChildProduct ? $matchedChildProduct : $product;

    }
}
