<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Helper;

use Alekseon\AlekseonEav\Model\Entity;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Filesystem\Directory\WriteInterface;

/**
 * Class Image
 * @package Alekseon\AlekseonEav\Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;
    /**
     * @var WriteInterface|null
     */
    private $mediaDirectory;
    /**
     * @var array
     */
    private $miscParams = [];

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    )
    {
        $this->storeManager = $storeManager;
        $this->imageFactory = $imageFactory;
        $this->filesystem = $filesystem;
        $this->encryptor = $encryptor;
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
     * @param Entity $entity
     * @param string $attributeCode
     * @return $this
     */
    public function init(Entity $entity, string $attributeCode)
    {
        $imagePath = (string) $entity->getData($attributeCode);
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
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function setImagePath(string $imagePath, bool $isMediaImage = false)
    {
        $this->reset();
        if (empty($imagePath)) {
            return $this;
        }

        // getAbsolutePath() validates that the path stays inside pub/media
        $absolutePath = $isMediaImage
            ? $this->getMediaDirectory()->getAbsolutePath($imagePath)
            : $imagePath;

        $this->imagePath = $imagePath;
        $this->image = $this->imageFactory->create($absolutePath);
        return $this;
    }

    /**
     * @return $this
     */
    private function reset()
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
    private function prepareOutputImage()
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
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function getUrl($storeId = null)
    {
        if (!$this->image) {
            return '';
        }

        $mediaDirectory = $this->getMediaDirectory();

        $path = 'cache/alekseon_eav/' . $this->getMiscPath() . '/' . $this->imagePath;

        if (!$mediaDirectory->isExist($path)) {
            try {
                $this->prepareOutputImage();
            } catch (\Exception $e) {
                return false;
            }

            $pathParts = explode('/', $path);
            $fileName = array_pop($pathParts);
            // getAbsolutePath() validates the destination stays inside pub/media
            $this->image->save(
                $mediaDirectory->getAbsolutePath(implode('/', $pathParts)),
                $fileName
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
    private function getMiscPath()
    {
        return $this->encryptor->hash(
            implode('_', $this->miscParams),
            Encryptor::HASH_VERSION_MD5
        );
    }
}
