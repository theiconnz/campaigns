<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Api;

/**
 * Campaigns CRUD interface.
 * @api
 * @since 100.0.2
 */
interface CampaignRepositoryInterface
{
    /**
     * Save Campaigns.
     *
     * @param \Theiconnz\Campaigns\Api\Data\CampaignInterface $campaign
     * @return \Theiconnz\Campaigns\Api\Data\CampaignInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\CampaignInterface $campaign);

    /**
     * Retrieve Campaigns.
     *
     * @param string $campaignId
     * @return \Theiconnz\Campaigns\Api\Data\CampaignInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($campaignId);

    /**
     * Retrieve Campaigns matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Theiconnz\Campaigns\Api\Data\CampaignSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Campaigns.
     *
     * @param \Theiconnz\Campaigns\Api\Data\CampaignInterface $campaign
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\CampaignInterface $campaign);

    /**
     * Delete Campaigns by ID.
     *
     * @param string $campaignId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($campaignId);
}
