<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Block\Adminhtml;

/**
 * Adminhtml cms blocks content block
 */
class Campaign extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Theiconnz_Campaign';
        $this->_controller = 'adminhtml_campaign';
        $this->_headerText = __('Static Campaigns');
        $this->_addButtonLabel = __('Add New Campaigns');
        parent::_construct();
    }
}
