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
    public function getAllOptions()
    {
        $result = array();
        foreach ($this->toOptionArray() as $k => $v) {
            $result[] = array(
                'value' => $v,
                'label' => $v,
            );
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            'INDEX,FOLLOW',
            'NOINDEX,FOLLOW',
            'INDEX,NOFOLLOW',
            'NOINDEX,NOFOLLOW'
        );
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
