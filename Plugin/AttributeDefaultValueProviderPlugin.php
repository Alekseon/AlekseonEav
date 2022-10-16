<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Plugin;

use Alekseon\AlekseonEav\Api\Data\AttributeInterface;
use Alekseon\AlekseonEav\Model\Attribute\DefaultValueProviderRepository;

/**
 * Class AttributeDefaultValueProviderPlugin
 * @package Alekseon\AlekseonEav\Plugin
 */
class AttributeDefaultValueProviderPlugin
{
    /**
     * AttributeDefaultValueProviderPlugin constructor.
     * @param DefaultValueProviderRepository $defaultValueProviderRepository
     */
    public function __construct(
        DefaultValueProviderRepository $defaultValueProviderRepository
    )
    {
        $this->defaultValueProviderRepository = $defaultValueProviderRepository;
    }

    /**
     * @param AttributeInterface $attribute
     * @param callable $proceed
     * @return mixed
     */
    public function aroundGetDefaultValue(AttributeInterface $attribute, callable $proceed)
    {
        $defaultValueProvider = $this->defaultValueProviderRepository->getAttributeDefaultValueProvider($attribute);
        if ($defaultValueProvider) {
            return $defaultValueProvider->getValue();
        } else {
            return $proceed();
        }
    }

    /**
     * @param AttributeInterface $attribute
     * @param $backendModel
     * @return mixed
     */
    public function afterGetBackendModel(AttributeInterface $attribute, $backendModel)
    {
        if (!$backendModel) {
            $defaultValueProvider = $this->defaultValueProviderRepository->getAttributeDefaultValueProvider($attribute);
            if ($defaultValueProvider && $defaultValueProvider->getBackendModel()) {
                $backendModel = $defaultValueProvider->getBackendModel();
            }
        }

        return $backendModel;
    }

    /**
     * @param AttributeInterface $attribute
     * @param $hasDefaultValue
     * @return mixed
     */
    public function afterHasDefaultValue(AttributeInterface $attribute, $hasDefaultValue)
    {
        if (!$hasDefaultValue) {
            $defaultValueProvider = $this->defaultValueProviderRepository->getAttributeDefaultValueProvider($attribute);
            $hasDefaultValue = $defaultValueProvider->hasValue();
        }

        return $hasDefaultValue;
    }
}
