<?php

namespace Mageplaza\Seo\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


class InstallData implements InstallDataInterface
{

    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }


    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->_addRobotsAttribute($setup);

        $setup->endSetup();
    }

    /**
     * add robots attribute to Product, Category
     * @param $setup
     */
    protected function _addRobotsAttribute($setup){
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'mp_meta_robots',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Meta Robots',
                'note'=> 'Added by Mageplaza.com',
                'input' => 'select',
                'class' => '',
                'source' => '\Mageplaza\Seo\Model\Source\Robots',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'group' => 'Search Engine Optimization',
                'sort_order' => 100,
                'apply_to' => '',
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'mp_meta_robots',
            [
                'type' => 'varchar',
                'label' => 'Meta Robots',
                'note'=> 'Added by Mageplaza.com',
                'input' => 'select',
                'sort_order' => 41,
                'source' => '\Mageplaza\Seo\Model\Source\Robots',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'group' => 'General Information',
                'backend' => ''
            ]
        );
    }

}