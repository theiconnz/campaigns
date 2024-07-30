<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Theiconnz\Campaigns\Controller\Campaign;

use Magento\Captcha\Observer\CaptchaStringResolver;
use Magento\Contact\Model\MailInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\File\Uploader;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Newsletter\Model\SubscriptionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Theiconnz\Campaigns\Api\CampaignRepositoryInterface;
use Theiconnz\Campaigns\Helper\Campaign;
use Theiconnz\Campaigns\Helper\Campaign as CampaignHelper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultsInterface;
use Theiconnz\Campaigns\Model\Results;
use Theiconnz\Campaigns\Model\ResultsFactory;
use Theiconnz\Campaigns\Api\ResultsRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Theiconnz\Campaigns\Model\ResourceModel\Results\CollectionFactory;

/**
 * Custom page for storefront. Needs to be accessible by POST because of the store switching.
 */
class Post extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CampaignHelper
     */
    private $campaignHelper;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var AdapterFactory
     */
    private $adapterFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var File
     */
    private $io;

    /**
     * @var DirectoryList
     */
    private $dir;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * @var ResultsFactory
     */
    private $resultsFactory;

    /**
     * @var ResultsRepositoryInterface
     */
    private $resultsRepository;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var MailInterface
     */
    private $mail;

    /**
     * @var SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @var SubscriptionManagerInterface
     */
    private $subscriptionManager;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var \Theiconnz\Campaigns\Model\ResourceModel\Results\CollectionFactory
     */
    protected $_resultsCollectionFactory;

    /**
     * @var \Theiconnz\Campaigns\Helper\Campaign
     */
    protected $_helper;

    /**
     * @var CaptchaStringResolver
     */
    protected $captchaStringResolver;

    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param CampaignHelper $campaignHelper
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Image\AdapterFactory $adapterFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param CampaignRepositoryInterface $campaignRepository
     * @param ResultsFactory $resultsFactory
     * @param ResultsRepositoryInterface $resultRepository
     * @param TimezoneInterface $timezone
     * @param StoreManagerInterface $storeManager
     * @param SubscriberFactory $subscriberFactory
     * @param SubscriptionManagerInterface $subscriptionManager
     * @param Session $customerSession
     * @param CollectionFactory $resultsCollectionFactory
     * @param MailInterface $mail
     * @param Campaign $helper
     * @param CaptchaStringResolver $captchaStringResolver
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        CampaignHelper $campaignHelper,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        CampaignRepositoryInterface $campaignRepository,
        ResultsFactory $resultsFactory,
        ResultsRepositoryInterface $resultRepository,
        TimezoneInterface $timezone,
        StoreManagerInterface $storeManager,
        SubscriberFactory $subscriberFactory,
        SubscriptionManagerInterface $subscriptionManager,
        Session $customerSession,
        CollectionFactory $resultsCollectionFactory,
        MailInterface $mail,
        Campaign $helper,
        CaptchaStringResolver $captchaStringResolver
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->campaignHelper = $campaignHelper;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->io = $file;
        $this->dir = $dir;
        $this->campaignRepository = $campaignRepository;
        $this->resultsFactory = $resultsFactory;
        $this->resultsRepository = $resultRepository;
        $this->timezone = $timezone;
        $this->storeManager = $storeManager;
        $this->mail = $mail;
        $this->subscriptionManager = $subscriptionManager;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_customerSession = $customerSession;
        $this->_resultsCollectionFactory = $resultsCollectionFactory;
        $this->_helper = $helper;
        $this->captchaStringResolver = $captchaStringResolver;
    }

    /**
     * Campaigns id must be active
     *
     * @return ResultsInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('cid');
        $terms = $this->getRequest()->getParam('terms');

        $resultPage = $this->resultJsonFactory->create();
        $resultPage->setHttpResponseCode(500);
        $resultPage->setData([]);

        try {
            $this->checkCaptcha();
            $result = false;
            if (empty($id) || !is_numeric($id) || !$terms) {
                throw new LocalizedException(__('Incorrect data values'));
            }

            $this->validatedParams();

            $model = $this->campaignRepository->getById((integer)$id);

            if (!$model || ($model->getId()!=$id) && !$model->isActive()) {
                throw new LocalizedException(
                    __('Not an active campaign')
                );
            }

            $file = $this->getRequest()->getFiles('filename');
            $fileName = ($file && array_key_exists('name', $file)) ? $file['name'] : null;

            if ($file && $fileName) {
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'filename']);

                $uploaddir = $this->dir->getPath('media') . Results::UPLOADPATH;
                if (!$this->io->fileExists($uploaddir)) {
                    $this->io->mkdir($uploaddir);
                }

                $uploaderFactory->setAllowedExtensions(['jpg', 'gif', 'png']); // you can add more extension which need
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);

                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath(Results::UPLOADPATH);

                $ext = $uploaderFactory->getFileExtension();
                $newfile = $this->renameFile($this->validateInputFields($this->request->getParam('email')));
                $newfilename = sprintf("%s.%s", $newfile, $ext);
                $result = $uploaderFactory->save($destinationPath, $newfilename);

                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
            }

            $resultFactory = $this->resultsFactory->create();
            $resultFactory->setCampId($model->getId());
            $resultFactory->setFirstname($this->validateInputFields($this->request->getParam('firstname')));
            $resultFactory->setContent($this->validateInputFields($this->request->getParam('content')));
            $resultFactory->setEmail($this->request->getParam('email'));
            if ($result) {
                $resultFactory->setImagename($result['file']);
            }

            $nl = $this->request->getParam('newsletter');
            if ($nl==1) {
                $resultFactory->setNewsletter(1);
            }
            $terms = $this->request->getParam('terms');
            if ($terms==1) {
                $resultFactory->setTermsAgreed(1);
            }
            $um = $this->request->getParam('useinmarketing');
            if ($um==1) {
                $resultFactory->setUsedataAgreed(1);
            }

            $resultFactory->setStoreId($this->storeManager->getStore()->getId());
            $this->resultsRepository->save($resultFactory);
            $resultPage->setHttpResponseCode(200);

            $response = ['success' => 200, 'message' => "Result submit success"];
            $resultPage->setData($response);

            if ($nl && $nl==1) {
                $this->addtoSubscription($this->request->getParam('email'));
            }

            $this->messageManager->addSuccessMessage("Thank you");
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultPage->setHttpResponseCode(500);
            $resultPage->setJsonData(
                json_encode([
                    'error'   => 400,
                    'message' => $e->getMessage(),
                ])
            );
            return $resultPage;
        }
        return $resultPage;
    }

    /**
     * New file name. prefix, noempty spage, lowercase
     *
     * @param String $email
     * @return string
     */
    public function renameFile(string $email) : string
    {
        return sprintf("%s_%s", $email, $this->timezone->scopeTimeStamp());
    }

    /**
     * Validate and strip html tags
     *
     * @param String $field
     * @return string
     */
    public function validateInputFields(string $field): string
    {
        return trim(strip_tags($field));
    }

    /**
     * Validate and strip html tags
     *
     * @return array
     * @throws LocalizedException
     */
    private function validatedParams()
    {
        $request = $this->getRequest();
        if (trim($request->getParam('firstname')) === '') {
            throw new LocalizedException(__('First Name is missing'));
        }

        if (trim($request->getParam('email')) === '') {
            throw new LocalizedException(__('Email is missing'));
        }

        if ($request->getParam('terms') == '' ||
            $request->getParam('terms') == 0) {
            throw new LocalizedException(__('Incorrect terms and conditions agreement'));
        }

        if (!str_contains($request->getParam('email'), '@')) {
            throw new LocalizedException(__('Invalid email address'));
        }

        return $request->getParams();
    }

    /**
     * Send email method
     *
     * @param array $post Post data from campaign form
     * @return void
     */
    private function sendEmail($post)
    {
        $this->mail->send(
            $post['email'],
            ['data' => new DataObject($post)]
        );
    }

    /**
     * Add to subscription by email
     *
     * @param string $email
     * @return void
     * @throws NoSuchEntityException
     */
    private function addtoSubscription($email)
    {
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        $subscriber = $this->_subscriberFactory->create()->loadBySubscriberEmail($email, $websiteId);
        if ($subscriber->getId()
            && (int)$subscriber->getSubscriberStatus() === Subscriber::STATUS_SUBSCRIBED) {
            return true;
        }

        $storeId = (int)$this->storeManager->getStore()->getId();
        $currentCustomerId = $this->getSessionCustomerId($email);
        $subscriber = $currentCustomerId
            ? $this->subscriptionManager->subscribeCustomer($currentCustomerId, $storeId)
            : $this->subscriptionManager->subscribe($email, $storeId);
    }

    /**
     * Get customer id from session if he is owner of the email
     *
     * @param string $email
     * @return int|null
     */
    private function getSessionCustomerId(string $email): ?int
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return null;
        }

        $customer = $this->_customerSession->getCustomerDataObject();
        if ($customer->getEmail() !== $email) {
            return null;
        }

        return (int)$this->_customerSession->getId();
    }

    /**
     * Returns results collection
     *
     * @param String $email
     * @param String $cid
     * @return object
     */
    public function getResultsCollection(string $email, string $cid) : object
    {
        $collection = $this->_resultsCollectionFactory->create()
            ->addFieldToSelect('email')
            ->addFieldToFilter('email', ['eq' => $email])
            ->addFieldToFilter('camp_id', ['eq' => $cid]);
        return $collection;
    }

    /**
     * Check captcha method
     *
     * @return void
     * @throws LocalizedException
     */
    private function checkCaptcha()
    {
        $formId = 'campaign_form';
        $enabled = $this->_helper->getConfig('campaigns/recaptcha/enabled');
        if ($enabled) {
            $captcha = $this->_helper->getCaptcha($formId);
            if (!$captcha->isCorrect($this->captchaStringResolver->resolve($this->getRequest(), $formId))) {
                throw new LocalizedException(__('Incorrect captcha'));
            }
        }
    }
}
