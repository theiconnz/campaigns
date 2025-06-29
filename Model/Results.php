<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Validation\ValidationException;
use Magento\Framework\Validator\HTML\WYSIWYGValidatorInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Backend\Model\Validator\UrlKey\CompositeUrlKey;
use Magento\Framework\Exception\LocalizedException;
use Theiconnz\Campaigns\Api\Data\ResultsInterface;

/**
 * Results model
 *
 */
class Results extends AbstractModel implements ResultsInterface, IdentityInterface
{
    /**
     * CMS block cache tag
     */
    const CACHE_TAG = 'c_results';

    const RESULTS_FOLDER = '';

    /**
     * New file name  prefix
     */
    const PREFIX = 'result_';

    const UPLOADPATH = '/campaign/u';


    /**#@-*/

    /**#@-*/
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'c_results_';

    /**
     * @var WYSIWYGValidatorInterface
     */
    private $wysiwygValidator;

    /**
     * @var CompositeUrlKey
     */
    private $compositeUrlValidator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param WYSIWYGValidatorInterface|null $wysiwygValidator
     * @param CompositeUrlKey|null $compositeUrlValidator
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        ?WYSIWYGValidatorInterface $wysiwygValidator = null,
        CompositeUrlKey $compositeUrlValidator = null
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->wysiwygValidator = $wysiwygValidator
            ?? ObjectManager::getInstance()->get(WYSIWYGValidatorInterface::class);
        $this->compositeUrlValidator = $compositeUrlValidator
            ?? ObjectManager::getInstance()->get(CompositeUrlKey::class);
    }

    /**
     * Construct.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Theiconnz\Campaigns\Model\ResourceModel\Results::class);
    }

    /**
     * Prevent blocks recursion
     *
     * @return AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        if ($this->hasDataChanges()) {
            $this->setUpdateTime(null);
        }

        $needle = 'r_id="' . $this->getId() . '"';
        if (strstr($this->getContent(), (string) $needle) !== false) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Make sure that static result content does not reference the block itself.')
            );
        }

        $errors = $this->compositeUrlValidator->validate($this->getIdentifier());
        if (!empty($errors)) {
            throw new LocalizedException($errors[0]);
        }

        parent::beforeSave();

        //Validating HTML content.
        if ($this->getContent() && $this->getContent() !== $this->getOrigData(self::CONTENT)) {
            try {
                $this->wysiwygValidator->validate($this->getContent());
            } catch (ValidationException $exception) {
                throw new ValidationException(
                    __('Content field contains restricted HTML elements. %1', $exception->getMessage()),
                    $exception
                );
            }
        }

        return $this;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG ];
    }

    /**
     * Retrieve block id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::R_ID);
    }

    /**
     * Retrieve campain id
     *
     * @return string
     */
    public function getCampId()
    {
        return (string)$this->getData(self::CAMP_ID);
    }

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->getData(self::FIRSTNAME);
    }


    /**
     * Retrieve name
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->getData(self::LASTNAME);
    }


    /**
     * Retrieve email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * Retrieve block content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }


    /**
     * Retrieve newsletter
     *
     * @return int
     */
    public function getNewsletter()
    {
        return $this->getData(self::NEWSLETTER);
    }


    /**
     * Retrieve terms and conditions agreed
     *
     * @return int
     */
    public function getTermsAgreed()
    {
        return $this->getData(self::TERMSAGREED);
    }


    /**
     * Retrieve use data for marketing agreed
     *
     * @return int
     */
    public function getUsedataAgreed()
    {
        return $this->getData(self::USEDATAAGREED);
    }

    /**
     * Retrieve imagename
     *
     * @return int
     */
    public function getImagename()
    {
        return $this->getData(self::IMAGENAME);
    }

    /**
     * Retrieve image 2 name
     *
     * @return string
     */
    public function getImage2name()
    {
        return $this->getData(self::IMAGE2NAME);
    }

    /**
     * Retrieve block creation time
     *
     * @return string
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Retrieve block update time
     *
     * @return string
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Is active
     *
     * @return bool
     */
    public function getIsActive()
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * get validation field
     *
     * @return string
     */
    public function getValidationfield()
    {
        return $this->getData(self::VALIDATIONFIELD);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return ResultsInterface
     */
    public function setId($id)
    {
        return $this->setData(self::R_ID, $id);
    }

    /**
     * Set campaign id
     *
     * @param int $campid
     * @return ResultsInterface
     */
    public function setCampId($campid)
    {
        return $this->setData(self::CAMP_ID, $campid);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ResultsInterface
     */
    public function setFirstname($name)
    {
        return $this->setData(self::FIRSTNAME, $name);
    }


    /**
     * Set last name
     *
     * @param string $name
     * @return ResultsInterface
     */
    public function setLastname($name)
    {
        return $this->setData(self::LASTNAME, $name);
    }


    /**
     * Set email
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setEmail($value)
    {
        return $this->setData(self::EMAIL, $value);
    }


    /**
     * Set content
     *
     * @param string $content
     * @return ResultsInterface
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Set newsletter
     *
     * @param int $nid
     * @return ResultsInterface
     */
    public function setNewsletter($nid)
    {
        return $this->setData(self::NEWSLETTER, $nid);
    }

    /**
     * Set terms and conditions agreed
     *
     * @param int $termsagreed
     * @return ResultsInterface
     */
    public function setTermsAgreed($termsagreed)
    {
        return $this->setData(self::TERMSAGREED, $termsagreed);
    }

    /**
     * Set use data for marketing
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setUsedataAgreed($value)
    {
        return $this->setData(self::USEDATAAGREED, $value);
    }

    /**
     * Set Imagename
     *
     * @param string $imgname
     * @return ResultsInterface
     */
    public function setImagename($imgname)
    {
        return $this->setData(self::IMAGENAME, $imgname);
    }

    /**
     * Set Image 2 name
     *
     * @param string $imgname
     * @return ResultsInterface
     */
    public function setImage2name($imgname)
    {
        return $this->setData(self::IMAGE2NAME, $imgname);
    }

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return ResultsInterface
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return ResultsInterface
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Set is active
     *
     * @param bool|int $isActive
     * @return ResultsInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Set validation field
     *
     * @param bool|string $value
     * @return ResultsInterface
     */
    public function setValidationfield($value)
    {
        return $this->setData(self::VALIDATIONFIELD, $value);
    }

}
