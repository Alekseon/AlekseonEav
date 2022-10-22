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
            $defaultValue = $defaultValueProvider->getValue();
            if ($defaultValue) {
                return $defaultValue;
            }
        }

        return $proceed();
    }

    /**
     * @param AttributeInterface $attribute
     */
    public function beforeGetBackendModels(AttributeInterface $attribute)
    {
        $defaultValueProvider = $this->defaultValueProviderRepository->getAttributeDefaultValueProvider($attribute);
        if ($defaultValueProvider) {
            $attribute->addBackendModel('default_value_provider', $defaultValueProvider->getBackendModel());
        }
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

    /**
     * @param AttributeInterface $attribute
     * @param $inputParamsConfig
     * @return array|mixed
     */
    public function afterGetInputParamsConfig(AttributeInterface $attribute, $inputParamsConfig)
    {
        $defaultValueProvider = $this->defaultValueProviderRepository->getAttributeDefaultValueProvider($attribute);
        if ($defaultValueProvider) {
            $defaultValueInputParams = $defaultValueProvider->getInputParams() ?? [];
            $inputParamsConfig = array_merge($inputParamsConfig, $defaultValueInputParams);
        }
        return $inputParamsConfig;
    }
}
