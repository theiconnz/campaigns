<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Model\Campaign\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Is Active Class to return is active data source
 */
class IsActive implements OptionSourceInterface
{
    /**
     * @var \Theiconnz\Campaigns\Model\Campaign
     */
    protected $campaign;

    /**
     * Constructor
     *
     * @param \Theiconnz\Campaigns\Model\Campaign $campaign
     */
    public function __construct(\Theiconnz\Campaigns\Model\Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->campaign->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
