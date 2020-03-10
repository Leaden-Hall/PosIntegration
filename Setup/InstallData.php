<?php

namespace Petswonderland\PosIntegration\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Exception;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;

/**
 * Class InstallData
 * @package Petswonderland\PosIntegration\Setup
 */
class InstallData implements InstallDataInterface
{
    const POS_FLAG = 'customer_pos_sync';

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig       = $eavConfig;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws LocalizedException
     * @throws Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Customer::ENTITY,
            self::POS_FLAG,
            [
                'type' => 'int',
                'label' => 'Customer POS Sync Flag',
                'input' => 'boolean',
                'default' => 1,
                'position' => 999,
                'required' => false,
                'adminhtml_only' => false,
                'visible'      => false,
                'user_defined' => false,
                'system'       => 0,
            ]
        );
        $customerPosSync = $this->eavConfig->getAttribute(Customer::ENTITY, self::POS_FLAG);
        $customerPosSync->save();
    }
}
