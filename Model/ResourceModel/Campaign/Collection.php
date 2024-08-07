<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Model\ResourceModel\Campaign;

use Theiconnz\Campaigns\Api\Data\CampaignInterface;
use \Theiconnz\Campaigns\Model\ResourceModel\AbstractCollection;

/**
 * Campaigns Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'camp_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'campaign_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'campagin_collection';

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(CampaignInterface::class);

        $this->performAfterLoad('campaign_store', $entityMetadata->getLinkField());

        return parent::_afterLoad();
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Theiconnz\Campaigns\Model\Campaign::class,
            \Theiconnz\Campaigns\Model\ResourceModel\Campaign::class
        );
        $this->_map['fields']['store_id'] = 'store_table.store_id';
    }

    /**
     * Returns pairs camp_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('camp_id', 'title');
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(CampaignInterface::class);
        $this->joinStoreRelationTable('campaign_store', $entityMetadata->getLinkField());
    }
}
