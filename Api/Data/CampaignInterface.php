<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Api\Data;

/**
 * Campaigns interface.
 * @api
 * @since 0.0.1
 */
interface CampaignInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const CAMP_ID       = 'camp_id';
    const IDENTIFIER    = 'identifier';
    const TITLE         = 'title';
    const CONTENT       = 'content';
    const CONTENTBOTTOM = 'contentbottom';
    const NEWSLETTER    = 'newsletter';
    const SHOWPHONE     = 'showphone';

    const SHOWNAME     = 'disablename';

    const SHOWLASTNAME  = 'showlastname';

    const SHOWEMAIL     = 'disableemail';
    const SHOWUPLOAD     = 'disableupload';
    const SHOWUPLOAD2     = 'disableupload2';
    const SHOWCONTENT   = 'disablecontent';

    const SHOWMARKETING   = 'disablemarketing';

    const ONEENTRY      = 'oneentry';

    const VALIDATIONFIELD   = 'validationfield';

    const VALIDATIONFIELDSIZE   = 'validationfieldsize';
    const FILEUPLOADMANDATORY   = 'fileupload_mandatory';

    const FILEUPLOAD2MANDATORY   = 'fileupload_2_mandatory';
    const FILE1LABEL            = 'file_1_label';
    const FILE2LABEL            = 'file_2_label';
    const TEXTNEWSLETTER   = 'newslettersubtext';
    const TEXTMARKETING   = 'marketingtext';
    const TEXTTERMS   = 'termstext';
    const TEXTCONTENT   = 'contenttext';

    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME   = 'update_time';
    const IS_ACTIVE     = 'is_active';
    const FORMENABLE     = 'form_enable';

    const BLOCK_ID     = 'block_identifier';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Get content bottom
     *
     * @return string
     */
    public function getContentbottom();

    /**
     * Get newsletter
     *
     * @return int
     */
    public function getNewsletter();

    /**
     * Get show phone
     *
     * @return int
     */
    public function getShowphone();

    /**
     * Get show name
     *
     * @return int
     */
    public function getShowname();

    /**
     * Get show last name
     *
     * @return int
     */
    public function getShowLastname();


    /**
     * Get show email
     *
     * @return int
     */
    public function getShowemail();


    /**
     * Get show upload
     *
     * @return int
     */
    public function getShowupload();


    /**
     * Get show upload 2
     *
     * @return int
     */
    public function getShowUpload2();


    /**
     * Get show CONTENT
     *
     * @return int
     */
    public function getShowcontent();

    /**
     * Get show MARKETING
     *
     * @return int
     */
    public function getShowmarketing();

    /**
     * Get show oen entry
     *
     * @return int
     */
    public function getOneEntry();

    /**
     * Get newsletter text
     *
     * @return string|null
     */
    public function getNewsletterText();

    /**
     * Get upload 1 label
     *
     * @return string|null
     */
    public function getUpload1Label();

    /**
     * Get upload 2 label
     *
     * @return string|null
     */
    public function getUpload2Label();


    /**
     * Get content text
     *
     * @return string|null
     */
    public function getContentText();

    /**
     * Get terms text
     *
     * @return string|null
     */
    public function getTermsText();

    /**
     * Get marketing text
     *
     * @return string|null
     */
    public function getMarketingText();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function getIsActive();

    /**
     * Get validation field
     *
     * @return int|null
     */
    public function getValidationfield();

    /**
     * Get validation field size
     *
     * @return int|null
     */
    public function getValidationfieldsize();

    /**
     * Get is upload field mandatory
     *
     * @return int|null
     */
    public function getUloadFieldMandatory();

    /**
     * Get is upload 2 field mandatory
     *
     * @return int|null
     */
    public function getUloadField2Mandatory();

    /**
     * Is form enable
     *
     * @return bool|null
     */
    public function getFormEnable();


    /**
     * get block id
     *
     * @return int
     */
    public function getBlockId();


    /**
     * Set ID
     *
     * @param int $id
     * @return CampaignInterface
     */
    public function setId($id);

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return CampaignInterface
     */
    public function setIdentifier($identifier);

    /**
     * Set title
     *
     * @param string $title
     * @return CampaignInterface
     */
    public function setTitle($title);

    /**
     * Set content
     *
     * @param string $content
     * @return CampaignInterface
     */
    public function setContent($content);

    /**
     * Set content bottom
     *
     * @param string $content
     * @return CampaignInterface
     */
    public function setContentbottom($content);

    /**
     * Set Newsletter
     *
     * @param int $newsletter
     * @return ResultsInterface
     */
    public function setNewsletter($newsletter);

    /**
     * Set Show phone
     *
     * @param int $phone
     * @return ResultsInterface
     */
    public function setShowphone($phone);

    /**
     * Set Show name
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowname($value);

    /**
     * Set Show last name
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowLastname($value);

    /**
     * Set Show email
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowemail($value);

    /**
     * Set Show upload
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowupload($value);

    /**
     * Set Show upload 2
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowUpload2($value);

    /**
     * Set Show content
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowcontent($value);


    /**
     * Set Show marketing
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setShowmarketing($value);

    /**
     * Set Show one entry
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setOneEntry($value);

    /**
     * Set newsletter text
     *
     * @param string $value
     * @return CampaignInterface
     */
    public function setNewsletterText($value);

    /**
     * Set upload 1 label
     *
     * @param string $value
     * @return CampaignInterface
     */
    public function setUpload1Label($value);

    /**
     * Set upload 2 label
     *
     * @param string $value
     * @return CampaignInterface
     */
    public function setUpload2Label($value);

    /**
     * Set content text
     *
     * @param string $value
     * @return CampaignInterface
     */
    public function setContentText($value);

    /**
     * Set terms text
     *
     * @param string $value
     * @return CampaignInterface
     */
    public function setTermsText($value);

    /**
     * Set marketing text
     *
     * @param string $value
     * @return CampaignInterface
     */
    public function setMarketingText($value);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return CampaignInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return CampaignInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set is active
     *
     * @param bool|int $isActive
     * @return CampaignInterface
     */
    public function setIsActive($isActive);

    /**
     * Set is form enable
     *
     * @param bool|int $isActive
     * @return CampaignInterface
     */
    public function setFormEnable($isActive);

    /**
     * Set block id
     *
     * @param bool|int $value
     * @return CampaignInterface
     */
    public function setBlockId($value);

    /**
     * Set validation field
     *
     * @param bool|int $value
     * @return CampaignInterface
     */
    public function setValidationfield($value);

    /**
     * Set validation field size
     *
     * @param bool|int $value
     * @return CampaignInterface
     */
    public function setValidationfieldsize($value);

    /**
     * Set file uplaod field mandatory
     *
     * @param bool|int $value
     * @return CampaignInterface
     */
    public function setFileuploadMandatory($value);

    /**
     * Set file uplaod 2 field mandatory
     *
     * @param bool|int $value
     * @return CampaignInterface
     */
    public function setFileupload2Mandatory($value);

}
