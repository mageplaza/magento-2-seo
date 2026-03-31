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
 * @package     Mageplaza_Demo
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DemoTutorial\Controller\Adminhtml\Steps;

use Mageplaza\DemoTutorial\Controller\Adminhtml\Step;

/**
 * Class NewAction
 * @package Mageplaza\Demo\Controller\Adminhtml\Step
 */
class NewAction extends Step
{
    /**
     * Forward to edit form
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
