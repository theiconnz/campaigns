<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Block\Adminhtml;

use \Theiconnz\Campaigns\Model\ResultsFactory;
/**
 * Adminhtml cms blocks content block
 */

class Results extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param Theiconnz\Campaigns\Model\ResultsFactory $resultsFactory
     * @param \Magento\Framework\Registry $registry
     */
    private $resultsFactory;

    public function __construct(
        ResultsFactory $resultsFactory,
        \Magento\Framework\Registry $registry
    )
    {
        $this->resultsFactory = $resultsFactory;
        $this->_coreRegistry = $registry;
    }

    protected function getResult()
    {
        return $this->_coreRegistry->registry('result');
    }

}
