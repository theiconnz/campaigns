<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Controller\Adminhtml\Results;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Theiconnz\Campaigns\Api\ResultsRepositoryInterface;
use Theiconnz\Campaigns\Model\ResourceModel\Results\CollectionFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Theiconnz\Campaigns\Model\Results;
/**
 * Class MassEnable
 */
class MassDelete extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Theiconnz_Campaigns::resultssave';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ResultsRepositoryInterface;
     */
    protected $resultsRepository;

    protected $directoryList;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param DirectoryList $directoryList
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context, Filter $filter,
        CollectionFactory $collectionFactory,
        DirectoryList $directoryList,
        ResultsRepositoryInterface $resultsRepository
    )
    {
        $this->filter = $filter;
        $this->directoryList = $directoryList;
        $this->collectionFactory = $collectionFactory;
        $this->resultsRepository = $resultsRepository;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $productDeleted=$productDeletedError=0;

        foreach ($collection as $item) {
            try {
                $this->resultsRepository->delete($item);
                $this->deleteImages($item);
                $this->_eventManager->dispatch(
                    'campaign_adminhtml_result_delete_after',
                    ['account_controller' => $this, 'item' => $item, 'request' => $this->getRequest()]
                );
                $productDeleted++;
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getLogMessage());
                $productDeletedError++;
            }
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $collection->getSize())
        );

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }


    public function deleteImages($item){
        $mediaPath = $this->directoryList->getPath('media') . Results::UPLOADPATH;

        if($item) {
            $imagename=$item->getImagename();
            $imagename2=$item->getImage2name();
            if (is_file($mediaPath . $imagename2) && file_exists($mediaPath . $imagename)) {
                unlink($mediaPath . $imagename);
                $this->messageManager->addSuccessMessage(
                    __('File %1 delete success', $mediaPath . $imagename)
                );
            }
            if ( is_file($mediaPath . $imagename2) && file_exists($mediaPath . $imagename2)) {
                unlink($mediaPath . $imagename2);
                $this->messageManager->addSuccessMessage(
                    __('File %1 delete success', $mediaPath . $imagename2)
                );
            }
        }
    }
}
