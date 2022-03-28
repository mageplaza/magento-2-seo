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
            ['label' => __('Animal Shelter'), 'value' => 'AnimalShelter'],
            ['label' => __('Archive Organization'), 'value' => 'ArchiveOrganization'],
            ['label' => __('Automotive Business'), 'value' => 'AutomotiveBusiness'],
            ['label' => __('Child Care'), 'value' => 'ChildCare'],
            ['label' => __('Dentist'), 'value' => 'Dentist'],
            ['label' => __('Dry Cleaning Or Laundry'), 'value' => 'DryCleaningOrLaundry'],
            ['label' => __('Emergency Service'), 'value' => 'EmergencyService'],
            ['label' => __('Employment Agency'), 'value' => 'EmploymentAgency'],
            ['label' => __('Entertainment Business'), 'value' => 'EntertainmentBusiness'],
            ['label' => __('Financial Service'), 'value' => 'FinancialService'],
            ['label' => __('Food Establishment'), 'value' => 'FoodEstablishment'],
            ['label' => __('Government Office'), 'value' => 'Government Office'],
            ['label' => __('Health And Beauty Business'), 'value' => 'HealthAndBeautyBusiness'],
            ['label' => __('Home And Construction Business'), 'value' => 'HomeAndConstructionBusiness'],
            ['label' => __('Internet Cafe'), 'value' => 'InternetCafe'],
            ['label' => __('Legal Service'), 'value' => 'Legal Service'],
            ['label' => __('Library'), 'value' => 'Library'],
            ['label' => __('Lodging Business'), 'value' => 'LodgingBusiness'],
            ['label' => __('Medical Business'), 'value' => 'MedicalBusiness'],
            ['label' => __('Professional Service'), 'value' => 'ProfessionalService'],
            ['label' => __('Radio Station'), 'value' => 'RadioStation'],
            ['label' => __('Real Estate Agent'), 'value' => 'RealEstateAgent'],
            ['label' => __('Recycling Center'), 'value' => 'RecyclingCenter'],
            ['label' => __('Self Storage'), 'value' => 'SelfStorage'],
            ['label' => __('Shopping Center'), 'value' => 'ShoppingCenter'],
            ['label' => __('Sports Activity Location'), 'value' => 'SportsActivityLocation'],
            ['label' => __('Television Station'), 'value' => 'TelevisionStation'],
            ['label' => __('Tourist Information Center'), 'value' => 'TouristInformationCenter'],
            ['label' => __('Travel Agency'), 'value' => 'TravelAgency'],
        ];
    }
}
