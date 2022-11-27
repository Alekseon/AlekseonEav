<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model;

use \Alekseon\AlekseonEav\Api\Data\AttributeInterface;
use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\Scopes;
use Alekseon\AlekseonEav\Model\Attribute\Backend\AbstractBackend;
use Alekseon\AlekseonEav\Model\Attribute\InputType\AbstractInputType;

/**
 * Class Attribute
 * @package Alekseon\AlekseonEav\Model
 */
abstract class Attribute extends \Magento\Framework\Model\AbstractModel implements AttributeInterface
{
    /**
     * @var
     */
    private $backendTable;
    /**
     * @var Attribute\InputTypeRepository
     */
    private $inputTypeRepository;
    /**
     * @var Attribute\InputValidatorRepository
     */
    protected $inputValidatorRepository;
    /**
     * @var
     */
    private $inputTypeModel;
    /**
     * @var
     */
    private $backendModels = [];
    /**
     * @var array
     */
    private $assignedBackendModelCodes = [];
    /**
     * @var
     */
    private $metadataFormModel;
    /**
     * @var bool
     */
    protected $canUseGroup = false;
    /**
     * @var bool
     */
    protected $isGroupEditable = true;
    /**
     * @var
     */
    protected $inputValidators;
    /**
     * @var
     */
    protected $inputParamsConfig;
    /**
     * @var string
     */
    protected $_eventObject = 'attribute';

    /**
     * Attribute constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Attribute\InputTypeRepository $inputTypeRepository
     * @param ResourceModel\Attribute $resource
     * @param ResourceModel\Attribute\Collection $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Alekseon\AlekseonEav\Model\Attribute\InputTypeRepository $inputTypeRepository,
        \Alekseon\AlekseonEav\Model\Attribute\InputValidatorRepository $inputValidatorRepository,
        \Alekseon\AlekseonEav\Model\ResourceModel\Attribute $resource,
        \Alekseon\AlekseonEav\Model\ResourceModel\Attribute\Collection $resourceCollection
    ) {
        $this->inputTypeRepository = $inputTypeRepository;
        $this->inputValidatorRepository = $inputValidatorRepository;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    /**
     * @return mixed
     */
    public function getAttributeId()
    {
        return $this->getId();
    }

    /**
     * @return AbstractInputType
     */
    public function getInputTypeModel()
    {
        if (is_null($this->inputTypeModel)) {
            $this->inputTypeModel = $this->inputTypeRepository->getInputTypeModelByAttribute($this);
        }
        return $this->inputTypeModel;
    }

    /**
     * @return bool|mixed
     */
    public function getFrontendInputTypeConfig()
    {
        return $this->inputTypeRepository->getFrontendInputTypeConfigByAttribute($this);
    }

    /**
     * Get attribute backend table name
     *
     * @return string
     */
    public function getBackendTable()
    {
        if ($this->backendTable === null) {
            $entityTable = [$this->getResource()->getBackendTablePrefix(), $this->getBackendType()];
            $backendTable = $this->getResource()->getTable($entityTable);
            $this->backendTable = $backendTable;
        }

        return $this->backendTable;
    }

    /**
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getScope() == Scopes::SCOPE_GLOBAL;
    }

    /**
     * @return bool
     */
    public function isScopeWebsite()
    {
        return $this->getScope() == Scopes::SCOPE_WEBSITE;

    }

