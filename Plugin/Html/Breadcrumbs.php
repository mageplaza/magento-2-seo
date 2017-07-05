<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza
 * @copyright   Copyright (c) 2016 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Plugin\Html;

use Magento\Framework\App;
use Magento\Framework\Registry;

/**
 * Class Breadcrumbs
 * @package Mageplaza\Seo\Plugin\Html
 */
class Breadcrumbs
{
    /**
     * @type \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * List of available breadcrumb properties
     *
     * @var string[]
     */
    protected $_properties = ['label', 'title', 'link', 'first', 'last', 'readonly'];

    protected $_logger;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Registry $coreRegistry
     */
    function __construct(
        Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger
    ) {
    
        $this->_coreRegistry = $coreRegistry;
        $this->_logger       = $logger;
    }

    /**
     * Before add Crumb - Registry BreadCrumbs
     *
     * @param \Magento\Theme\Block\Html\Breadcrumbs $subject
     * @param string $crumbName
     * @param array $crumbInfo
     * @return array
     */
    public function beforeAddCrumb(\Magento\Theme\Block\Html\Breadcrumbs $subject, $crumbName, $crumbInfo)
    {
        $crumbs = $this->_coreRegistry->registry('crumbs');
        if (!isset($crumbs)) {
            $crumbs = [];
            $this->_coreRegistry->register('crumbs', $crumbs);
        }
        foreach ($this->_properties as $key) {
            if (!isset($crumbInfo[$key])) {
                $crumbInfo[$key] = null;
            }
        }

        if (!isset($crumbs[$crumbName]) || !$crumbs[$crumbName]['readonly']) {
            $crumbs[$crumbName] = $crumbInfo;
        }

        $this->_coreRegistry->unregister('crumbs');
        $this->_coreRegistry->register('crumbs', $crumbs);

        return [$crumbName, $crumbInfo];
    }
}
