<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Theiconnz\Campaigns\Model;

use Theiconnz\Campaigns\Api\Data\CampaignSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Service Data Object with Block search results.
 */
class CampaignSearchResults extends SearchResults implements CampaignSearchResultsInterface
{
}
