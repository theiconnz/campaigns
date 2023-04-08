<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Theiconnz\Campaigns\Controller\Campaign;

use Theiconnz\Campaigns\Helper\Campaign as CampaignHelper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultsInterface;

/**
 * Custom page for storefront. Needs to be accessible by POST because of the store switching.
 */
class View extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CampaignHelper
     */
    private $campaignHelper;

    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param CampaignHelper $campaignHelper
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        CampaignHelper $campaignHelper,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->campaignHelper = $campaignHelper;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * View CMS page action
     *
     * @return ResultsInterface
     */
    public function execute()
    {
        $resultCampaign = $this->campaignHelper->prepareResultPage($this, $this->getPageId());
        if (!$resultCampaign) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        return $resultCampaign;
    }

    /**
     * Returns Page ID if provided or null
     *
     * @return int|null
     */
    private function getPageId(): ?int
    {
        $id = $this->request->getParam('page_id') ?? $this->request->getParam('id');

        return $id ? (int)$id : null;
    }
}
