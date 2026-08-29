<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\Backend;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Filesystem\Directory\WriteInterface;

/**
 * Class Image
 * @package Alekseon\AlekseonEav\Model\Attribute\Backend
 */
class Image extends AbstractBackend
{
    /**
     * @var array
     */
    private $imagesToBeDeleted = [];
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     * @deprecated file removal goes through the media directory (see deleteImageFile())
     */
    protected $file;
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $imageAdapterFactory;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;
    /**
     * @var \Alekseon\AlekseonEav\Helper\Image
     */
    protected $imageHelper;
    /**
     * @var WriteInterface|null
     */
    private $mediaDirectory;

    /**
     * Image constructor.
     * @param \Magento\Framework\Image\AdapterFactory $imageAdapterFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Alekseon\AlekseonEav\Helper\Image $imageHelper
     */
    public function __construct(
        \Magento\Framework\Image\AdapterFactory $imageAdapterFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \Alekseon\AlekseonEav\Helper\Image $imageHelper
    ) {
        $this->imageAdapterFactory = $imageAdapterFactory;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->uploaderFactory = $uploaderFactory;
        $this->urlInterface = $urlInterface;
        $this->imageHelper = $imageHelper;
    }

    /**
     * @return string
     */
    public function getImagesDirPath()
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        return $mediaDirectory->getAbsolutePath();
    }

    /**
     * @return WriteInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }

    /**
     * Remove an image file, never touching anything outside of the media directory.
     *
     * @param mixed $relativePath
     * @return void
     */
    private function deleteImageFile($relativePath)
    {
        if (!is_string($relativePath) || trim($relativePath) === '') {
            return;
        }

        try {
            // WriteInterface::delete() validates that the path stays inside pub/media
            $this->getMediaDirectory()->delete($relativePath);
        } catch (ValidatorException $e) {
            // path points outside of the media directory, it must never be removed
        } catch (\Exception $e) {
            // do nothing
        }
    }

    /**
     * @param $object
     * @return $this
     * @throws \Exception
     */
    public function beforeSave($object)
    {
        $imagesDirName = $object->getResource()->getImagesDirName();
        $attrCode = $this->getAttribute()->getAttributeCode();

        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $attrCode]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        } catch (\Exception $e) {
            $uploader = false;
        }

        if ($uploader) {
            $file = $uploader->validateFile();

            if (!$file['tmp_name'] || $file['error']) {
                throw new LocalizedException(__('The file was not uploaded.'));
            }

            if (!$uploader->checkMimeType(['image/png', 'image/jpeg', 'image/gif'])) {
                throw new LocalizedException(__('File validation failed.'));
            }

            $imageAdapter = $this->imageAdapterFactory->create();
            $uploader->addValidateCallback('eav_image_attribute', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $fileName = $object->getResource()->getNameForUploadedFile($object, $this->getAttribute(), $file['name']);

            $this->imageHelper->setImagePath($file['tmp_name']);
            $this->imageHelper->resize(false, true);
            $this->imageHelper->getImage()->save();
            $result = $uploader->save($this->getImagesDirPath() . $imagesDirName, $fileName);

            $object->setData(
                $attrCode,
                rtrim($imagesDirName, '/') . '/' . ltrim($result['file'], '/')
            );
        }
        $value = $object->getData($attrCode);
        if (is_array($value)) {
            // The request only decides whether the image is dropped. The path itself is never taken
            // from the posted "[value]" field (that is just Magento's hidden round trip input),
            // it always comes from the database.
            $object->setData($attrCode, empty($value['delete']) ? $object->getOrigData($attrCode) : null);
        }

        return parent::beforeSave($object);
    }

    /**
     * @param $object
     * @return $this
     */
    public function afterSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $newValue = $object->getData($attrCode);
        $oldValue = $object->getOrigData($attrCode);

        if ($newValue == $oldValue || !$oldValue) {
            return parent::afterSave($object);
        }

        $currentImageValues = $object->getResource()->getAllAttributeValues($object, $this->getAttribute());
        foreach ($currentImageValues as $imageValue) {
            if ($imageValue['value'] == $oldValue) {
                // the file is still used by another store view
                return parent::afterSave($object);
            }
        }

        $this->deleteImageFile($oldValue);

        return parent::afterSave($object);
    }

    /**
     * @param $object
     * @return $this|void
     */
    public function beforeDelete($object)
    {
        $this->imagesToBeDeleted = $object->getResource()->getAllAttributeValues($object, $this->getAttribute());
        return parent::beforeDelete($object);
    }

    /**
     * @param $object
     * @return $this|void
     */
    public function afterDelete($object)
    {
        foreach ($this->imagesToBeDeleted as $imageValue) {
            $this->deleteImageFile($imageValue['value'] ?? null);
        }
        return parent::afterDelete($object);
    }

    /**
     * @param $object
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isAttributeValueUpdated($object)
    {
        $isAttributeUpdated = parent::isAttributeValueUpdated($object);
        $attrCode = $this->getAttribute()->getAttributeCode();
        // phpcs:disable Magento2.Security.Superglobal
        if (!$isAttributeUpdated && isset($_FILES[$attrCode])) {
            $isAttributeUpdated = true;
        }
        // phpcs:enable Magento2.Security.Superglobal
        return $isAttributeUpdated;
    }
}
