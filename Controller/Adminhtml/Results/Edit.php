<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Controller\Adminhtml\Results;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action;

/**
 * Edit Results page action.
 */
class Edit extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Theiconnz_Campaigns::resultssave';

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
     * @var Theiconnz\Campaigns\Api\ResultsRepositoryInterface $resultsRepository
     */
    private $resultsRepository;

    /**
     * @var Theiconnz\Campaigns\Model\ResultsFactory $resultsFactory
     */
    private $resultsFactory;


    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Theiconnz\Campaigns\Api\ResultsRepositoryInterface $resultsRepository
     * @param \Theiconnz\Campaigns\Model\ResultsFactory $resultsFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Theiconnz\Campaigns\Api\ResultsRepositoryInterface $resultsRepository,
        \Theiconnz\Campaigns\Model\ResultsFactory $resultsFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->resultsRepository = $resultsRepository;
        $this->resultsFactory = $resultsFactory;
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
        $resultPage->setActiveMenu('Theiconnz_Campaigns::resultssave')
            ->addBreadcrumb(__('Result'), __('Result'))
            ->addBreadcrumb(__('Manage Result'), __('Manage Result'));
        return $resultPage;
    }

    /**
     * Edit Results page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('r_id');
        if($id) {
            $model = $this->resultsRepository->getById($id);
        } else {
            $model = $this->resultsFactory->create();
        }

        // 2. Initial checking
        if ($id) {
            $model = $this->resultsRepository->getById($id);
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This result no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('result', $model);
        $this->_eventManager->dispatch(
            'campaign_adminhtml_result_edit_after',
            ['account_controller' => $this, 'model' => $model, 'request' => $this->getRequest()]
        );

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Result') : __('New Result'),
            $id ? __('Edit Result') : __('New Result')
        );


        return $resultPage;
    }
}
