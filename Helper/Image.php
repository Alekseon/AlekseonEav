<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Helper;

use Alekseon\AlekseonEav\Model\Entity;
use Magento\Framework\Encryption\Encryptor;
use Psr\Log\LoggerInterface;

/**
 * Class Image
 * @package Alekseon\AlekseonEav\Helper
 */
class Image
{
    /**
     * @var
     */
    protected $imagePath;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var
     */
    protected $imageFactory;
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;
    /**
     * @var
     */
    protected $image;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;
    /**
     * @var array
     */
    protected $miscParams = [];

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    )
    {
        $this->storeManager = $storeManager;
        $this->imageFactory = $imageFactory;
        $this->directoryList = $directoryList;
        $this->encryptor = $encryptor;
    }

    /**
     *
     */
    public function init(Entity $entity, $attributeCode)
    {
        $mediaDir = $this->directoryList->getPath('media');
        $imagePath = $mediaDir . DIRECTORY_SEPARATOR . $entity->getData($attributeCode);
        try {
            $this->setImagePath($imagePath);
        } catch (\Exception $e) {
            // do nothing
        }
        return $this;
    }

    /**
     * @param $imagePath
     * @return $this
     */
    public function setImagePath($imagePath)
    {
        $this->reset();
        $this->imagePath = $imagePath;
        $this->image = $this->imageFactory->create($imagePath);
        return $this;
    }

    /**
     * @return $this
     */
    protected function reset()
    {
        $this->imagePath = null;
        $this->miscParams = [];
        $this->image = null;
        return $this;
    }

    /**
     *
     */
    public function setWidth(int $width)
    {
        $this->miscParams['width'] = $width;
        return $this;
    }

    /**
     *
     */
    public function setHeight(int $height)
    {
        $this->miscParams['height'] = $height;
        return $this;
    }

    /**
     *
     */
    public function resize($allowBiggerSize = false, $needResize = false)
    {
        $originalWidth = $this->image->getOriginalWidth();
        $originalHeight = $this->image->getOriginalHeight();

        $width = $this->miscParams['width'] ?? $originalWidth;
        $height = $this->miscParams['width'] ?? $originalHeight;

        if (!$allowBiggerSize) {
            $width = min($originalWidth, $width);
            $height = min($originalHeight, $height);
        }

        if ($width != $originalWidth) {
            $needResize = true;
        }

        if ($height != $originalHeight) {
            $needResize = true;
        }

        if ($needResize) {
            $this->image->keepAspectRatio(true);
            $this->image->resize($width, $height);
        }
        return $this;
    }

    /**
     * @return \Magento\Framework\Image
     */
    protected function prepareOutputImage()
    {
        $this->resize();
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl($storeId = null)
    {
        $mediaDir = $this->directoryList->getPath('media');

        $path = 'cache'
            . DIRECTORY_SEPARATOR . 'alekseon_eav'
            . DIRECTORY_SEPARATOR . $this->getMiscPath()
            . DIRECTORY_SEPARATOR . $this->imagePath;

        if (!file_exists($mediaDir . DIRECTORY_SEPARATOR . $path)) {
            try {
                $this->prepareOutputImage();
            } catch (\Exception $e) {
                return false;
            }

            $pathParts = explode(DIRECTORY_SEPARATOR, $path);
            $fileName = array_pop($pathParts);
            $this->image->save(
                $mediaDir . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pathParts)
                , $fileName
            );
        }

        $mediaUrl = $this->storeManager->getStore($storeId)
            ->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );

        return $mediaUrl . $path;
    }

    /**
     * @return string
     */
    protected function getMiscPath()
    {
        return $this->encryptor->hash(
            implode('_', $this->miscParams),
            Encryptor::HASH_VERSION_MD5
        );
    }
}
