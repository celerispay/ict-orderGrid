<?php
declare(strict_types=1);

/**
 * @author tjitse (Vendic)
 * Created on 17/01/2019 16:46
 */

namespace Boostsales\IctOrderGrid\Model\ResourceModel\Order\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;
use Psr\Log\LoggerInterface as Logger;
use Boostsales\IctOrderGrid\Model\Settings;

class Collection extends OriginalCollection
{
    /**
     * @var Settings
     */
    protected $settings;

    public function __construct(
        Settings $settings,
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        string $mainTable = 'sales_order_grid',
        string $resourceModel = \Magento\Sales\Model\ResourceModel\Order::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->settings = $settings;
    }

    /**
     * Copy the normal order grid collection but filter
     */
    public function _renderFiltersBefore()
    {
        $this->getSelect()->orwhere('store_id = ? or store_id = ?', 9,10);
        parent::_renderFiltersBefore();
    }

}
