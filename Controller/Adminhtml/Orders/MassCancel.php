<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Boostsales\IctOrderGrid\Controller\Adminhtml\Orders;

use Magento\Sales\Model\Order;
use Magento\Framework\Controller\ResultFactory;

class MassCancel extends \Magento\Backend\App\Action
{

    protected $orderData;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Order $orderData
    ) {
        $this->orderData = $orderData;
        parent::__construct($context);
    }


    public function execute()
    {
        try {
            $orderIds = $this->getRequest()->getPost('selected', array());
            $countCancelOrder = 0;
            $countNonCancelOrder = 0;
            foreach ($orderIds as $orderId) {
                $order = $this->orderData->load($orderId);
                if ($order->canCancel()) {
                    $order->cancel()
                        ->save();
                    $countCancelOrder++;
                } else {
                    $countNonCancelOrder++;
                }
            }
            if ($countNonCancelOrder) {
                if ($countCancelOrder) {
                    $this->messageManager->addError(__('%1 order(s) cannot be canceled', $countNonCancelOrder));
                } else {
                    $this->messageManager->addError(__('The order(s) cannot be canceled'));
                }
            }
            if ($countCancelOrder) {
                $this->messageManager->addSuccess(__('%1 order(s) have been canceled.', $countCancelOrder));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create([ResultFactory::TYPE_REDIRECT]);
            return $resultRedirect->setPath('*/*/');
        }
        $resultRedirect = $this->resultRedirectFactory->create([ResultFactory::TYPE_REDIRECT]);
        return $resultRedirect->setPath('*/*/');
    }
}
