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
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Newsletter\Model\SubscriptionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use tests\verification\Tests\DataActionsTest;
use Theiconnz\Campaigns\Api\CampaignRepositoryInterface;
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
use Theiconnz\Campaigns\Helper\Campaign as Helper;
/**
 * Custom page for storefront. Needs to be accessible by POST because of the store switching.
 */
class Post extends Action implements CsrfAwareActionInterface, HttpGetActionInterface, HttpPostActionInterface
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

    private $storeManager;

    /**
     * @var MailInterface
     */
    private $mail;

    /**
     * Subscriber factory
     *
     * @var SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @var SubscriptionManagerInterface
     */
    private $subscriptionManager;

    /**
     * Customer session
     *
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
     * @param ForwardFactory $resultForwardFactory
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
     * @param CollectionFactory $resultsCollectionFactory
     * @param MailInterface $mail
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
        \Theiconnz\Campaigns\Helper\Campaign $helper,
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
     * Campaigns Id must be active
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

        try{
            $this->checkCaptcha();

            $result = false;
            $file2result = false;
            if (
                ( null == $id ) || empty($id) || !is_numeric($id) || !$terms
             ) {
                throw new LocalizedException(
                    __('Incorrect data values')
                );
            }

            $model = $this->campaignRepository->getById( (integer)$id );
            if(!$model->getFormEnable()){
                throw new LocalizedException(
                    __('Campaign submission disabled')
                );
            }

            $this->validatedParams($model);


            if(!$model || ($model->getId()!=$id) && !$model->isActive()) {
                throw new LocalizedException(
                    __('Not an active campaign')
                );
            }

            if( $model->getShowemail() && $model->getOneEntry()){
                $collection=$this->getResultsCollection($this->getRequest()->getParam('email'), $id);
                if($collection->count()>0){
                    throw new LocalizedException(
                        __('This email is already registered')
                    );
                }
            }

            if( isset($_FILES['filename']) && count($_FILES['filename'])>0 && !$_FILES['filename']['error'] && $model->getShowupload() ) {
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'filename']);

                $uploaddir = $this->dir->getPath('media') . Results::UPLOADPATH;
                if (!file_exists($uploaddir)) {
                    $this->io->mkdir($uploaddir);
                }

                $uploaderFactory->setAllowedExtensions(['jpg','jpeg', 'gif', 'png']); // you can add more extension which need
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);


                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath(Results::UPLOADPATH);

                $ext = $uploaderFactory->getFileExtension();
                if($model->getShowemail()) {
                    $newfile = $this->renameFile($this->validateInputFields($this->request->getParam('email')));
                } else {
                    $timeValue = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
                    $newfile = md5($timeValue);
                }
                $newfilename = sprintf("%s.%s", $newfile, $ext);
                $result = $uploaderFactory->save($destinationPath, $newfilename);

                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
            }

            if( isset($_FILES['filename_2']) && count($_FILES['filename_2'])>0 && !$_FILES['filename_2']['error'] && $model->getShowupload2() ) {
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'filename_2']);

                $uploaddir = $this->dir->getPath('media') . Results::UPLOADPATH;
                if (!file_exists($uploaddir)) {
                    $this->io->mkdir($uploaddir);
                }

                $uploaderFactory->setAllowedExtensions(['jpg','jpeg', 'gif', 'png']); // you can add more extension which need
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);


                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath(Results::UPLOADPATH);

                $ext = $uploaderFactory->getFileExtension();
                if($model->getShowemail()) {
                    $newfile = $this->renameFile($this->validateInputFields($this->request->getParam('email'))."-2");
                } else {
                    $timeValue = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
                    $newfile = md5($timeValue);
                }
                $newfilename = sprintf("%s.%s", $newfile, $ext);
                $file2result = $uploaderFactory->save($destinationPath, $newfilename);

                if (!$file2result) {
                    throw new LocalizedException(
                        __('File 2 cannot be saved to path: $1', $destinationPath)
                    );
                }
            }

            $resultfactory = $this->resultsFactory->create();
            $resultfactory->setCampId($model->getId());
            if($model->getShowname()) {
                $resultfactory->setFirstname($this->validateInputFields($this->request->getParam('firstname')));
            }
            if($model->getShowLastname()) {
                $resultfactory->setLastname($this->validateInputFields($this->request->getParam('lastname')));
            }
            if($model->getShowcontent()) {
                $resultfactory->setContent($this->validateInputFields($this->request->getParam('content')));
            }
            if($model->getShowphone()) {
                $resultfactory->setPhone($this->validateInputFields($this->request->getParam('phone')));
            }
            if($model->getShowemail()) {
                $resultfactory->setEmail($this->request->getParam('email'));
            }
            if($result && $model->getShowupload()) {
                $resultfactory->setImagename($result['file'] );
            }
            if($file2result && $model->getShowUpload2()) {
                $resultfactory->setImage2name($file2result['file'] );
            }
            if($model->getValidationfield()) {
                $validationField=$this->validateInputFields($this->request->getParam('validationfield'));
                $this->ValidateValidationField($validationField);

                $resultfactory->setValidationfield($validationField);
            }

            if($model->getNewsletter()) {
                $nl = $this->request->getParam('newsletter');
                if ($nl && $nl == 1) $resultfactory->setNewsletter(1);
            }

            $terms = $this->request->getParam('terms');
            if($terms && $terms==1) $resultfactory->setTermsAgreed(1);

            if($model->getShowmarketing()) {
                $um = $this->request->getParam('useinmarketing');
                if ($um && $um == 1) $resultfactory->setUsedataAgreed(1);
            }

            $this->_eventManager->dispatch(
                'campaign_result_post',
                ['account_controller' => $this, 'model' => $model, 'params' => $this->getRequest()->getParams()]
            );

            $resultfactory->setStoreId( $this->storeManager->getStore()->getId() );
            $this->resultsRepository->save($resultfactory);
            $resultPage->setHttpResponseCode(200);

            $response = ['success' => 200, 'message' => "Result submit success"];
            $resultPage->setData($response);

            if( $model->getNewsletter() && $nl && $nl==1 ) {
                $this->addtoSubscription($this->request->getParam('email'));
            }

            $this->messageManager->addSuccessMessage("Thank you");


        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage( $e->getMessage() );
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
     * @param String $context
     *
     * @return string
     */
    public function renameFile($email){
        return sprintf("%s_%s",  $email,  $this->timezone->scopeTimeStamp() );
    }

    /**
     * Validate and strip html tags
     * @param String $context
     *
     * @return string
     */
    public function validateInputFields($field){
        return trim(strip_tags($field));
    }

    /**
     * Validate and strip html tags
     * @param String $context
     *
     * @return string
     */
    private function validatedParams($model)
    {
        $request = $this->getRequest();

        if($model->getShowname()) {
            if (trim($request->getParam('firstname')) === '') {
                throw new LocalizedException(__('First Name is missing'));
            }
        }

        if($model->getShowemail()) {
            if (trim($request->getParam('email')) === '') {
                throw new LocalizedException(__('Email is missing'));
            }

            if (false === \strpos($request->getParam('email'), '@')) {
                throw new LocalizedException(__('Invalid email address'));
            }
        }

        if ($request->getParam('terms') == '' ||
            $request->getParam('terms') == 0 ) {
            throw new LocalizedException(__('Incorrect terms and conditions agreement'));
        }

        return $request->getParams();
    }


    /**
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
     * @param array $post Post data from campaign form
     * @return void
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
     * @return string
     */
    public function getResultsCollection($email, $cid)
    {
        $collection = $this->_resultsCollectionFactory->create()
            ->addFieldToSelect('email')
            ->addFieldToFilter('email',
                ['eq' => $email]
            )
            ->addFieldToFilter('camp_id',
                ['eq' => $cid]
            );
        return $collection;
    }


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


    private function ValidateValidationField($fieldvalue)
    {
        $collection = $this->_resultsCollectionFactory->create()
            ->addFieldToSelect('validationfield')
            ->addFieldToFilter('validationfield',
                ['eq' => $fieldvalue]
            );
        if($collection->count()>0){
            throw new LocalizedException(
                __('This invoice is already submitted to our system.')
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        $resultPage = $this->resultJsonFactory->create();
        $message=new Phrase('Invalid Form Key. Please refresh the page.');
        $this->messageManager->addErrorMessage( $message );
        $resultPage->setHttpResponseCode(500);
        $resultPage->setJsonData(
            json_encode([
                'error'   => 500,
                'message' => $message,
            ])
        );
        return $resultPage;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return null;
    }
}
