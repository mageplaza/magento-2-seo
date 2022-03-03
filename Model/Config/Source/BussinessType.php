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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class BussinessType
 * @package Mageplaza\Seo\Model\Config\Source
 */
class BussinessType implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('--Please Select--'), 'value' => 'Store'],
            ['label' => __('LocalBussiness'), 'value' => 'LocalBussiness'],
            ['label' => __('AutoPartsStore'), 'value' => 'AutoPartsStore'],
            ['label' => __('BikeStore'), 'value' => 'BikeStore'],
            ['label' => __('BookStore'), 'value' => 'BookStore'],
            ['label' => __('ClothingStore'), 'value' => 'ClothingStore'],
            ['label' => __('ComputerStore'), 'value' => 'ComputerStore'],
            ['label' => __('ConvenienceStore'), 'value' => 'ConvenienceStore'],
            ['label' => __('DepartmentStore'), 'value' => 'DepartmentStore'],
            ['label' => __('ElectronicsStore'), 'value' => 'ElectronicsStore'],
            ['label' => __('Florist'), 'value' => 'Florist'],
            ['label' => __('FurnitureStore'), 'value' => 'FurnitureStore'],
            ['label' => __('GardenStore'), 'value' => 'GardenStore'],
            ['label' => __('GroceryStore'), 'value' => 'GroceryStore'],
            ['label' => __('HardwareStore'), 'value' => 'HardwareStore'],
            ['label' => __('HobbyShop'), 'value' => 'HobbyShop'],
            ['label' => __('HomeGoodsStore'), 'value' => 'HomeGoodsStore'],
            ['label' => __('JewelryStore'), 'value' => 'JewelryStore'],
            ['label' => __('LiquorStore'), 'value' => 'LiquorStore'],
            ['label' => __('MensClothingStore'), 'value' => 'MensClothingStore'],
            ['label' => __('MobilePhoneStore'), 'value' => 'MobilePhoneStore'],
            ['label' => __('MovieRentalStore'), 'value' => 'MovieRentalStore'],
            ['label' => __('MusicStoreMusicStore'), 'value' => 'MusicStore'],
            ['label' => __('OfficeEquipmentStore'), 'value' => 'OfficeEquipmentStore'],
            ['label' => __('OutletStore'), 'value' => 'OutletStore'],
            ['label' => __('PawnShop'), 'value' => 'PawnShop'],
            ['label' => __('PetStore'), 'value' => 'PetStore'],
            ['label' => __('ShoeStore'), 'value' => 'ShoeStore'],
            ['label' => __('SportingGoodsStore'), 'value' => 'SportingGoodsStore'],
            ['label' => __('TireShop'), 'value' => 'TireShop'],
            ['label' => __('ToyStore'), 'value' => 'ToyStore'],
            ['label' => __('WholesaleStore'), 'value' => 'WholesaleStore'],
        ];
    }
}
