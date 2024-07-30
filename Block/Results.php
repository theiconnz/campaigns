<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Block;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Template;
use Theiconnz\Campaigns\Model\ResourceModel\Results\CollectionFactory;

/**
 * Campaigns results page
 *
 * @api
 * @since 100.0.2
 */
class Results extends Template
{
    /**
     * @var \Theiconnz\Campaigns\Model\Campaign
     */
    protected $_campaign;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Theiconnz\Campaigns\Model\CampaignFactory
     */
    protected $_campaignFactory;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Theiconnz\Campaigns\Model\ResourceModel\Results\CollectionFactory
     */
    protected $_resultsCollectionFactory;

    /**
     * Construct
     *
     * @param Template\Context $context
     * @param \Theiconnz\Campaigns\Model\Campaign $page
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Theiconnz\Campaigns\Model\CampaignFactory $pageFactory
     * @param \Theiconnz\Campaigns\Model\ResourceModel\Results\CollectionFactory $resultsCollectionFactory
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Theiconnz\Campaigns\Model\Campaign $page,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Theiconnz\Campaigns\Model\CampaignFactory $pageFactory,
        CollectionFactory $resultsCollectionFactory,
        \Magento\Framework\View\Page\Config $pageConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        // used singleton (instead factory) because there exist dependencies on \Theiconnz\Campaigns\Helper\Results
        $this->_campaign = $page;
        $this->_storeManager = $storeManager;
        $this->_campaignFactory = $pageFactory;
        $this->pageConfig = $pageConfig;
        $this->_resultsCollectionFactory = $resultsCollectionFactory;
    }

    /**
     * Retrieve Results instance
     *
     * @return \Theiconnz\Campaigns\Model\Results
     */
    public function getPage()
    {
        if (!$this->hasData('page')) {
            if ($this->getPageId()) {
                /** @var \Theiconnz\Campaigns\Model\Campaign $page */
                $page = $this->_campaignFactory->create();
                $page->setStoreId($this->_storeManager->getStore()->getId())
                    ->load($this->getPageId());
            } else {
                $page = $this->_campaign;
            }
            $this->setData('page', $page);
        }
        return $this->getData('page');
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $page = $this->getPage();
        $this->pageConfig->addBodyClass('campaign-' . $page->getIdentifier());
        $metaTitle = $page->getMetaTitle();
        $this->pageConfig->getTitle()->set($metaTitle ? $metaTitle : $page->getTitle());
        $this->pageConfig->setKeywords($page->getMetaKeywords());
        $this->pageConfig->setDescription($page->getMetaDescription());

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            // Setting empty page title if content heading is absent
            $cmsTitle = $page->getContentHeading() ?: ' ';
            $pageMainTitle->setPageTitle($this->escapeHtml($cmsTitle));
        }
        return parent::_prepareLayout();
    }

    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getCampaignId()
    {
        return $this->getPage()->getId();
    }

    /**
     * Returns weburl of the image
     *
     * @return string|boolean
     */
    public function getImageUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) .
            \Theiconnz\Campaigns\Model\Results::UPLOADPATH;
    }

    /**
     * Returns results collection
     *
     * @return string
     */
    public function getResultsCollection()
    {
        $storeid = $this->_storeManager->getStore()->getId();
        $collection = $this->_resultsCollectionFactory->create()
            ->addFieldToSelect('name')
            ->addFieldToSelect('content')
            ->addFieldToSelect('imagename')
            ->addFieldToFilter('is_active', ['eq' => 1])
            ->setOrder(
                'creation_time',
                'desc'
            );
        return $collection;
    }
}
