<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_DemoTutorial
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DemoTutorial\Model\Source;

/**
 * Class Type
 * @package Mageplaza\DemoTutorial\Model
 */
class Type extends OptionArray
{
    const FRONTEND = 'frontend';
    const BACKEND = 'backend';

    /**
     * @return array
     */
    public function getOptionHash()
    {
        return [
            self::FRONTEND => __('Frontend'),
            self::BACKEND => __('Backend'),
        ];
    }
}
