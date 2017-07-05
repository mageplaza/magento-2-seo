<?php
namespace Mageplaza\Seo\Model\Source;

class Robots extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    const DEFAULT_ROBOTS = 'INDEX,FOLLOW';

    protected $_eavAttrEntity;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $eavAttrEntity
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $eavAttrEntity
    ) {
    
        $this->_eavAttrEntity = $eavAttrEntity;
    }

    /**
     * @return array
     */
//    public function getAllOptions()
//    {
//        $result = array();
//        foreach ($this->toOptionArray() as $k => $v) {
//            $result[] = array(
//                'value' => $v,
//                'label' => $v,
//            );
//        }
//
//        return $result;
//    }
//
//    /**
//     * @return array
//     */
//    public function toOptionArray()
//    {
//        return array(
//            'INDEX,FOLLOW',
//            'NOINDEX,FOLLOW',
//            'INDEX,NOFOLLOW',
//            'NOINDEX,NOFOLLOW'
//        );
//    }


    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('INDEX,FOLLOW'), 'value' => 'INDEX,FOLLOW'],
                ['label' => __('NOINDEX,FOLLOW'), 'value' => 'NOINDEX,FOLLOW'],
                ['label' => __('INDEX,NOFOLLOW'), 'value' => 'INDEX,NOFOLLOW'],
                ['label' => __('NOINDEX,NOFOLLOW'), 'value' => 'NOINDEX,NOFOLLOW'],
            ];
        }

        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }

        return $_options;
    }


    /**
     * Get a text for option value
     *
     * @param string|int $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }

        return false;
    }
}
