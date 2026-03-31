<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\DemoTutorial\Helper;

use Exception;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Data\Form\Element\Text;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Module\PackageInfoFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface as FormatPrice;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Block\Adminhtml\System\Config\Docs;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\Demo\Helper\Data as HelperDemo;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Mageplaza\Demo\Model\GuideFactory;
use Magento\Customer\Model\Session;

class Data extends HelperDemo
{
    /**
     * @var AdminSession
     */
    protected $adminSession;

    /**
     * @var Session
     */
    protected $customerSession;


    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        DeploymentConfig $deploymentConfig,
        ComponentRegistrarInterface $componentRegistrar,
        ReadFactory $readFactory,
        StoreManagerInterface $storeManager,
        UrlInterface $backendUrlManager,
        PackageInfoFactory $packageInfoFactory,
        FormatPrice $priceFormat,
        AbstractData $coreHelper,
        GuideFactory $guideFactory,
        Docs $docs,
        Text $text,
        AdminSession $adminSession,
        Session $customerSession
    )
    {
        $this->adminSession    = $adminSession;
        $this->customerSession = $customerSession;
        parent::__construct($context, $objectManager, $deploymentConfig, $componentRegistrar, $readFactory, $storeManager, $backendUrlManager, $packageInfoFactory, $priceFormat, $coreHelper, $guideFactory, $docs, $text);
    }

    public function isCustomerLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    public function isAdminLoggedIn(): bool
    {
        return $this->adminSession->isLoggedIn();
    }
    /**
     * Get extension info from JSON file based on current SKU
     *
     * @return array
     */
    public function getContentJson(): array
    {
        try {
            $fileData = file_get_contents(BP . '/extension_info.json');
        } catch (Exception $e) {
            $this->_logger->error($e->getMessage());

            return [];
        }
        $extensionInfo = json_decode($fileData);
        $extensionSku  = $this->getConfigGeneral('sku');
        $data          = [];

        foreach ($extensionInfo as $item) {
            $array_items = (array) $item;
            if ($array_items['sku'] === $extensionSku) {
                $data['name']             = $array_items['name'];
                $data['module']           = $array_items['module'];
                $data['image']            = $array_items['image'];
                $data['live_url']         = $array_items['live_url'];
                $array_price              = (array) $array_items['price'];
                $data['price_community']  = '';
                $data['price_enterprise'] = '';
                foreach ($array_price as $eddition => $price) {
                    if ($eddition === 'ce') {
                        $data['price_community'] = $price;
                    }
                    if ($eddition === 'ee') {
                        $data['price_enterprise'] = $price;
                    }
                }
                $data['discount_price'] = (array) $array_items['final_price'];
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getContentTutorialJson(): array
    {
        $file = BP . '/tutorial_extension_info.json';

        if (!is_readable($file)) {
            $this->_logger->error(__('Cannot read %1', $file));
            return [];
        }

        $data = json_decode(file_get_contents($file), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $this->_logger->error('JSON decode error: ' . json_last_error_msg());
            return [];
        }

        return $data;
    }
}
