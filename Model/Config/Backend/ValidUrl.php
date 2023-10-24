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
 * @package     Mageplaza_Webhook
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\ValidatorException;

/**
 * Class ValidUrl
 * @package Mageplaza\Seo\Model\Config\Backend
 */
class ValidUrl extends Value
{
    /**
     * @return Value|void
     * @throws ValidatorException
     */
    public function beforeSave()
    {
        if (!empty($this->getValue())) {
            $valueArray = array_map('trim', explode(
                "\n",
                $this->getValue()
                ?? ''));
            foreach ($valueArray as $value) {
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    throw new ValidatorException(__('Invalid url format.'));
                }
            }
        }
        parent::beforeSave();
    }
}
