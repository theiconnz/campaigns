<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Block\Widget;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Template;
use Magento\Cms\Model\Template\FilterProvider;

/**
 * Campaigns page content block
 *
 * @api
 * @since 100.0.2
 */
class Campaign extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = 'Theiconnz_Campaigns::forms.phtml';


    /**
     * @var \Theiconnz\Campaigns\Model\Campaign
     */
    protected $_campaign;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Campaigns factory
     *
     * @var \Theiconnz\Campaigns\Model\CampaignFactory
     */
    protected $_campaignFactory;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Theiconnz\Campaigns\Model\Campaign $page
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Theiconnz\Campaigns\Model\CampaignFactory $pageFactory
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param Template\Context $context
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Theiconnz\Campaigns\Model\Campaign $page,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Theiconnz\Campaigns\Model\CampaignFactory $pageFactory,
        \Magento\Framework\View\Page\Config $pageConfig,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_campaign = $page;
        $this->_storeManager = $storeManager;
        $this->_campaignFactory = $pageFactory;
        $this->pageConfig = $pageConfig;
        $this->_filterProvider = $filterProvider;
    }

    /**
     * Retrieve Campaigns instance
     *
     * @return \Theiconnz\Campaigns\Model\Campaign
     */
    public function getPage()
    {
        if (!$this->hasData('page')) {
            if ($this->getPageId()) {
                /** @var \Theiconnz\Campaigns\Model\Campaign $page */
                $page = $this->_campaignFactory->create();
                $page->setStoreId($this->_storeManager->getStore()->getId())->load($this->getPageId(), 'camp_id');
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
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Theiconnz\Campaigns\Model\Campaign::CACHE_TAG . '_' . $this->getPage()->getId()];
    }


    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('campaigns/campaign/post', ['_secure' => true]);
    }

    /**
     * get Campain success message block
     *
     * @return string
     */
    public function getSuccessBlockMessage()
    {
        return $this->getUrl('campaigns/campaign/post', ['_secure' => true]);
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

    public function getCmsFilterContent($value='')
    {
        $html = $this->_filterProvider->getPageFilter()->filter($value);
        return $html;
    }
}
