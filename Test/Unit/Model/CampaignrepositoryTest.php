<?php

namespace Theiconnz\Campaigns\Test\Unit\Model;

use Magento\Framework\App\Action\Context;
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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CampaignrepositoryTest extends TestCase
{
    /**
     * @var MockObject|(Context&MockObject)
     */
    protected $resource;

    /**
     * @var MockObject
     */
    protected $campaignFactory;

    /**
     * @var MockObject
     */
    protected $campaignCollectionFactory;

    /**
     * @var MockObject
     */
    protected $searchResultsFactory;

    /**
     * @var MockObject
     */
    protected $dataObjectHelper;

    /**
     * @var MockObject
     */
    protected $dataObjectProcessor;

    /**
     * @var MockObject
     */
    protected $dataCampaignFactory;

    /**
     * @var MockObject
     */
    private $storeManager;

    /**
     * @var MockObject
     */
    private $collectionProcessor;

    /**
     * @var MockObject
     */
    private $hydrator;

    /**
     * @var MockObject
     */
    private $routeConfig;



    /**
     * Start up initialization
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->resource=new ResourceCampaign();
        $this->campaignFactory=$this->getMockBuilder(
            CampaignFactory::class
        )->disableOriginalConstructor()->getMock();
        $this->campaigninterface=$this->getMockForAbstractClass(
            Data\CampaignInterface::class
        );
    }

    /**
     * @return void
     */
    public function testGetById()
    {
        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testSave()
    {
        $this->campaigninterface->setContent('');

        $this->assertTrue(true);
    }

}
