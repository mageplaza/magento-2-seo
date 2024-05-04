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
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Mageplaza\Core\Helper\AbstractData as CoreHelper;
use Mageplaza\Seo\Helper\Data;

/**
 * Class UpdateDateTimeValue
 * @package Mageplaza\Seo\Setup\Patch\Data
 */
class UpdateDateTimeValue implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var CoreHelper
     */
    private $_helperData;

    /**
     * InstallAttributeData constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Data $helperData
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Data $helperData
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->_helperData     = $helperData;
    }

    /**
     * @return void
     */
    public function apply()
    {
        $setup = $this->moduleDataSetup;

        $path    = Data::CONFIG_MODULE_PATH . '/richsnippets/price_valid_until_custom';
        $table   = $setup->getTable('core_config_data');
        $adapter = $setup->getConnection();
        $select  = $adapter->select()
            ->from($table, 'value')
            ->where('path = :path_custom');
        $binds   = ['path_custom' => $path];

        if (!$adapter->fetchOne($select, $binds)) {
            $data  = [
                'scope'    => 'default',
                'scope_id' => 0,
                'path'     => $path,
                'value'    => 'NULL',
            ];
            $setup->getConnection()->insertOnDuplicate($setup->getTable('core_config_data'), $data, ['value']);
        }
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function revert()
    {
        return [];
    }
}
