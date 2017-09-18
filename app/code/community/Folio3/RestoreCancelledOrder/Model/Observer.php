<?php

/**
 * Filename: Observer.php.
 * Author: Muhammad Shahab Hameed
 * Date: 10/10/2016
 */
class Folio3_RestoreCancelledOrder_Model_Observer extends Mage_Core_Model_Abstract {

	public function massRestoreOrder( Varien_Event_Observer $observer ) {

		$block = $observer->getEvent()->getBlock();

		if ( get_class( $block ) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction'
		     && $block->getRequest()->getControllerName() == 'sales_order'
		) {

			$block->addItem( 'restore', array(
				'label' => 'Restore Order',
				'url'   => $this->getRestoreUrl(),
			) );
		}
	}

	public function getRestoreUrl() {
		return Mage::getUrl( '*/*/massrestoreorder' );
	}

	public function saveOrderStatus( Varien_Event_Observer $observer ) {

		$order  = $observer->getOrder();

		// only save value in the database
		if (isset($order) && $order->getStatus() != Mage_Sales_Model_Order::STATE_CANCELED) {

			$model = Mage::getModel( 'folio3_restorecancelledorder/status' )->load($order->getId(), 'folio3_restoreorder_order_id');
			$model->setData( 'folio3_restoreorder_order_id', $order->getId() );
			$model->setData( 'folio3_restoreorder_status_code', $order->getStatus() );
			try {
				if ( $model->getData( 'folio3_restoreorder_created_time' ) == null || $model->getData( 'folio3_restoreorder_modified_time' ) == null ) {
					$model->setData( 'folio3_restoreorder_created_time', now() )
					      ->setData( 'folio3_restoreorder_modified_time', now() );
				} else {
					$model->setData( 'folio3_restoreorder_modified_time', now() );
				}
				$model->save();

			} catch ( Exception $ex ) {
				Mage::log( "Observer Cancel Order : " . $ex->getMessage() );
			}
		}
	}
}