    /**
     * Retrieve attribute is store scope flag
     *
     * @return bool
     */
    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }

    /**
     * @return mixed
     */
    public function usesSource()
    {
        return $this->getInputTypeModel()->usesSource();
    }

    /**
     * @return bool
     */
    public function getSourceModel()
    {
        if ($this->getInputTypeModel()->usesSource()) {
            $sourceModel = $this->getInputTypeModel()->getSourceModel();
            $sourceModel->setAttribute($this);
            if ($this->getResource()->getCurrentStore()) {
                $storeId = $this->getResource()->getCurrentStore()->getId();
                $sourceModel->setStoreId($storeId);
            }
            return $sourceModel;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getBackendModels()
    {
        $this->addInputTypeBackendModel();
        return $this->backendModels;
    }

    /**
     * @return $this
     */
    protected function addInputTypeBackendModel()
    {
        $inputTypeBackendModel = $this->getInputTypeModel()->getBackendModel();
        $this->addBackendModel('input_type_backedn_model', $inputTypeBackendModel);
        return $this;
    }

    /**
     * @param string $backendModelCode
     * @param $backendModel
     * @return bool
     */
    public function addBackendModel(string $backendModelCode, $backendModel)
    {
        if (!isset($this->assignedBackendModelCodes[$backendModelCode])) {
            $this->assignedBackendModelCodes[$backendModelCode] = $backendModelCode;
            if ($backendModel instanceof AbstractBackend) {
                $backendModel->setAttribute($this);
                $this->backendModels[] = $backendModel;
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function canDisplayInGrid()
    {
        return $this->getInputTypeModel()->canDisplayInGrid();
    }

    /**
     * @param $object
     * @return string
     */
    public function getObjectValueAsText($object)
    {
        $inputTypeModel = $this->getInputTypeModel();
        $value = $object->getData($this->getAttributeCode());
        return $inputTypeModel->getValueAsText($value, $object->getStoreId());
    }

    /**
     * @return mixed
     */
    public function getFrontendLabels()
    {
        if ($this->getData('frontend_labels') === null) {
            $this->setFrontendLabels($this->getResource()->getFrontendLabels($this, false));
        }
        return $this->getData('frontend_labels');
    }

    /**
     * @param null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFrontendLabel($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->getResource()->getCurrentStore()->getId();
        }

        if ($frontendLabels = $this->getFrontendLabels()) {
            if (isset($frontendLabels[$storeId])) {
                return $frontendLabels[$storeId];
            }
        }

        return $this->getDefaultFrontendLabel();
    }

    /**
     * @return mixed
     */
    public function getDefaultFrontendLabel()
    {
        return $this->getData('frontend_label');
    }

    /**
     * @param $object
     * @param $attribute
     */
    public function isAttributeValueUpdated($object)
    {
        $attributeCode = $this->getAttributeCode();
        $oldValue = $object->getOrigData($attributeCode);
        $newValue = $object->getData($attributeCode);

        if ($object->isObjectNew()) {
            return true;
        }

        if ($object->hasData($attributeCode) && ($newValue !== $oldValue || $newValue === null)) {
            return true;
        }

        $backendModels = $this->getBackendModels();
        foreach ($backendModels as $backendModel) {
            if ($backendModel->isAttributeValueUpdated($object)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getMetadataFormModel()
    {
        if ($this->metadataFormModel === null) {
            $metadataFormModel = $this->getInputTypeModel()->getMetadataFormModel();
            if ($metadataFormModel) {
                $metadataFormModel->setAttribute($this);
            }
            $this->metadataFormModel = $metadataFormModel;
        }
        return $this->metadataFormModel;
    }

    /**
     * @param $metadataFormModel
     * @return $this
     */
    public function setMetadataFormModel($metadataFormModel)
    {
        $this->metadataFormModel = $metadataFormModel;
        $metadataFormModel->setAttribute($this);
        return $this;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return mixed
     */
    public function extractValueFromRequest(\Magento\Framework\App\RequestInterface $request, $paramName = null)
    {
        $metadataFormModel = $this->getMetadataFormModel($request);
        if ($metadataFormModel) {
            return $metadataFormModel->extractValue($request, $paramName);
        } else {
            if ($paramName === null) {
                $paramName = $this->getAttributeCode();
            }
            return $request->getParam($paramName);
        }
    }

    /**
     * @param $optionsId
     */
    public function getOptionCode($optionId)
    {
        if (!$this->getHasOptionCodes()) {
            return false;
        }

        return $this->getInputTypeModel()->getOptionCode($optionId);
    }

    /**
     * @return bool
     */
    public function isAttributeCodeEditable()
    {
       if (!$this->getId()) {
           return true;
       }

       if ($this->getIsUserDefined()) {
           return true;
       }

       return false;
    }

    /**
     * @return mixed
     */
    public function getCanUseGroup()
    {
        return $this->canUseGroup;
    }

    /**
     * @return mixed
     */
    public function getIsGroupEditable()
    {
        return $this->isGroupEditable;
    }

    /**
     * @return mixed
     */
    public function getInputValidators()
    {
        if ($this->inputValidators == null) {
            $this->inputValidators = [];

            $validator = $this->inputValidatorRepository->getAttributeValidator($this);
            if ($validator) {
                $validator->setAttribute($this);
                $this->inputValidators[$validator->getCode()] = $validator;
            }
        }

        return $this->inputValidators;
    }

    /**
     * @param $key
     * @param $value
     * @return Attribute
     */
    public function setAttributeExtraParam($key, $value)
    {
        $params = $this->getData('attribute_extra_params');
        if (!$params || !is_array($params)) {
            $params = [];
        }
        $params[$key] = $value;
        return $this->setData('attribute_extra_params', $params);
    }

    /**
     * @param null $key
     * @return array|mixed|null
     */
    public function getAttributeExtraParam($key = null)
    {
        $params = $this->getData('attribute_extra_params');
        if (!$params) {
            $params = [];
        }
        if ($key) {
            return $params[$key] ?? null;
        }
        return $params;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->getInputTypeModel()->getDefaultValue();
    }

    /**
     * @return bool
     */
    public function hasDefaultValue()
    {
        if ($this->getDefaultValue()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $inputParamsToSave
     * @return $this
     */
    protected function setInputParams($inputParamsToSave = [])
    {
        $currentParams = $this->getAttributeExtraParam('input_params');
        $inputParams = [];
        $params = $this->getInputParamsConfig();
        if ($params) {
            foreach ($params as $paramCode => $paramConfig) {
                if (isset($inputParamsToSave[$paramCode])) {
                    $currentParams[$paramCode] = $inputParamsToSave[$paramCode];
                }

                if (isset($currentParams[$paramCode]) && $currentParams[$paramCode]) {
                    $inputParams[$paramCode] = $currentParams[$paramCode];
                }
            }
        }

        $this->setAttributeExtraParam('input_params', $inputParams);

        return $this;
    }

    /**
     * @return array
     */
    public function getInputParamsConfig()
    {
        if (is_null($this->inputParamsConfig)) {
            $inputTypeConfig = $this->getFrontendInputTypeConfig();
            $this->inputParamsConfig = $inputTypeConfig->getInputParams() ?? [];
            $inputValidators = $this->getInputValidators();

            foreach ($inputValidators as $validator) {
                $validatorParams = $validator->getInputParams() ?? [];
                $this->inputParamsConfig = array_merge($this->inputParamsConfig, $validatorParams);
            }
        }

        return $this->inputParamsConfig;
    }

    /**
     * @param $paramCode
     * @return false|mixed
     */
    public function getInputParam($paramCode)
    {
        $currentParams = $this->getAttributeExtraParam('input_params');
        if (isset($currentParams[$paramCode])) {
            $inputParamsConfig = $this->getInputParamsConfig();
            if (isset($inputParamsConfig[$paramCode])) {
                return $currentParams[$paramCode];
            }
        }

        return '';
    }
}
