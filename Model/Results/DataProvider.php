<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Model\Results;

use Theiconnz\Campaigns\Api\Data\ResultsInterface;
use Theiconnz\Campaigns\Api\ResultsRepositoryInterface;
use Theiconnz\Campaigns\Model\resultsFactory;
use Theiconnz\Campaigns\Model\ResourceModel\Results\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use Psr\Log\LoggerInterface;

/**
 * Cms Page DataProvider
 */
class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var ResultsRepositoryInterface
     */
    private $resultsRepository;

    /**
     * @var AuthorizationInterface
     */
    private $auth;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResultsFactory
     */
    private $resultsFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $campaignCollectionRepository
     * @param DataPersistorInterface $dataPersistor
     * @param PoolInterface|null $pool
     * @param RequestInterface|null $request
     * @param ResultsRepositoryInterface $resultsRepository
     * @param ResultsFactory $resultsFactory
     * @param LoggerInterface|null $logger
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $campaignCollectionRepository,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        RequestInterface $request,
        ResultsRepositoryInterface $resultsRepository,
        ResultsFactory $resultsFactory,
        LoggerInterface $logger,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->collection = $campaignCollectionRepository->create();
        $this->dataPersistor = $dataPersistor;
        $this->meta = $this->prepareMeta($this->meta);
        $this->request = $request;
        $this->resultsRepository = $resultsRepository;
        $this->resultsFactory = $resultsFactory;
        $this->logger = $logger;
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $page = $this->getCurrentPage();
        $this->loadedData[$page->getId()] = $page->getData();
        return $this->loadedData;
    }

    /**
     * Return current page
     *
     * @return ResultsInterface
     */
    private function getCurrentPage(): ResultsInterface
    {
        $pageId = $this->getPageId();
        if ($pageId) {
            try {
                $page = $this->resultsRepository->getById($pageId);
            } catch (LocalizedException $exception) {
                $page = $this->resultsFactory->create();
            }

            return $page;
        }

        $data = $this->dataPersistor->get('result');
        if (empty($data)) {
            return $this->resultsFactory->create();
        }
        $this->dataPersistor->clear('result');

        return $this->resultsFactory->create()
            ->setData($data);
    }

    /**
     * Returns current page id from request
     *
     * @return int
     */
    private function getPageId(): int
    {
        if ($this->request) {
            return (int)$this->request->getParam($this->getRequestFieldName());
        } else {
            return false;
        }
    }
}
