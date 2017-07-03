<?php
namespace Inchoo\SetupTest\Setup;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;

class Uninstall implements UninstallInterface
{
	/**
	 * @type \Magento\Eav\Setup\EavSetupFactory
	 */
	protected $eavSetupFactory;

	/**
	 * Constructor
	 *
	 * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
	 */
	function __construct(
		EavSetupFactory $eavSetupFactory
	)
	{
		$this->eavSetupFactory = $eavSetupFactory;
	}

	/**
	 * Uninstall
	 *
	 * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
	 * @param \Magento\Framework\Setup\ModuleContextInterface $context
	 */
	public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();

		/** @var \Magento\Eav\Setup\EavSetup $eavSetup */
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

		$eavSetup->removeAttribute(
			Product::ENTITY,
			'mp_meta_robots');
		$eavSetup->removeAttribute(
			Category::ENTITY,
			'mp_meta_robots');
		$setup->getConnection()->dropColumn($setup->getTable('cms_page'), 'mp_meta_robots');
		$setup->getConnection()->dropColumn($setup->getTable('store'), 'mp_lang');

		$setup->endSetup();
	}
}