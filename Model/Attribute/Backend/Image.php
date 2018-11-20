<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\Backend;

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
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $imageAdapterFactory;

    /**
     * Image constructor.
     * @param \Magento\Framework\Image\AdapterFactory $imageAdapterFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     */
    public function __construct(
        \Magento\Framework\Image\AdapterFactory $imageAdapterFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
    ) {
        $this->imageAdapterFactory = $imageAdapterFactory;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * @return string
     */
    public function getImagesDirPath()
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        return $mediaDirectory->getAbsolutePath();
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
        if (isset($_FILES[$attrCode]) && $_FILES[$attrCode]['tmp_name']) {
            $uploader = $this->uploaderFactory->create(['fileId' => $attrCode]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

            $imageAdapter = $this->imageAdapterFactory->create();
            $uploader->addValidateCallback('eav_image_attribute', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);

            $result = $uploader->save($this->getImagesDirPath() . $imagesDirName);

            $attrCode = $this->getAttribute()->getAttributeCode();
            $object->setData($attrCode, $imagesDirName . $result['file']);
        }
        $value = $object->getData($attrCode);
        if (is_array($value)) {
            if (isset($value['delete'])) {
                $object->setData($attrCode, null);
            } else {
                $object->setData($attrCode, $value['value']);
            }
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
        if ($newValue != $oldValue) {
            $currentImageValues = $object->getResource()->getAllAttributeValues($object, $this->getAttribute());
            foreach ($currentImageValues as $imageValue) {
                if ($imageValue['value'] == $oldValue) {
                    return parent::afterSave($object);
                }
            }
            @unlink($this->getImagesDirPath() . $oldValue);
        }

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
            $filePath = $this->getImagesDirPath() . $imageValue['value'];
            @unlink($filePath);
        }
        return parent::afterDelete($object);
    }

    /**
     * @param $object
     * @return bool
     */
    public function isAttributeValueUpdated($object, $isAttributeVAlueUpdated)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if (isset($_FILES[$attrCode]) && $_FILES[$attrCode]['tmp_name']) {
            return true;
        }
        return false;
    }
}
