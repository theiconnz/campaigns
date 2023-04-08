<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for campaign search results.
 * @api
 * @since 0.0.1
 */
interface CampaignSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Campaigns list.
     *
     * @return \Theiconnz\Campaigns\Api\Data\CampaignInterface[]
     */
    public function getItems();

    /**
     * Set Campaigns list.
     *
     * @param \Theiconnz\Campaigns\Api\Data\CampaignInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
