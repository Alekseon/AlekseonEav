<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Block\Adminhtml\Entity\Edit\Form\Renderer\Fieldset;

use Magento\Store\Model\Store;

/**
 * Class Element
 */
class Element extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{
    /**
     * Initialize block template
     */
    protected $_template = 'Alekseon_AlekseonEav::entity/edit/form/renderer/fieldset/element.phtml'; // @codingStandardsIgnoreLine

    /**
     * Retrieve data object related with form
     *
     * @return \Magento\Catalog\Model\Product || \Magento\Catalog\Model\Category
     */
    public function getDataObject()
    {
        return $this->getElement()->getForm()->getDataObject();
    }

    /**
     * Retireve associated with element attribute object
     *
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    public function getAttribute()
    {
        return $this->getElement()->getEntityAttribute();
    }

    /**
     * Retrieve label of attribute scope
     *
     * GLOBAL | WEBSITE | STORE
     *
     * @return string
     */
    public function getScopeLabel()
    {
        $html = '';
        $attribute = $this->getAttribute();
        if ($this->_storeManager->isSingleStoreMode()) {
            return $html;
        }

        if (!$attribute) {
            $html .= __('[GLOBAL]');
            return $html;
        }

        if ($attribute->isScopeWebsite()) {
            $html .= __('[WEBSITE]');
        } elseif ($attribute->isScopeStore()) {
            $html .= __('[STORE VIEW]');
        } else {
            $html .= __('[GLOBAL]');
        }

        return $html;
    }

    /**
     * Check "Use default" checkbox display availability
     *
     * @return bool
     */
    public function canDisplayUseDefault()
    {
        if ($attribute = $this->getAttribute()) {
            if (!$attribute->isScopeGlobal() &&
                $this->getDataObject() &&
                $this->getDataObject()->getId() &&
                $this->getDataObject()->getStoreId()
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function usedDefault()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $defaultValue = $this->getDataObject()->getAttributeDefaultValue($attributeCode);
        if ($this->getElement()->getValue() != $defaultValue &&
            $this->getDataObject()->getStoreId() != Store::DEFAULT_STORE_ID
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed|string
     */
    public function getAttributeCode()
    {
        return $this->getAttribute()->getAttributeCode();
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        if ($this->getElement()->getNote()) {
            return $this->getElement()->getNote();
        }
        if ($this->getAttribute() && $this->getAttribute()->getNote()) {
            return $this->getAttribute()->getNote();
        }
        return false;
    }
}
