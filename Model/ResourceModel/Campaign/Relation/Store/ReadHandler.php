<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Model\ResourceModel\Campaign\Relation\Store;

use Theiconnz\Campaigns\Model\ResourceModel\Campaign;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Read hander Class to return reading data
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var Block
     */
    protected $resourceCampaign;

    /**
     * @param Campaign $resourceCampaign
     */
    public function __construct(
        Campaign $resourceCampaign
    ) {
        $this->resourceCampaign = $resourceCampaign;
    }

    /**
     * Class execution method
     *
     * @param object $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $stores = $this->resourceCampaign->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
            $entity->setData('stores', $stores);
        }
        return $entity;
    }
}
