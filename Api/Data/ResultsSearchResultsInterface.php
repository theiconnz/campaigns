<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for results search results.
 *
 * @api
 * @since 0.0.1
 */
interface ResultsSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Results list.
     *
     * @return \Theiconnz\Campaigns\Api\Data\ResultsInterface[]
     */
    public function getItems();

    /**
     * Set Results list.
     *
     * @param \Theiconnz\Campaigns\Api\Data\ResultsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
