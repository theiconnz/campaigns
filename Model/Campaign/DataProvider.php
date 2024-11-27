<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Model\Campaign;

use Theiconnz\Campaigns\Api\Data\CampaignInterface;
use Theiconnz\Campaigns\Api\CampaignRepositoryInterface;
use Theiconnz\Campaigns\Model\CampaignFactory;
use Theiconnz\Campaigns\Model\ResourceModel\Campaign\CollectionFactory;
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
     * @var CampaigneRepositoryInterface
     */
    private $campaignRepository;

    /**
     * @var AuthorizationInterface
     */
    private $auth;

    /**
     * @var RequestInterface
     */
    private $request;


    /**
     * @var CampaignFactory
     */
    private $campaignFactory;

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
     * @param AuthorizationInterface|null $auth
     * @param RequestInterface|null $request
     * @param CampaignRepositoryInterface $campaignRepository
     * @param CampaignFactory|null $campaignFactory
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
        CampaignRepositoryInterface $campaignRepository,
        CampaignFactory $campaignFactory,
        LoggerInterface $logger,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->collection = $campaignCollectionRepository->create();
        $this->dataPersistor = $dataPersistor;
        $this->meta = $this->prepareMeta($this->meta);
        $this->request = $request;
        $this->campaignRepository = $campaignRepository;
        $this->campaignFactory = $campaignFactory;
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
        if ($page->getCustomLayoutUpdateXml() || $page->getLayoutUpdateXml()) {
            //Deprecated layout update exists.
            $this->loadedData[$page->getId()]['layout_update_selected'] = '_existing_';
        }

        return $this->loadedData;
    }

    /**
     * Return current page
     *
     * @return CampaignInterface
     */
    private function getCurrentPage(): CampaignInterface
    {
        $pageId = $this->getPageId();
        if ($pageId) {
            try {
                $page = $this->campaignRepository->getById($pageId);
            } catch (LocalizedException $exception) {
                $page = $this->campaignFactory->create();
            }

            return $page;
        }

        $data = $this->dataPersistor->get('campaign');
        if (empty($data)) {
            return $this->campaignFactory->create();
        }
        $this->dataPersistor->clear('campaign');

        return $this->campaignFactory->create()
            ->setData($data);
    }

    /**
     * Returns current page id from request
     *
     * @return int
     */
    private function getPageId(): int
    {
        if($this->request) {
            return (int)$this->request->getParam($this->getRequestFieldName());
        } else {
            return false;
        }
    }

}
