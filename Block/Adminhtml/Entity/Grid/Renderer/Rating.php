<?php
namespace Alekseon\AlekseonEav\Block\Adminhtml\Entity\Grid\Renderer;
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
class Rating extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return string|void|null
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $rate = (int) $row->getData($this->getColumn()->getIndex());
        if ($rate > 5) {
            $rate = 5;
        }
        $result = '';
        for ($i = 0; $i < $rate; $i++) {
            $result .= '&#9733;';
        }
        return $result;
    }
}
