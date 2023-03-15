<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Model\ResourceModel\Campaign\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Theiconnz\Campaigns\Api\Data\CampaignInterface;
use Theiconnz\Campaigns\Model\ResourceModel\Campaign;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Campaign
     */
    protected $resourceCampaign;

    /**
     * @param MetadataPool $metadataPool
     * @param Campaign $resourceCampaign
     */
    public function __construct(
        MetadataPool $metadataPool,
        Campaign $resourceCampaign
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceCampaign = $resourceCampaign;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(CampaignInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();

        $oldStores = $this->resourceCampaign->lookupStoreIds((int)$entity->getId());
        $newStores = (array)$entity->getStores();

        $table = $this->resourceCampaign->getTable('campaign_store');

        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'store_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    $linkField => (int)$entity->getData($linkField),
                    'store_id' => (int)$storeId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}
