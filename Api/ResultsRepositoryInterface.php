<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Api;

/**
 * Results CRUD interface.
 * @api
 * @since 100.0.2
 */
interface ResultsRepositoryInterface
{
    /**
     * Save Results.
     *
     * @param \Theiconnz\Campaigns\Api\Data\ResultsInterface $campaign
     * @return \Theiconnz\Campaigns\Api\Data\ResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\ResultsInterface $campaign);

    /**
     * Retrieve Results.
     *
     * @param string $campaignId
     * @return \Theiconnz\Campaigns\Api\Data\ResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($campaignId);

    /**
     * Retrieve Resultss matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Theiconnz\Campaigns\Api\Data\ResultsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Results.
     *
     * @param \Theiconnz\Campaigns\Api\Data\ResultsInterface $campaign
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\ResultsInterface $campaign);

    /**
     * Delete Results by ID.
     *
     * @param string $campaignId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($campaignId);
}
