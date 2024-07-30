<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Model\Campaign\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Theiconnz\Campaigns\Model\ResourceModel\Campaign\CollectionFactory;

/**
 * Class IsActive
 */
class Campaigns implements OptionSourceInterface
{

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->collectionFactory->create()
        ->addFieldToSelect('camp_id')
        ->addFieldToSelect('title');
        $options = [];
        foreach ($availableOptions as $value) {
            $options[] = [
                'label' => $value->getTitle(),
                'value' => $value->getCampId(),
            ];
        }
        return $options;
    }
}
