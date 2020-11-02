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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Helper;

use Magento\Theme\Block\Html\Header\Logo;
use Mageplaza\Core\Helper\AbstractData as CoreHelper;

/**
 * Class Data
 * @package Mageplaza\Seo\Helper
 */
class Data extends CoreHelper
{
    const CONFIG_MODULE_PATH = 'seo';

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getMetaConfig($code, $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/meta' . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getVerficationConfig($code, $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/verification' . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getRichsnippetsConfig($code, $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/richsnippets' . $code, $storeId);
    }

    /**
     * @param null $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getInfoConfig($code = null, $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/info' . $code, $storeId);
    }

    /**
     * @param null $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSocialProfiles($code = null, $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/social_profiles' . $code, $storeId);
    }

    /**
     * @param null $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDuplicateConfig($code = null, $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/duplicate' . $code, $storeId);
    }

    /**
     * Create structure data script
     *
     * @param $data
     * @param string $prefixComment
     * @param string $subfixComment
     *
     * @return string
     */
    public function createStructuredData($data, $prefixComment = '', $subfixComment = '')
    {
        $applicationLdJson = $prefixComment;
        $applicationLdJson .= '<script type="application/ld+json">' . json_encode(
            $data,
            JSON_PRETTY_PRINT
        ) . '</script>';
        $applicationLdJson .= $subfixComment;

        return $applicationLdJson;
    }

    /**
     * get Logo image url
     *
     * @return string
     */
    public function getLogo()
    {
        $logo = $this->objectManager->get(Logo::class);

        return $logo->getLogoSrc();
    }
}
