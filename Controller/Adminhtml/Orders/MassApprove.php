<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Boostsales\IctOrderGrid\Controller\Adminhtml\Orders;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Transliterator;

class MassApprove extends \Magento\Backend\App\Action
{

    protected $orderData;
    protected $invoiceService;
    protected $transaction;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Order $orderData,
        InvoiceService $invoiceService,
        Transaction $transaction,
	InvoiceSender $invoiceSender
    ) {
        $this->orderData = $orderData;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        parent::__construct($context);
    }


    public function execute()
    {
        try {
            $orderIds = $this->getRequest()->getPost('selected', array());
            $countInvoiceOrder = 0;
            $countNonInvoiceOrder = 0;

            foreach ($orderIds as $orderId) {
                $order = $this->orderData->load($orderId);
                if ($order->canInvoice()) {
                    $invoice = $this->invoiceService->prepareInvoice($order);
                    $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
                    $invoice->register();
                    $invoice->getOrder()->setCustomerNoteNotify(false);
                    $invoice->getOrder()->setIsInProcess(true);
                    $this->transaction->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();
                    $order->save();
		    $this->invoiceSender->send($invoice);
                    $countInvoiceOrder++;
                } else {
                    $countNonInvoiceOrder++;
                }
            }
            if ($countNonInvoiceOrder) {
                if ($countInvoiceOrder) {
                    $this->messageManager->addError(__('%1 order(s) were not approved.', $countNonInvoiceOrder));
                } else {
                    $this->messageManager->addError(__('No order(s) were approved.'));
                }
            }
            if ($countInvoiceOrder) {
                $this->messageManager->addSuccess(__('%1 order(s) have been approved.', $countInvoiceOrder));
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
