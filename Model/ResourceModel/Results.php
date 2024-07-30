<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Model\ResourceModel;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Theiconnz\Campaigns\Api\Data\ResultsInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;

/**
 * Results database model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Results extends AbstractDb
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $io;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param Filesystem $filesystem
     * @param File $file
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        Filesystem $filesystem,
        File $file,
        $connectionName = null
    ) {
        $this->_storeManager = $storeManager;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->filesystem = $filesystem;
        $this->io = $file;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('campaign_results', 'r_id');
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(ResultsInterface::class)->getEntityConnection();
    }

    /**
     * Get camp id.
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws \Exception
     */
    private function getBlockId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(ResultsInterface::class);
        if (!is_numeric($value) && $field === null) {
            $field = 'r_id';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }
        return $entityId;
    }

    /**
     * Load an object
     *
     * @param \Theiconnz\Campaigns\Model\Results|AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     * @throws LocalizedException
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $blockId = $this->getBlockId($object, $value, $field);
        if ($blockId) {
            $this->entityManager->load($object, $blockId);
        }
        return $this;
    }

    /**
     * Save an object.
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->deleteProfileImage($object);
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function deleteProfileImage(AbstractModel $object)
    {
        if ($object->getImagename() != nul) {
            $filename = $object->getImagename();
            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $destinationPath = $mediaDirectory->getAbsolutePath(\Theiconnz\Campaigns\Model\Results::UPLOADPATH);
            $file = $destinationPath . $filename;

            if ($this->io->fileExists($file)) {
                $this->io->rm($filename);
            }
        }
    }
}
