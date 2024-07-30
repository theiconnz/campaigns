<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Helper;

use Theiconnz\Campaigns\Model\Campaign\IdentityMap;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\Page as ResultPage;

/**
 * CMS Page Helper
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Results extends AbstractHelper
{
    /**
     * CMS no-route config path
     */
    public const XML_PATH_NO_ROUTE_PAGE = 'web/default/cms_no_route';

    /**
     * CMS no cookies config path
     */
    public const XML_PATH_NO_COOKIES_PAGE = 'web/default/cms_no_cookies';

    /**
     * CMS home page config path
     */
    public const XML_PATH_HOME_PAGE = 'web/default/cms_home_campaign';

    /**
     * Design package instance
     *
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $_design;

    /**
     * @var \Theiconnz\Campaigns\Model\Campaign
     */
    protected $_campaign;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
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
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var IdentityMap
     */
    private $identityMap;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Theiconnz\Campaigns\Model\Campaign $campaign
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param IdentityMap|null $identityMap
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Theiconnz\Campaigns\Model\Campaign $campaign,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        IdentityMap $identityMap
    ) {
        $this->messageManager = $messageManager;
        $this->_campaign = $campaign;
        $this->_design = $design;
        $this->_campaignFactory = $pageFactory;
        $this->_storeManager = $storeManager;
        $this->_localeDate = $localeDate;
        $this->_escaper = $escaper;
        $this->resultPageFactory = $resultPageFactory;
        $this->identityMap = $identityMap;
        parent::__construct($context);
    }

    /**
     * Return result CMS page
     *
     * @param ActionInterface $action
     * @param int $pageId
     * @return ResultPage|bool
     * @throws NoSuchEntityException
     */
    public function prepareResultPage(ActionInterface $action, $pageId = null)
    {
        if ($pageId !== null && $pageId !== $this->_campaign->getId()) {
            $delimiterPosition = strrpos((string)$pageId, '|');
            if ($delimiterPosition) {
                $pageId = substr($pageId, 0, $delimiterPosition);
            }

            $this->_campaign->setStoreId($this->_storeManager->getStore()->getId());
            if (!$this->_campaign->load($pageId)) {
                return false;
            }
        }

        if (!$this->_campaign->getId()) {
            return false;
        }
        $this->identityMap->add($this->_campaign);

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('campaigns_campaign_results');
        //Selected custom updates.

        $this->_eventManager->dispatch(
            'campaigns_campaign_render',
            ['page' => $this->_campaign, 'controller_action' => $action, 'request' => $this->_getRequest()]
        );

        $contentHeadingBlock = $resultPage->getLayout()->getBlock('page_content_heading');
        if ($contentHeadingBlock) {
            $contentHeading = $this->_escaper->escapeHtml($this->_campaign->getContentHeading());
            $contentHeadingBlock->setContentHeading($contentHeading);
        }

        return $resultPage;
    }

    /**
     * Retrieve page direct URL
     *
     * @param string $pageId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPageUrl($pageId = null)
    {
        /** @var \Theiconnz\Campaigns\Model\Campaign $campaign */
        $page = $this->_campaignFactory->create();
        if ($pageId !== null) {
            $page->setStoreId($this->_storeManager->getStore()->getId());
            $page->load($pageId);
        }

        if (!$page->getId()) {
            return null;
        }

        return $this->_urlBuilder->getUrl(null, ['_direct' => $page->getIdentifier()]);
    }

    /**
     * Set layout type
     *
     * @param bool $inRange
     * @param ResultPage $resultPage
     * @return ResultPage
     */
    protected function setLayoutType($inRange, $resultPage)
    {
        if ($this->_campaign->getPageLayout()) {
            if ($this->_campaign->getCustomPageLayout()
                && $this->_campaign->getCustomPageLayout() != 'empty'
                && $inRange
            ) {
                $handle = $this->_campaign->getCustomPageLayout();
            } else {
                $handle = $this->_campaign->getPageLayout();
            }
            $resultPage->getConfig()->setPageLayout($handle);
        }
        return $resultPage;
    }
}
