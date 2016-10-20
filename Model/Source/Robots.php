<?php
namespace Mageplaza\Seo\Model\Source;

class Robots
{

    const DEFAULT_ROBOTS = 'INDEX,FOLLOW';

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
}
