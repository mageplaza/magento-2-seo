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
class Target extends OptionArray
{
    const BLACK = '_blank';
    const SELF = '_self';
    const PARENT = '_parent';
    const TOP = '_top';

    /**
     * @return array
     */
    public function getOptionHash()
    {
        return [
            self::BLACK => __('New window or tab'),
            self::SELF => __('Current tab'),
            self::PARENT => __('Parent frame'),
            self::TOP => __('Full body of the window')
        ];
    }
}
