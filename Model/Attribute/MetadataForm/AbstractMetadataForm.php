<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\MetadataForm;

/**
 * Class Date
 * @package Alekseon\Eav\Model\Attribute\MetadataForm
 */
abstract class AbstractMetadataForm
{
    /**
     * @var
     */
    private $attribute;

    /**
     * @param $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @param $attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function extractValue(\Magento\Framework\App\RequestInterface $request, $paramName = null)
    {
        if ($paramName === null) {
            $paramName = $this->getAttribute()->getAttributeCode();
        }
        $value = $request->getParam($paramName);
        return $value;
    }
}
