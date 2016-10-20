<?php

namespace Mageplaza\Seo\Block;

class Richsnippets extends AbstractSeo
{
    /**
     * get logo
     * @return string
     */
    public function getLogo()
    {
        return $this->logo->getLogoSrc();
    }

    /**
     * get rich snippet config
     * @param $code
     *
     * @return mixed
     */
    public function getRichsnippetsHelper($code)
    {
        return $this->helperData->getRichsnippetsConfig($code);
    }

    /**
     * get profiles
     * @return mixed
     */
    public function getProfiles()
    {
        $config   = $this->helperData;
        $profiles = trim($config->getRichsnippetsConfig('social_profiles'));
        $lines    = '';
        if ( ! empty($profiles)) {
            $profiles = explode("\n", $profiles);
            foreach ($profiles as $_profile) {
                $lines .= '"' . trim($_profile) . '",';
            }
        }

        return rtrim($lines,',');

    }
}
