<?php
namespace Mageplaza\DemoTutorial\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AreaOptions implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'frontend', 'label' => __('Frontend')],
            ['value' => 'adminhtml', 'label' => __('Backend')],
        ];
    }
}
