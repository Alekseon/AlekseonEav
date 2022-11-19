<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Helper;

use Alekseon\AlekseonEav\Model\Entity;
use Magento\Framework\Encryption\Encryptor;

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
     * @var
     */
    protected $entity;
    /**
     * @var
     */
    protected $imageFactory;
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;
    /**
     * @var array
     */
    protected $miscParams = [];

    /**
     * Image constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
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
    public function init(Entity $entity, $attributrCode)
    {
        $this->reset();
        $this->entity = $entity;
        $this->imagePath = $entity->getData($attributrCode);
        return $this;
    }

    /**
     * @return $this
     */
    protected function reset()
    {
        $this->imagePath = null;
        $this->entity = null;
        $this->miscParams = [];
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
     * @return \Magento\Framework\Image
     */
    protected function prepareOutputImage()
    {
        $mediaDir = $this->directoryList->getPath('media');
        $imagePath = $mediaDir . DIRECTORY_SEPARATOR . $this->imagePath;
        $im = $this->imageFactory->create($imagePath);
        $width = $this->miscParams['width'] ?? $im->getOriginalWidth();
        $height = $this->miscParams['width'] ?? $im->getOriginalHeight();
        $im->keepAspectRatio(true);
        $im->resize($width, $height);
        return $im;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl()
    {
        $mediaDir = $this->directoryList->getPath('media');

        $path = 'cache'
            . DIRECTORY_SEPARATOR . 'alekseon_eav'
            . DIRECTORY_SEPARATOR . $this->getMiscPath()
            . DIRECTORY_SEPARATOR . $this->imagePath;

        if (!file_exists($mediaDir . DIRECTORY_SEPARATOR . $path)) {
            try {
                $im = $this->prepareOutputImage();
            } catch (\Exception $e) {
                return false;
            }

            $pathParts = explode(DIRECTORY_SEPARATOR, $path);
            $fileName = array_pop($pathParts);
            $im->save(
                $mediaDir . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pathParts)
                , $fileName
            );
        }

        $mediaUrl = $this->storeManager->getStore($this->entity->getStoreId())
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
