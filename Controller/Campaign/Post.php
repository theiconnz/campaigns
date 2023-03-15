<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Theiconnz\Campaigns\Controller\Campaign;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
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

    private $storeManager;


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
        StoreManagerInterface $storeManager
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
            $result = false;
            if (
                ( null == $id ) || empty($id) || !is_numeric($id) || !$terms
             ) {
                throw new LocalizedException(
                    __('Incorrect data values')
                );
            }

            $this->validatedParams();

            try {
                $model = $this->campaignRepository->getById( (integer)$id );

                if(!$model || ($model->getId()!=$id) && !$model->isActive()) {
                    throw new LocalizedException(
                        __('Not an active campaign')
                    );
                }

                if(count($_FILES['filename'])>0 && !$_FILES['filename']['error'] ) {
                    $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'filename']);

                    $uploaddir = $this->dir->getPath('media') . Results::UPLOADPATH;
                    if (!file_exists($uploaddir)) {
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

                $resultfactory = $this->resultsFactory->create();
                $resultfactory->setCampId($model->getId());
                $resultfactory->setName( $this->validateInputFields($this->request->getParam('name')) );
                $resultfactory->setContent( $this->validateInputFields($this->request->getParam('content')) );
                $resultfactory->setEmail( $this->request->getParam('email') );
                if($result) {
                    $resultfactory->setImagename($result['file'] );
                }

                $nl = $this->request->getParam('newsletter');
                if($nl) $resultfactory->setNewsletter(1);
                $terms = $this->request->getParam('terms');
                if($terms) $resultfactory->setNewsletter(1);
                $um = $this->request->getParam('useinmarketing');
                if($um) $resultfactory->setUsedataAgreed(1);

                $resultfactory->setStoreId( $this->storeManager->getStore()->getId() );
                $this->resultsRepository->save($resultfactory);
                $resultPage->setHttpResponseCode(200);

                $response = ['success' => 200, 'message' => "Result submit success"];
                $resultPage->setData($response);
                $this->messageManager->addSuccessMessage("Thank you");

            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage( $e->getMessage() );
            }

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
    private function validatedParams()
    {
        $request = $this->getRequest();
        if (trim($request->getParam('name')) === '') {
            throw new LocalizedException(__('First Name is missing'));
        }

        if (trim($request->getParam('content')) === '') {
            throw new LocalizedException(__('Content is missing'));
        }

        if (trim($request->getParam('email')) === '') {
            throw new LocalizedException(__('CEmail is missing'));
        }

        if (false === \strpos($request->getParam('email'), '@')) {
            throw new LocalizedException(__('Invalid email address'));
        }

        return $request->getParams();
    }

}
