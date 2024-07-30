<?php

namespace Theiconnz\Campaigns\Test\Unit\Controller\Campaign;

use Magento\Captcha\Observer\CaptchaStringResolver;
use Magento\Contact\Model\MailInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Newsletter\Model\SubscriptionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Theiconnz\Campaigns\Api\CampaignRepositoryInterface;
use Theiconnz\Campaigns\Api\ResultsRepositoryInterface;
use Theiconnz\Campaigns\Controller\Campaign\Post;
use Theiconnz\Campaigns\Helper\Campaign;
use Theiconnz\Campaigns\Model\ResourceModel\Results\CollectionFactory;
use Theiconnz\Campaigns\Model\ResultsFactory;

/**
 * Post test class for form post in front end
 */
class PostTest extends TestCase
{
    /**
     * @var MockObject|(Context&MockObject)
     */
    private MockObject $context;
    /**
     * @var MockObject|(Context&MockObject)
     */
    private MockObject $requestInterface;
    /**
     * @var MockObject|(Campaign&MockObject)
     */
    private MockObject $campaignHelper;
    /**
     * @var MockObject|(UploaderFactory&MockObject)
     */
    private MockObject $uploaderFactory;
    /**
     * @var MockObject|(AdapterFactory&MockObject)
     */
    private MockObject $adapterFactory;
    /**
     * @var MockObject|(Filesystem&MockObject)
     */
    private MockObject $filesystem;
    /**
     * @var MockObject|(JsonFactory&MockObject)
     */
    private MockObject $resultJsonFactory;
    /**
     * @var MockObject|(Filesystem\Io\File&MockObject)
     */
    private MockObject $file;
    /**
     * @var MockObject|(Filesystem\DirectoryList&MockObject)
     */
    private MockObject $dir;
    /**
     * @var MockObject|CampaignRepositoryInterface|(CampaignRepositoryInterface&MockObject)
     */
    private MockObject $campaignRepository;
    /**
     * @var MockObject|(ResultsFactory&MockObject)
     */
    private MockObject $resultsFactory;
    /**
     * @var MockObject|ResultsRepositoryInterface|(ResultsRepositoryInterface&MockObject)
     */
    private MockObject $resultRepository;
    /**
     * @var MockObject|TimezoneInterface|(TimezoneInterface&MockObject)
     */
    private MockObject $timezone;
    /**
     * @var MockObject|StoreManagerInterface|(StoreManagerInterface&MockObject)
     */
    private MockObject $storeManager;
    /**
     * @var MockObject|(SubscriberFactory&MockObject)
     */
    private MockObject $subscriberFactory;
    /**
     * @var MockObject|SubscriptionManagerInterface|(SubscriptionManagerInterface&MockObject)
     */
    private MockObject $subscriptionManager;
    /**
     * @var MockObject|(Session&MockObject)
     */
    private MockObject $customerSession;
    /**
     * @var MockObject|(CollectionFactory&MockObject)
     */
    private MockObject $resultsCollectionFactory;
    /**
     * @var MockObject|MailInterface|(MailInterface&MockObject)
     */
    private MockObject $mail;
    /**
     * @var MockObject|(Campaign&MockObject)
     */
    private MockObject $helper;
    /**
     * @var MockObject|(CaptchaStringResolver&MockObject)
     */
    private MockObject $captchaStringResolver;

    /**
     * @var $postModel
     */
    private $postModel;

    /**
     * Start up initialization
     *
     * @return void
     */
    protected function setUp() :  void
    {
        $this->context=$this->getMockBuilder(
            Context::class
        )->disableOriginalConstructor()->getMock();

        $this->requestInterface=$this->getMockForAbstractClass(
            RequestInterface::class
        );

        $this->campaignHelper=$this->getMockBuilder(
            Campaign::class
        )->disableOriginalConstructor()->getMock();

        $this->uploaderFactory=$this->getMockBuilder(
            UploaderFactory::class
        )->disableOriginalConstructor()->getMock();

        $this->adapterFactory=$this->getMockBuilder(
            AdapterFactory::class
        )->disableOriginalConstructor()->getMock();

        $this->filesystem=$this->getMockBuilder(
            Filesystem::class
        )->disableOriginalConstructor()->getMock();

        $this->resultJsonFactory=$this->getMockBuilder(
            JsonFactory::class
        )->disableOriginalConstructor()->getMock();

        $this->file=$this->getMockBuilder(
            Filesystem\Io\File::class
        )->disableOriginalConstructor()->getMock();
        $this->dir=$this->getMockBuilder(
            Filesystem\DirectoryList::class
        )->disableOriginalConstructor()->getMock();
        $this->campaignRepository=$this->getMockForAbstractClass(
            CampaignRepositoryInterface::class,
        );
        $this->resultsFactory=$this->getMockBuilder(
            ResultsFactory::class,
        )->disableOriginalConstructor()->getMock();

        $this->resultRepository=$this->getMockForAbstractClass(
            ResultsRepositoryInterface::class
        );
        $this->timezone=$this->getMockForAbstractClass(
            TimezoneInterface::class,
        );
        $this->storeManager=$this->getMockForAbstractClass(
            StoreManagerInterface::class,
        );
        $this->subscriberFactory=$this->getMockBuilder(
            SubscriberFactory::class
        )->disableOriginalConstructor()->getMock();

        $this->subscriptionManager=$this->getMockForAbstractClass(
            SubscriptionManagerInterface::class
        );
        $this->customerSession=$this->getMockBuilder(
            Session::class
        )->disableOriginalConstructor()->getMock();
        $this->resultsCollectionFactory=$this->getMockBuilder(
            CollectionFactory::class
        )->disableOriginalConstructor()->getMock();
        $this->mail=$this->getMockForAbstractClass(
            MailInterface::class
        );
        $this->helper=$this->getMockBuilder(
            Campaign::class
        )->disableOriginalConstructor()->getMock();
        $this->captchaStringResolver=$this->getMockBuilder(
            CaptchaStringResolver::class
        )->disableOriginalConstructor()->getMock();
    }

    /**
     * @return void
     */
    public function testArrrayCheck()
    {
        $this->postModel=new Post(
            $this->context,
            $this->requestInterface,
            $this->campaignHelper,
            $this->uploaderFactory,
            $this->adapterFactory,
            $this->filesystem,
            $this->resultJsonFactory,
            $this->file,
            $this->dir,
            $this->campaignRepository,
            $this->resultsFactory,
            $this->resultRepository,
            $this->timezone,
            $this->storeManager,
            $this->subscriberFactory,
            $this->subscriptionManager,
            $this->customerSession,
            $this->resultsCollectionFactory,
            $this->mail,
            $this->campaignHelper,
            $this->captchaStringResolver
        );

        $this->assertTrue(1);
    }
}
