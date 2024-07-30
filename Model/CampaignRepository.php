<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Theiconnz\Campaigns\Model;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\App\Route\Config;
use Theiconnz\Campaigns\Api\CampaignRepositoryInterface;
use Theiconnz\Campaigns\Api\Data;
use Theiconnz\Campaigns\Model\ResourceModel\Campaign as ResourceCampaign;
use Theiconnz\Campaigns\Model\ResourceModel\Campaign\CollectionFactory as CampaignCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\EntityManager\HydratorInterface;

/**
 * Default block repo impl.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CampaignRepository implements CampaignRepositoryInterface
{
    /**
     * @var ResourceCampaign
     */
    protected $resource;

    /**
     * @var CampaignFactory
     */
    protected $campaignFactory;

    /**
     * @var CampaignCollectionFactory
     */
    protected $campaignCollectionFactory;

    /**
     * @var Data\CampaignSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Theiconnz\Campaigns\Api\Data\CampaignInterfaceFactory
     */
    protected $dataCampaignFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var Config
     */
    private $routeConfig;

    /**
     * @param ResourceCampaign $resource
     * @param CampaignFactory $campaignFactory
     * @param Data\CampaignInterfaceFactory $dataCampaignFactory
     * @param CampaignCollectionFactory $campaignCollectionFactory
     * @param Data\CampaignSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param HydratorInterface|null $hydrator
     * @param Config|null $routeConfig
     */
    public function __construct(
        ResourceCampaign $resource,
        CampaignFactory $campaignFactory,
        \Theiconnz\Campaigns\Api\Data\CampaignInterfaceFactory $dataCampaignFactory,
        CampaignCollectionFactory $campaignCollectionFactory,
        Data\CampaignSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        HydratorInterface $hydrator,
        Config $routeConfig
    ) {
        $this->resource = $resource;
        $this->campaignFactory = $campaignFactory;
        $this->campaignCollectionFactory = $campaignCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCampaignFactory = $dataCampaignFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->hydrator = $hydrator;
        $this->routeConfig = $routeConfig;
    }

    /**
     * Save Campaigns data
     *
     * @param \Theiconnz\Campaigns\Api\Data\CampaignInterface $campaign
     * @return Campaign
     * @throws CouldNotSaveException
     */
    public function save(Data\CampaignInterface $campaign)
    {
        if (empty($campaign->getStoreId())) {
            $campaign->setStoreId($this->storeManager->getStore()->getId());
        }

        if ($campaign->getId() && $campaign instanceof Campaign && !$campaign->getOrigData()) {
            $campaign = $this->hydrator->hydrate(
                $this->getById($campaign->getId()),
                $this->hydrator->extract($campaign)
            );
        }

        try {
            $this->validateRoutesDuplication($campaign);
            $this->resource->save($campaign);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $campaign;
    }

    /**
     * Load Campaigns data by given Campaigns Identity
     *
     * @param string $campid
     * @return Campaign
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($campid)
    {
        $campaign = $this->campaignFactory->create();
        $this->resource->load($campaign, $campid);
        if (!$campaign->getId()) {
            throw new NoSuchEntityException(__('The Campaigns block with the "%1" ID doesn\'t exist.', $campid));
        }
        return $campaign;
    }

    /**
     * Load Campaigns data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Theiconnz\Campaigns\Api\Data\CampaignSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Theiconnz\Campaigns\Model\ResourceModel\Campaign\Collection $collection */
        $collection = $this->campaignCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var Data\CampaignSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Campaigns
     *
     * @param \Theiconnz\Campaigns\Api\Data\CampaignInterface $campaign
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\CampaignInterface $campaign)
    {
        try {
            $this->resource->delete($campaign);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Campaigns by given Campaigns Identity
     *
     * @param string $campid
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($campid)
    {
        return $this->delete($this->getById($campid));
    }

    /**
     * Checks that page identifier doesn't duplicate existed routes
     *
     * @param PageInterface $page
     * @return void
     * @throws CouldNotSaveException
     */
    private function validateRoutesDuplication($page): void
    {
        if ($this->routeConfig->getRouteByFrontName($page->getIdentifier(), 'frontend')) {
            throw new CouldNotSaveException(
                __('The value specified in the URL Key field would generate a URL that already exists.')
            );
        }
    }
}
