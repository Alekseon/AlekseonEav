<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model;

use \Alekseon\AlekseonEav\Api\Data\AttributeInterface;
use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\Scopes;

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
     * @var
     */
    private $inputTypeModel;
    /**
     * @var
     */
    private $backendModel;

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
        \Alekseon\AlekseonEav\Model\ResourceModel\Attribute $resource,
        \Alekseon\AlekseonEav\Model\ResourceModel\Attribute\Collection $resourceCollection
    ) {
        $this->inputTypeRepository = $inputTypeRepository;
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

    public function getInputTypeModel()
    {
        if (is_null($this->inputTypeModel)) {
            $this->inputTypeModel = $this->inputTypeRepository->getInputTypeModelByAttribute($this);
        }
        return $this->inputTypeModel;
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
            return $sourceModel;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getBackendModel()
    {
        if ($this->backendModel === null) {
            $backendModel = $this->getInputTypeModel()->getBackendModel();
            if ($backendModel) {
                $backendModel->setAttribute($this);
            }
            $this->backendModel = $backendModel;
        }
        return $this->backendModel;
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
}
