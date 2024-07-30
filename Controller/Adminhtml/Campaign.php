<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Controller\Adminhtml;

abstract class Campaign extends \Magento\Backend\App\Action
{
    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Theiconnz_Campaigns::campaign';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Theiconnz_Campaigns::campaign')
            ->addBreadcrumb(__('Campaigns'), __('Campaigns'))
            ->addBreadcrumb(__('Campaigns'), __('Campaigns'));
        return $resultPage;
    }
}
