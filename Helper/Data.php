<?php
namespace Mageplaza\Seo\Helper;

use Mageplaza\Core\Helper\AbstractData as CoreHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends CoreHelper
{
    const XML_PATH_GENERAL = 'seo/general/';
    const XML_PATH_META = 'seo/meta/';
    const XML_PATH_RICHSNIPPETS = 'seo/richsnippets/';
    const XML_PATH_HTACCESS = 'seo/htaccess/';
    const XML_PATH_ROBOTS = 'seo/robots/';
    const XML_PATH_HTML_SITEMAP = 'seo/htmlsitemap/';
    protected $objectManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
        $this->_storeManager = $storeManager;

        parent::__construct($context, $objectManager, $storeManager);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GENERAL . $code, $storeId);
    }

    public function getMetaConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_META . $code, $storeId);
    }

    public function getRichsnippetsConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RICHSNIPPETS . $code, $storeId);
    }

    public function getHtaccessConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HTACCESS . $code, $storeId);
    }

    public function getRobotsConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ROBOTS . $code, $storeId);
    }

    public function getHtmlsitemapConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HTML_SITEMAP . $code, $storeId);
    }

    /**
     * convert array to options
     *
     * @access public
     *
     * @param $options
     *
     * @return array
     * @author Sam
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && ! is_array($option['value'])
                && isset($option['label'])
                && ! is_array($option['label'])
            ) {
                $converted[$option['value']] = $option['label'];
            }
        }

        return $converted;
    }

    public function getHtmlSitemapUrl()
    {
        $this->_getUrl('mageplaza_seo/sitemap');
    }
}
