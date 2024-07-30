<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Theiconnz\Campaigns\Model;

use Theiconnz\Campaigns\Api\ResultsRepositoryInterface;
use Theiconnz\Campaigns\Api\Data;
use Theiconnz\Campaigns\Model\ResourceModel\Results as ResourceBlock;
use Theiconnz\Campaigns\Model\ResourceModel\Results\CollectionFactory as ResultsCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
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
class ResultsRepository implements ResultsRepositoryInterface
{
    /**
     * @var ResourceBlock
     */
    protected $resource;

    /**
     * @var ResultsFactory
     */
    protected $resultsFactory;

    /**
     * @var ResultsCollectionFactory
     */
    protected $resultsCollectionFactory;

    /**
     * @var Data\BlockSearchResultsInterfaceFactory
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
     * @param ResourceBlock $resource
     * @param ResultsFactory $resultsFactory
     * @param ResultsCollectionFactory $resultsCollectionFactory
     * @param Data\BlockSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param HydratorInterface $hydrator
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceBlock $resource,
        ResultsFactory $resultsFactory,
        ResultsCollectionFactory $resultsCollectionFactory,
        Data\ResultsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor = null,
        ?HydratorInterface $hydrator = null
    ) {
        $this->resource = $resource;
        $this->resultsFactory = $resultsFactory;
        $this->resultsCollectionFactory = $resultsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->hydrator = $hydrator;
    }

    /**
     * Save Block data
     *
     * @param \Theiconnz\Campaigns\Api\Data\ResultsInterface $results
     * @return Block
     * @throws CouldNotSaveException
     */
    public function save(Data\ResultsInterface $results)
    {
        if (empty($results->getStoreId())) {
            $results->setStoreId($this->storeManager->getStore()->getId());
        }

        if ($results->getId() && $results instanceof Block && !$results->getOrigData()) {
            $results = $this->hydrator->hydrate($this->getById($results->getId()), $this->hydrator->extract($results));
        }

        try {
            $this->resource->save($results);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $results;
    }

    /**
     * Load result by given id
     *
     * @param string $campid
     * @return Block
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($campid)
    {
        $results = $this->resultsFactory->create();
        $this->resource->load($results, $campid);
        if (!$results->getId()) {
            throw new NoSuchEntityException(__('The Result with the "%1" ID doesn\'t exist.', $campid));
        }
        return $results;
    }

    /**
     * Load Result data by given Email
     *
     * @param string $email
     * @return Block
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByEmail($email)
    {
        $results = $this->resultsFactory->create();
        $this->resource->load($results, $email, 'email');
        if (!$results->getId()) {
            return false;
        }
        return $results;
    }

    /**
     * Load Block data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Theiconnz\Campaigns\Api\Data\BlockSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Theiconnz\Campaigns\Model\ResourceModel\Results\Collection $collection */
        $collection = $this->resultsCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var Data\BlockSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Block
     *
     * @param \Theiconnz\Campaigns\Api\Data\ResultsInterface $results
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\ResultsInterface $results)
    {
        try {
            $this->resource->delete($results);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Block by given Block Identity
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
}
