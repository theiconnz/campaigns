<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Controller\Adminhtml\Campaign;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action;

/**
 * Edit Campaigns page action.
 */
class Edit extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Theiconnz_Campaigns::save';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;


    /**
     * @var Theiconnz\Campaigns\Api\CampaignRepositoryInterface $campaignRepository
     */
    private $campaignRepository;

    /**
     * @var Theiconnz\Campaigns\Model\CampaignFactory $campaignFactory
     */
    private $campaignFactory;


    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Theiconnz\Campaigns\Api\CampaignRepositoryInterface $campaignRepository
     * @param \Theiconnz\Campaigns\Model\CampaignFactory $campaignFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Theiconnz\Campaigns\Api\CampaignRepositoryInterface $campaignRepository,
        \Theiconnz\Campaigns\Model\CampaignFactory $campaignFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->campaignRepository = $campaignRepository;
        $this->campaignFactory = $campaignFactory;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Theiconnz_Campaigns::campaign')
            ->addBreadcrumb(__('Campaigns'), __('Campaigns'))
            ->addBreadcrumb(__('Manage Campaigns'), __('Manage Campaigns'));
        return $resultPage;
    }

    /**
     * Edit Campaigns page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('camp_id');
        if($id) {
            $model = $this->campaignRepository->getById($id);
        } else {
            $model = $this->campaignFactory->create();
        }

        // 2. Initial checking
        if ($id) {
            $model = $this->campaignRepository->getById($id);
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This page no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('campaign', $model);
        $this->_eventManager->dispatch(
            'campaign_adminhtml_edit_after',
            ['account_controller' => $this, 'model' => $model, 'request' => $this->getRequest()]
        );

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Campaigns') : __('New Campaigns'),
            $id ? __('Edit Campaigns') : __('New Campaigns')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Campaigns'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Campaigns'));

        return $resultPage;
    }
}
