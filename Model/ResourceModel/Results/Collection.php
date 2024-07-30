<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Model\ResourceModel\Results;

use Theiconnz\Campaigns\Api\Data\ResultsInterface;
use \Theiconnz\Campaigns\Model\ResourceModel\AbstractCollection;

/**
 * Results Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'r_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'results_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'results_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Theiconnz\Campaigns\Model\Results::class,
            \Theiconnz\Campaigns\Model\ResourceModel\Results::class
        );
        $this->_map['fields']['store_id'] = 'store_table.store_id';
    }

    /**
     * Returns pairs r_id - name
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('r_id', 'name');
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
     * @throws \Exception
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(ResultsInterface::class);
    }
}
