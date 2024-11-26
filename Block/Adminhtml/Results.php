<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Block\Adminhtml;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use \Theiconnz\Campaigns\Model\ResultsFactory;
/**
 * Adminhtml cms blocks content block
 */

class Results extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var Theiconnz\Campaigns\Api\ResultsRepositoryInterface $resultsRepository
     */
    private $resultsRepository;


    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Theiconnz\Campaigns\Api\ResultsRepositoryInterface $resultsRepository
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param StoreManager $storeManager
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Theiconnz\Campaigns\Api\ResultsRepositoryInterface $resultsRepository,
        \Magento\Backend\Helper\Data $adminhtmlData,
        StoreManager $storeManager,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->resultsRepository = $resultsRepository;
        $this->_storeManager = $storeManager;
        $this->_adminhtmlData = $adminhtmlData;
    }

    public function getResult()
    {
        $id = $this->getRequest()->getParam('r_id');
        $model = $this->resultsRepository->getById($id);
        if (!$model->getId()) {
            return false;
        }
        return $model;
    }


    public function getImageUrl($item)
    {
        $profilepath = $this->_storeManager->getStore()->getBaseUrl(  UrlInterface::URL_TYPE_MEDIA ) .
            \Theiconnz\Campaigns\Model\Results::UPLOADPATH;
        return ($item!=null || $item!='') ? sprintf("%s%s",$profilepath,$item) : '';
    }

    /**
     * Prepare URL rewrite editing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_addBackButton();

        return parent::_prepareLayout();
    }


    /**
     * Add back button
     *
     * @return void
     */
    protected function _addBackButton()
    {
        $this->addButton(
            'back',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->_adminhtmlData->getUrl('campaigns/results/') . '\')',
                'class' => 'back',
                'level' => -1
            ]
        );
    }

}
