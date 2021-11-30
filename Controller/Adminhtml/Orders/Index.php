<?php
declare(strict_types=1);

/**
 * @author tjitse (Vendic)
 * Created on 16/01/2019 18:13
 */

namespace Boostsales\IctOrderGrid\Controller\Adminhtml\Orders;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;
use Boostsales\IctOrderGrid\Model\Settings;

class Index extends Action
{
    protected $resultRedirectFactory = false;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Settings
     */
    protected $settings;

    public function __construct(
        Settings $settings,
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->settings = $settings;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend($this->getTitle());

        return $resultPage;
    }
    /**
     * @return string
     */
    protected function getTitle()
    {
        $title = $this->settings->getGridName();
        if (!$title) {
            return __('Operations');
        }
        return $title;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Boostsales_IctOrderGrid::orders_resource');
    }
}
