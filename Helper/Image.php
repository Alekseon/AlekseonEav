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
    private $imagePath;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var
     */
    private $imageFactory;
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;
    /**
     * @var
     */
    private $image;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;
    /**
     * @var array
     */
    private $miscParams = [];

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
     * @param Entity $entity
     * @param string $attributeCode
     * @return $this
     */
    public function init(Entity $entity, string $attributeCode)
    {
        $imagePath = $entity->getData($attributeCode);
        try {
            $this->setImagePath($imagePath, true);
        } catch (\Exception $e) {
            // do nothing
        }
        return $this;
    }

    /**
     * @param string $imagePath
     * @param bool $isMediaImage
     * @return $this
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function setImagePath(string $imagePath, bool $isMediaImage = false)
    {
        $this->reset();
        $this->imagePath = $imagePath;
        if ($isMediaImage) {
            $mediaDir = $this->directoryList->getPath('media');
            $imagePath = $mediaDir . DIRECTORY_SEPARATOR . $imagePath;
        }
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
     * @return $this
     */
    public function setWidth(int $width)
    {
        $this->miscParams['width'] = $width;
        return $this;
    }

    /**
     * @param int $height
     * @return $this
     */
    public function setHeight(int $height)
    {
        $this->miscParams['height'] = $height;
        return $this;
    }

    /**
     * @param bool $allowBiggerSize
     * @param bool $needResize
     * @return $this
     */
    public function resize(bool $allowBiggerSize = false, bool $needResize = false)
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
     * @return $this
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
     * @param $storeId
     * @return false|string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl($storeId = null)
    {
        if (!$this->image) {
            return '';
        }

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
