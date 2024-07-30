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
    public const CAMP_ID       = 'camp_id';
    public const IDENTIFIER    = 'identifier';
    public const TITLE         = 'title';
    public const CONTENT       = 'content';
    public const CONTENTBOTTOM = 'contentbottom';
    public const NEWSLETTER    = 'newsletter';
    public const SHOWPHONE     = 'showphone';
    public const CREATION_TIME = 'creation_time';
    public const UPDATE_TIME   = 'update_time';
    public const IS_ACTIVE     = 'is_active';

    public const BLOCK_ID     = 'block_identifier';
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
     * Get block id
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
     * Set block id
     *
     * @param bool|int $value
     * @return CampaignInterface
     */
    public function setBlockId($value);
}
