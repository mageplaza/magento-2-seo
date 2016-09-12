<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Adminhtml Save Robot block
 */
namespace Mageplaza\Seo\Block\Adminhtml\System\Config;

class Savehtaccess extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_saveButtonLabel = 'Save .htaccess';

    public function setVatButtonLabel($saveButtonLabel)
    {
        $this->_saveButtonLabel = $saveButtonLabel;

        return $this;
    }

    /**
     * Set template to itself
     *
     * @return \Magento\Customer\Block\Adminhtml\System\Config\Validatevat
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ( ! $this->getTemplate()) {
            $this->setTemplate('system/config/savehtaccess.phtml');
        }

        return $this;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel  = ! empty($originalData['button_label']) ? $originalData['button_label']
            : $this->_saveButtonLabel;
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'html_id'      => $element->getHtmlId(),
                'ajax_url'     => $this->_urlBuilder->getUrl('seo/system_config_htaccess/save'),
            ]
        );

        return $this->_toHtml();
    }
}
