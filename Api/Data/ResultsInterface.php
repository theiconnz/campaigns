<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Api\Data;

/**
 * Results interface.
 * @api
 * @since 0.0.1
 */
interface ResultsInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const R_ID          = 'r_id';
    const CAMP_ID       = 'camp_id';
    const FIRSTNAME     = 'firstname';
    const LASTNAME      = 'lastname';
    const EMAIL         = 'email';
    const CONTENT       = 'content';
    const IMAGENAME     = 'imagename';
    const NEWSLETTER    = 'newsletter';
    const VALIDATIONFIELD    = 'validationfield';
    const TERMSAGREED   = 'terms_agreed';
    const USEDATAAGREED = 'usedata_agreed';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME   = 'update_time';
    const IS_ACTIVE     = 'is_active';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get campaign id
     *
     * @return int
     */
    public function getCampId();

    /**
     * Get first name
     *
     * @return string|null
     */
    public function getFirstname();

    /**
     * Get last name
     *
     * @return string|null
     */
    public function getLastname();

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Get newsletter
     *
     * @return int
     */
    public function getNewsletter();


    /**
     * Get terms and conditions agreed
     *
     * @return int
     */
    public function getTermsAgreed();


    /**
     * Get use data
     *
     * @return int
     */
    public function getUsedataAgreed();


    /**
     * Get imagename
     *
     * @return string
     */
    public function getImagename();

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
     * Validation field
     *
     * @return string|null
     */
    public function getValidationfield();

    /**
     * Set ID
     *
     * @param int $id
     * @return ResultsInterface
     */
    public function setId($id);

    /**
     * Set campaign id
     *
     * @param int $campid
     * @return ResultsInterface
     */
    public function setCampId($campid);

    /**
     * Set first name
     *
     * @param string $name
     * @return ResultsInterface
     */
    public function setFirstname($name);

    /**
     * Set last name
     *
     * @param string $name
     * @return ResultsInterface
     */
    public function setLastname($name);

    /**
     * Set email
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setEmail($value);

    /**
     * Set content
     *
     * @param string $content
     * @return ResultsInterface
     */
    public function setContent($content);

    /**
     * Set Newsletter
     *
     * @param int $newsletter
     * @return ResultsInterface
     */
    public function setNewsletter($newsletter);

    /**
     * Set terms and conditions agreed
     *
     * @param int $termsagreed
     * @return ResultsInterface
     */
    public function setTermsAgreed($termsagreed);


    /**
     * Set use data for marketing
     *
     * @param int $termsagreed
     * @return ResultsInterface
     */
    public function setUsedataAgreed($termsagreed);


    /**
     * Set Imagename
     *
     * @param string $imagename
     * @return ResultsInterface
     */
    public function setImagename($imagename);


    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return ResultsInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return ResultsInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set is active
     *
     * @param bool|int $isActive
     * @return ResultsInterface
     */
    public function setIsActive($isActive);

    /**
     * Set validation field
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setValidationfield($value);
}
