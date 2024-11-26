<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Block\Page;

use Theiconnz\Campaigns\Api\CampaignRepositoryInterface;

/**
 * Campaigns page content block
 *
 * @api
 * @since 100.0.2
 */
class Campaign extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    private $_model=null;

    /**
     * Construct
     *
     * @param Context $context
     * @param CampaignRepositoryInterface $campaignRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CampaignRepositoryInterface $campaignRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->campaignRepository = $campaignRepository;
    }

    public function getPageId()
    {
        return $this->getData('pagecampaign');
    }

    /**
     * Retrieve Campaigns instance
     *
     * @return \Theiconnz\Campaigns\Model\Campaign
     */
    public function getPage()
    {
        if($this->_model==null) {
            $pgid= $this->getPageId();
            $this->_model = $this->campaignRepository->getById( (integer)$pgid );
        }
        return $this->_model;
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

}
