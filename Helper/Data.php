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
 * @package     Mageplaza_Seo
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData as CoreHelper;

/**
 * Class Data
 * @package Mageplaza\Seo\Helper
 */
class Data extends CoreHelper
{
    const CONFIG_MODULE_PATH = 'seo';

    const XML_PATH_GENERAL = 'seo/general/';
    const XML_PATH_META = 'seo/meta/';
    const XML_PATH_RICHSNIPPETS = 'seo/richsnippets/';
    const XML_PATH_HTACCESS = 'seo/htaccess/';
    const XML_PATH_ROBOTS = 'seo/robots/';
    const XML_PATH_HTML_SITEMAP = 'seo/htmlsitemap/';
    const XML_PATH_HREFLANG_TAG = 'seo/hreflang/';
    const XML_PATH_VERIFICATION = 'seo/verification/';

    /**
     * @type \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager
    )
    {
        $this->_moduleManager = $context->getModuleManager();

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GENERAL . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getMetaConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_META . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getVerficationConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_VERIFICATION . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getRichsnippetsConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RICHSNIPPETS . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getHtaccessConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HTACCESS . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getRobotsConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ROBOTS . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getHtmlsitemapConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HTML_SITEMAP . $code, $storeId);
    }

    /**
     * Info config
     * @param null $code
     * @return mixed
     */
    public function getInfoConfig($code = null)
    {
        return $this->getConfigValue('seo/info/' . $code);
    }

    /**
     * Get social shares
     * @param null $code
     * @return mixed
     */
    public function getSocialShares($code = null)
    {
        return $this->getConfigValue('seo/social_shares/' . $code);
    }

    /**
     * Get Social profiles
     * @param null $code
     * @return mixed
     */
    public function getSocialProfiles($code = null)
    {
        return $this->getConfigValue('seo/social_profiles/' . $code);
    }

    /**
     * Get duplicate content config
     * @param null $code
     * @return mixed
     */
    public function getDuplicateConfig($code = null)
    {
        return $this->getConfigValue('seo/duplicate/' . $code);
    }

    /**
     * Convert array to options
     * @access public
     * @param $options
     * @return array
     */
    public function convertOptions($options)
    {
        $converted = [];
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value'])
                && isset($option['label'])
                && !is_array($option['label'])
            ) {
                $converted[$option['value']] = $option['label'];
            }
        }

        return $converted;
    }

    /**
     * Get html sitemap
     * @return string
     */
    public function getHtmlSitemapUrl()
    {
        return $this->_getUrl('mageplaza_seo/sitemap');
    }

    /**
     * @param null $code
     * @return mixed
     */
    public function getHreflang($code = null)
    {
        return $this->getConfigValue(self::XML_PATH_HREFLANG_TAG . $code);
    }

    /**
     * Check module active
     * @param $moduleName
     * @return bool
     */
    public function checkModuleActive($moduleName)
    {
        return $this->_moduleManager->isOutputEnabled($moduleName);
    }

    /**
     * Create structure data script
     *
     * @param $data
     * @param string $prefixComment
     * @param string $subfixComment
     * @return string
     */
    public function createStructuredData($data, $prefixComment = '', $subfixComment = '')
    {
        $applicationLdJson = $prefixComment;
        $applicationLdJson .= '<script type="application/ld+json">' . json_encode($data, JSON_PRETTY_PRINT) . '</script>';
        $applicationLdJson .= $subfixComment;

        return $applicationLdJson;
    }
}
