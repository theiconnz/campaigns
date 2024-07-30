<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Ui\Component\Listing\Columns\Column;
use Theiconnz\Campaigns\Api\CampaignRepositoryInterface;

/**
 * Class to build edit and delete link for each item.
 */
class Thumbnail extends Column
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CampaignRepositoryInterface $campaignRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CampaignRepositoryInterface $campaignRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $fieldName = $this->getData('name');
                $item[$fieldName . '_src'] = $this->prepareItem($item['imagename']);
            }
        }
        return $dataSource;
    }

    /**
     * @inheritDoc
     */
    protected function prepareItem($item)
    {
        $profilepath = $this->getStoreManager()->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) .
        \Theiconnz\Campaigns\Model\Results::UPLOADPATH;
        return ($item != null) ? sprintf("%s%s", $profilepath, $item) : '';
    }

    /**
     * Get StoreManager dependency
     *
     * @return StoreManager
     */
    private function getStoreManager()
    {
        if ($this->storeManager === null) {
            $this->storeManager = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Store\Model\StoreManagerInterface::class);
        }
        return $this->storeManager;
    }
}
