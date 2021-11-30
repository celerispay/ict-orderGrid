<?php
/**
 * @author tjitse (Vendic)
 * Created on 18/01/2019 09:01
 */

namespace Boostsales\IctOrderGrid\Plugin\Model\Menu;

use Boostsales\IctOrderGrid\Model\Settings;

class ItemPlugin
{
    /**
     * @var Settings
     */
    protected $settings;

    public function __construct(
        Settings $settings
    ) {
        $this->settings = $settings;
    }

    public function beforeGetTitle(\Magento\Backend\Model\Menu\Item $subject)
    {
        $id = $subject->getId();
        if ($id === 'Boostsales_IctOrderGrid::sales_order_by_invoice') {
            if ($gridTitle = $this->settings->getGridName()) {
                $subject->setTitle($gridTitle);
            }
        }
    }
}
