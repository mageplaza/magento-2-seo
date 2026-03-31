<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Seo
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

use Magento\Framework\Component\ComponentRegistrar;

$moduleMap = [
    'Mageplaza_Seo'  => __DIR__,
    'Mageplaza_DemoTutorial' => __DIR__ . '/DemoTutorial',
];

if (!defined('BP')) {
    \define('BP', \dirname(__DIR__, 3));
}
if (!defined('VENDOR_PATH')) {
    \define('VENDOR_PATH', BP . '/app/etc/vendor_path.php');
}
$vendorDir      = require VENDOR_PATH;
$vendorAutoload = BP . "/{$vendorDir}/autoload.php";
$loader         = require $vendorAutoload;

foreach ($moduleMap as $namespace => $path) {
    ComponentRegistrar::register(ComponentRegistrar::MODULE, $namespace, $path);
    $loader->setPsr4(str_replace('_', '\\', $namespace) . '\\', [$path]);
}
