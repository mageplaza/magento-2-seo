<?php

namespace Mageplaza\Seo\Block;

class Richsnippets extends Abstractt
{
    public function getLogo()
    {
        return $this->logo->getLogoSrc();
    }

    public function getRichsnippetsHelper($code)
    {
        return $this->helperData->getRichsnippetsConfig($code);
    }

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
