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
 * Class BusinessType
 * @package Mageplaza\Seo\Model\Config\Source
 */
class BusinessType implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Store'), 'value' => 'Store'],
            ['label' => __('Local Business'), 'value' => 'LocalBusiness'],
            ['label' => __('Auto Parts Store'), 'value' => 'AutoPartsStore'],
            ['label' => __('Bike Store'), 'value' => 'BikeStore'],
            ['label' => __('Book Store'), 'value' => 'BookStore'],
            ['label' => __('Clothing Store'), 'value' => 'ClothingStore'],
            ['label' => __('Computer Store'), 'value' => 'ComputerStore'],
            ['label' => __('Convenience Store'), 'value' => 'ConvenienceStore'],
            ['label' => __('Department Store'), 'value' => 'DepartmentStore'],
            ['label' => __('Electronics Store'), 'value' => 'ElectronicsStore'],
            ['label' => __('Florist Store'), 'value' => 'Florist'],
            ['label' => __('Furniture Store'), 'value' => 'FurnitureStore'],
            ['label' => __('Garden Store'), 'value' => 'GardenStore'],
            ['label' => __('Grocery Store'), 'value' => 'GroceryStore'],
            ['label' => __('Hardware Store'), 'value' => 'HardwareStore'],
            ['label' => __('Hobby Shop'), 'value' => 'HobbyShop'],
            ['label' => __('Home Goods Store'), 'value' => 'HomeGoodsStore'],
            ['label' => __('Jewelry Store'), 'value' => 'JewelryStore'],
            ['label' => __('Liquor Store'), 'value' => 'LiquorStore'],
            ['label' => __('Mens Clothing Store'), 'value' => 'MensClothingStore'],
            ['label' => __('Mobile Phone Store'), 'value' => 'MobilePhoneStore'],
            ['label' => __('Movie Rental Store'), 'value' => 'MovieRentalStore'],
            ['label' => __('Music Store'), 'value' => 'MusicStore'],
            ['label' => __('Office Equipment Store'), 'value' => 'OfficeEquipmentStore'],
            ['label' => __('Outlet Store'), 'value' => 'OutletStore'],
            ['label' => __('Pawn Shop'), 'value' => 'PawnShop'],
            ['label' => __('Pet Store'), 'value' => 'PetStore'],
            ['label' => __('Shoe Store'), 'value' => 'ShoeStore'],
            ['label' => __('Sporting Goods Store'), 'value' => 'SportingGoodsStore'],
            ['label' => __('Tire Shop'), 'value' => 'TireShop'],
            ['label' => __('Toy Store'), 'value' => 'ToyStore'],
            ['label' => __('Wholesale Store'), 'value' => 'WholesaleStore'],
        ];
    }
}
