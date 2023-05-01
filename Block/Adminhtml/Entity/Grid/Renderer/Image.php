<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Block\Adminhtml\Entity\Grid\Renderer;

/**
 * Class Image
 * @package Alekseon\AleksoenEav\Block\Adminhtml\Entity\Grid\Renderer
 */
class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Alekseon\AlekseonEav\Helper\Image
     */
    protected $imageHelper;

    /**
     * Image constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Alekseon\AlekseonEav\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Alekseon\AlekseonEav\Helper\Image $imageHelper,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string|void
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if (!$row->getData($this->getColumn()->getIndex())) {
            return null;
        }

        $this->imageHelper->init($row, $this->getColumn()->getIndex());
        $this->imageHelper->setWidth(100);
        $this->imageHelper->setHeight(100);
        $url =  $this->imageHelper->getUrl();
        return '<img src="' . $url . '"/>';
    }
}
