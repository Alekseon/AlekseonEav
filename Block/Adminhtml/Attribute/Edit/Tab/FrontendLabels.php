<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Class FrontendLabels
 * @package Alekseon\AlekseonEav\Block\Adminhtml\Attribute\Edit\Tab
 */
class FrontendLabels extends Generic
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var
     */
    private $attribute;

    /**
     * FrontendLabels constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return mixed
     */
    public function getAttributeObject()
    {
        if (null === $this->attribute) {
            return $this->_coreRegistry->registry('current_attribute');
        }
        return $this->attribute;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm() // @codingStandardsIgnoreLine
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );

        $webistes = $this->storeManager->getWebsites();
        foreach($webistes as $website) {
            foreach($website->getGroups() as $group) {
                $groupFieldset = $form->addFieldset('group_' . $group->getId() . '_fieldset', ['legend' => $website->getName() . ' / ' . $group->getName()]);

                foreach($group->getStores() as $store) {
                    $groupFieldset->addField(
                        'frontend_labels_' . $store->getId(),
                        'text',
                        [
                            'name' => 'frontend_labels[' . $store->getId() . ']',
                            'label' => $store->getName(),
                            'title' => $store->getName(),
                        ]
                    );
                }
            }
        }

        $frontendLabels = [];
        $attribute = $this->getAttributeObject();
        foreach ($attribute->getFrontendLabels() as $storeId => $label) {
            $frontendLabels['frontend_labels_' . $storeId] = $label;
        }
        $form->setValues($frontendLabels);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
