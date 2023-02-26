<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Block\Adminhtml\Entity\Grid\Renderer;

/**
 * Class Multiselect
 * @package Alekseon\AlekseonEav\Block\Adminhtml\Entity\Grid\Renderer
 */
class Multiselect extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $data = (string) $row->getData($this->getColumn()->getIndex());
        $attribute = $row->getAttribute($this->getColumn()->getIndex());
        $oprtonsSource = $attribute->getSourceModel();
        $options = $oprtonsSource->getOptions();

        $selectedOptions = explode(',', $data);
        $result = '<ul>';

        foreach ($selectedOptions as $optionId) {
            if (isset($options[$optionId])) {
                $result .= '<li>' . $options[$optionId] . '</li>';
            }
        }

        $result .= '</ul>';

        return $result;
    }
}
