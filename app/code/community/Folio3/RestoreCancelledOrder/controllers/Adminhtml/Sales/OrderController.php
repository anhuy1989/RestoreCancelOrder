<?php

/**
 * Filename: OrderController.php.
 * Author: Muhammad Shahab Hameed
 * Date: 10/10/2016
 */
class Folio3_RestoreCancelledOrder_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Controller_Action {

	public function restoreorderAction() {
		$orderId  = $this->getRequest()->getParam( 'order_id' );
		$restore = Mage::getModel( 'Folio3_RestoreCancelledOrder_Model_Restore' );

		if ( $restore->restoreOrder( $orderId ) ) {
			$this->_getSession()->addSuccess( $this->__( 'Order was successfully restored.' ) );
		} else {
			$this->_getSession()->addError( $this->__( 'Order was not restored.' ) );
		}

		$this->_redirect( '*/sales_order/view', array( 'order_id' => $orderId ) );
	}


	public function massRestoreOrderAction() {
		$orderIds              = $this->getRequest()->getPost( 'order_ids', array() );
		$countRestoreOrder    = 0;
		$countNonRestoreOrder = 0;
		$restore              = Mage::getModel( 'Folio3_RestoreCancelledOrder_Model_Restore' );

		foreach ( $orderIds as $orderId ) {
			if ( $restore->restoreOrder( $orderId ) ) {
				$countRestoreOrder ++;
			} else {
				$countNonRestoreOrder ++;
			}
		}
		if ( $countNonRestoreOrder ) {
			if ( $countRestoreOrder ) {
				$this->_getSession()->addError( $this->__( '%s order(s) cannot be restored', $countNonRestoreOrder ) );
			} else {
				$this->_getSession()->addError( $this->__( 'The order(s) cannot be restored' ) );
			}
		}
		if ( $countRestoreOrder ) {
			$this->_getSession()->addSuccess( $this->__( '%s order(s) have been restored.', $countRestoreOrder ) );
		}
		$this->_redirect( '*/*/' );
	}

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('system/config');
	}
}