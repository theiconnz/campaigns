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
use Theiconnz\Campaigns\Api\Data\CampaignInterface;
use Theiconnz\Campaigns\Api\Data\ResultsInterface;

/**
 * CMS block model
 *
 * @method Campaign setStoreId(int $storeId)
 * @method int getStoreId()
 */
class Campaign extends AbstractModel implements CampaignInterface, IdentityInterface
{
    /**
     * CMS block cache tag
     */
    const CACHE_TAG = 'campaign_b';

    /**#@+
     * Block's statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**#@-*/

    /**#@-*/
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'campaign_';

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
        $this->_init(\Theiconnz\Campaigns\Model\ResourceModel\Campaign::class);
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

        $needle = 'camp_id="' . $this->getId() . '"';
        if (strstr($this->getContent(), (string) $needle) !== false) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Make sure that static campaign content does not reference the block itself.')
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
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getIdentifier()];
    }

    /**
     * Retrieve block id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::CAMP_ID);
    }

    /**
     * Retrieve block identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return (string)$this->getData(self::IDENTIFIER);
    }

    /**
     * Retrieve block title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
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
     * Retrieve block content bootom
     *
     * @return string
     */
    public function getContentbottom()
    {
        return $this->getData(self::CONTENTBOTTOM);
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
     * Retrieve show phone
     *
     * @return int
     */
    public function getShowphone()
    {
        return $this->getData(self::SHOWPHONE);
    }

    /**
     * Retrieve show name
     *
     * @return int
     */
    public function getShowname()
    {
        return $this->getData(self::SHOWNAME);
    }


    /**
     * Retrieve show last name
     *
     * @return int
     */
    public function getShowLastname()
    {
        return $this->getData(self::SHOWLASTNAME);
    }

    /**
     * Retrieve show email
     *
     * @return int
     */
    public function getShowemail()
    {
        return $this->getData(self::SHOWEMAIL);
    }

    /**
     * Retrieve show upload
     *
     * @return int
     */
    public function getShowupload()
    {
        return $this->getData(self::SHOWUPLOAD);
    }

    /**
     * Retrieve show content
     *
     * @return int
     */
    public function getShowcontent()
    {
        return $this->getData(self::SHOWCONTENT);
    }

    /**
     * Retrieve show marketing
     *
     * @return int
     */
    public function getShowmarketing()
    {
        return $this->getData(self::SHOWMARKETING);
    }

    /**
     * Retrieve get one entry
     *
     * @return int
     */
    public function getOneEntry()
    {
        return $this->getData(self::ONEENTRY);
    }


    /**
     * Retrieve get newsletter text
     *
     * @return string
     */
    public function getNewsletterText()
    {
        return $this->getData(self::TEXTNEWSLETTER);
    }

    /**
     * Retrieve get content text
     *
     * @return string
     */
    public function getContentText()
    {
        return $this->getData(self::TEXTCONTENT);
    }

    /**
     * Retrieve get terms text
     *
     * @return string
     */
    public function getTermsText()
    {
        return $this->getData(self::TEXTTERMS);
    }


    /**
     * Retrieve get merketing text
     *
     * @return string
     */
    public function getMarketingText()
    {
        return $this->getData(self::TEXTMARKETING);
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
     * Is form enable
     *
     * @return bool
     */
    public function getFormEnable()
    {
        return (bool)$this->getData(self::FORMENABLE);
    }

    /**
     * Block Id
     *
     * @return int
     */
    public function getBlockId()
    {
        return (bool)$this->getData(self::BLOCK_ID);
    }

    /**
     * Validation field
     *
     * @return int
     */
    public function getValidationfield()
    {
        return (bool)$this->getData(self::VALIDATIONFIELD);
    }

    /**
     * Validation field size
     *
     * @return int
     */
    public function getValidationfieldsize()
    {
        return $this->getData(self::VALIDATIONFIELDSIZE);
    }

    /**
     * get fiel upload manatory
     *
     * @return int
     */
    public function getUloadFieldMandatory()
    {
        return (bool)$this->getData(self::FILEUPLOADMANDATORY);
    }


    /**
     * Set ID
     *
     * @param int $id
     * @return BlockInterface
     */
    public function setId($id)
    {
        return $this->setData(self::CAMP_ID, $id);
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return BlockInterface
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return BlockInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set content
     *
     * @param string $content
     * @return CamaignInterface
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }


    /**
     * Set content bottom
     *
     * @param string $content
     * @return CamaignInterface
     */
    public function setContentbottom($content)
    {
        return $this->setData(self::CONTENTBOTTOM, $content);
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
     * Set show phone
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowphone($value)
    {
        return $this->setData(self::SHOWPHONE, $value);
    }

    /**
     * Set show name
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowname($value)
    {
        return $this->setData(self::SHOWNAME, $value);
    }


    /**
     * Set show last name
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowLastname($value)
    {
        return $this->setData(self::SHOWLASTNAME, $value);
    }


    /**
     * Set show email
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowemail($value)
    {
        return $this->setData(self::SHOWEMAIL, $value);
    }


    /**
     * Set show upload
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowupload($value)
    {
        return $this->setData(self::SHOWUPLOAD, $value);
    }
    /**
     * Set show content
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowcontent($value)
    {
        return $this->setData(self::SHOWCONTENT, $value);
    }
    /**
     * Set show marketing
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowmarketing($value)
    {
        return $this->setData(self::SHOWCONTENT, $value);
    }

    /**
     * Set show one entry
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setOneEntry($value)
    {
        return $this->setData(self::ONEENTRY, $value);
    }


    /**
     * Set newsletter text
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setNewsletterText($value)
    {
        return $this->setData(self::TEXTNEWSLETTER, $value);
    }


    /**
     * Set content text
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setContentText($value)
    {
        return $this->setData(self::TEXTCONTENT, $value);
    }
    /**
     *
     * Set terms text
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setTermsText($value)
    {
        return $this->setData(self::TEXTTERMS, $value);
    }
    /**
     * Set marketing text
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setMarketingText($value)
    {
        return $this->setData(self::TEXTMARKETING, $value);
    }


    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return CamaignInterface
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return CamaignInterface
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Set is active
     *
     * @param bool|int $isActive
     * @return CamaignInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }


    /**
     * Set is form enable
     *
     * @param bool|int $value
     * @return CamaignInterface
     */
    public function setFormEnable($value)
    {
        return $this->setData(self::FORMENABLE, $value);
    }


    /**
     * Set block id
     *
     * @param int $value
     */
    public function setBlockId($value)
    {
        return $this->setData(self::BLOCK_ID, $value);
    }

    /**
     * Set validation field
     *
     * @param int $value
     */
    public function setValidationfield($value)
    {
        return $this->setData(self::VALIDATIONFIELD, $value);
    }

    /**
     * Set validation field  size
     *
     * @param int $value
     */
    public function setValidationfieldsize($value)
    {
        return $this->setData(self::VALIDATIONFIELDSIZE, $value);
    }

    /**
     * Set upload field mandatory
     *
     * @param int $value
     */
    public function setFileuploadMandatory($value)
    {
        return $this->setData(self::FILEUPLOADMANDATORY, $value);
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }

    /**
     * Prepare block's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }


}
