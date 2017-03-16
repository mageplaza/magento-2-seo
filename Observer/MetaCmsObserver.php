<?php

namespace Mageplaza\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Mageplaza\Seo\Helper\Data as SeoHelper;
use Magento\Framework\Registry;
use Mageplaza\Seo\Model\Source\Robots;

class MetaCmsObserver implements ObserverInterface
{
    protected $helper;
    protected $registry;
    protected $robots;

    public function __construct(
        SeoHelper $helper,
        Registry $registry,
        Robots $robots
    ) {
        $this->helper   = $helper;
        $this->registry = $registry;
        $this->robots   = $robots;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $currentCms = $this->registry->registry('cms_page');
        $form       = $observer->getEvent()->getForm();
        $fieldset   = $form->addFieldset(
            'seo_meta_fieldset',
            ['legend' => __('Mageplaza SEO'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'meta_robots',
            'select',
            [
                'name'   => 'meta_robots',
                'label'  => __('Meta Robots'),
                'title'  => __('Meta Robots'),
                'values' => $this->robots->getAllOptions(),
                'class'  => 'select'
            ]
        );

        return $this;
    }


}