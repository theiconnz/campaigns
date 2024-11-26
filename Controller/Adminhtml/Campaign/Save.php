<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Theiconnz\Campaigns\Controller\Adminhtml\Campaign;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Theiconnz\Campaigns\Api\Data\CampaignInterface;
use Theiconnz\Campaigns\Api\CampaignRepositoryInterface;
use Theiconnz\Campaigns\Model\Campaign;
use Theiconnz\Campaigns\Model\CampaignFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Save CMS page action.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Theiconnz_Campaigns::save';

    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var CampaignFactory
     */
    private $campaignFactory;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     * @param CampaignFactory|null $campaignFactory
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        CampaignFactory $campaignFactory,
        CampaignRepositoryInterface $campaignRepository
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->campaignFactory = $campaignFactory;
        $this->campaignRepository = $campaignRepository;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Campaign::STATUS_ENABLED;
            }
            if (empty($data['camp_id'])) {
                $data['camp_id'] = null;
            }

            /** @var Campaign $model */
            $model = $this->campaignFactory->create();

            $id = $this->getRequest()->getParam('camp_id');
            if ($id) {
                try {
                    $model = $this->campaignRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This campaign no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                $this->campaignRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the campaign.'));
                return $this->processResultRedirect($model, $resultRedirect, $data);
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the campaign.'));
            }

            $this->dataPersistor->set('campaign', $data);
            return $resultRedirect->setPath('*/*/edit', ['camp_id' => $this->getRequest()->getParam('camp_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process result redirect
     *
     * @param CampaignInterface $model
     * @param Redirect $resultRedirect
     * @param array $data
     * @return Redirect
     * @throws LocalizedException
     */
    private function processResultRedirect($model, $resultRedirect, $data)
    {
        $this->dataPersistor->clear('campaign');
        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', ['camp_id' => $model->getId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
