<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Block\Adminhtml\Attribute;

/**
 * Class Grid
 * @package Alekseon\AlekseonEav\Block\Adminhtml\Attribute
 */
abstract class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\Scopes
     */
    private $scopesSource;
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    private $yesNoSource;
    /**
     * @var \Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\InputType
     */
    private $inputTypeSource;
    /**
     * @var
     */
    protected $collectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Config\Model\Config\Source\Yesno $yesNoSource
     * @param \Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\Scopes $scopesSource
     * @param \Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\InputType $inputTypeSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Config\Model\Config\Source\Yesno $yesNoSource,
        \Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\Scopes $scopesSource,
        \Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\InputType $inputTypeSource,
        array $data = []
    ) {
        $this->scopesSource = $scopesSource;
        $this->yesNoSource = $yesNoSource;
        $this->inputTypeSource = $inputTypeSource;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct() // @codingStandardsIgnoreLine
    {
        parent::_construct();
        $this->setId('attribute_grid');
        $this->setDefaultSort('attribute_code');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare product attributes grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns() // @codingStandardsIgnoreLine
    {
        parent::_prepareColumns();

        $this->addColumn(
            'attribute_code',
            [
                'header' => __('Attribute Code'),
                'index' => 'attribute_code',
            ]
        );

        $this->addColumn(
            'frontend_label',
            [
                'header' => __('Frontend Label'),
                'index' => 'frontend_label',
            ]
        );

        $this->addColumn(
            'frontend_input',
            [
                'header' => __('Input Type'),
                'index' => 'frontend_input',
                'type' => 'options',
                'options' => $this->inputTypeSource->getOptionArray()
            ]
        );

        $this->addColumn(
            'scope',
            [
                'header' => __('Scope'),
                'index' => 'scope',
                'type' => 'options',
                'options' => $this->scopesSource->getOptionArray()
            ]
        );

        $this->addColumn(
            'visible_in_grid',
            [
                'header' => __('Visible In Grid'),
                'index' => 'visible_in_grid',
                'type' => 'options',
                'options' => $this->yesNoSource->toArray()
            ]
        );

        $this->addColumn(
            'sort_order',
            [
                'header' => __('Sort Order'),
                'index' => 'sort_order',
                'type' => 'number',
            ]
        );

        return $this;
    }

    /**
     * Return url of given row
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['attribute_code' => $row->getAttributeCode()]);
    }
}
