<?php

class Folio3_RestoreCancelledOrder_Model_Restore extends Mage_Core_Model_Abstract {

	public function restoreOrder( $Id ) {

		$order = Mage::getModel( 'sales/order' )->load( $Id );

		if ( $order->getId() && $order->getStatus() == Mage_Sales_Model_Order::STATE_CANCELED ) {

			// if the value is stored in database, then that will be preferred else default value.
			$statusCommentConfigValue          = Mage::getStoreConfig( 'sales/restore_order/status_comment' );
			$isCommentVisibleConfigValue       = Mage::getStoreConfig( 'sales/restore_order/is_comment_visible' );
			$notifyCustomerConfigValue         = Mage::getStoreConfig( 'sales/restore_order/notify_customer' );
			$previousStatusDatabaseConfigValue = Mage::getStoreConfig( 'sales/restore_order/previous_status_database' );
			$restoreStatusConfigValue          = Mage::getStoreConfig( 'sales/restore_order/restore_status' );

			$state  = Mage_Sales_Model_Order::STATE_NEW; // default value
			$status = 'pending'; // default value

			// checking if the previous status from the database needs to be restored
			if ( isset( $previousStatusDatabaseConfigValue ) && $previousStatusDatabaseConfigValue ) {

				// load database previous status and state
				$previousValue = Mage::getModel( 'folio3_restorecancelledorder/status' )->load( $order->getId(), 'folio3_restoreorder_order_id' );
				if ( isset( $previousValue ) && $previousValue != null && ! empty( $previousValue->getData( 'folio3_restoreorder_status_code' ) ) ) {
					$status = $previousValue->getData( 'folio3_restoreorder_status_code' );
				}

				// checking if the status value to be restored is set up manually.
			} else if ( isset( $previousStatusDatabaseConfigValue ) && ! $previousStatusDatabaseConfigValue && isset( $restoreStatusConfigValue ) ) {

				// load from manually configured status
				$orderStatus = Mage::getResourceModel( 'sales/order_status_collection' )
				                   ->joinStates()
				                   ->addFieldToFilter( 'main_table.status', $restoreStatusConfigValue )
				                   ->getFirstItem();
				$state       = $orderStatus->getState();
				$status      = $restoreStatusConfigValue;
			}

			try {

				// restoring  all the items inside a given order.
				foreach ( $order->getItemsCollection() as $item ) {
					$item->setQtyCanceled( 0 );
					$item->setTaxCanceled( 0 );
					$item->setHiddenTaxCanceled( 0 );
					$item->save();
				}

				$user     = Mage::getSingleton( 'admin/session' );
				$username = $user->getUser()->getUsername();
				$comment  = "Order restored by $username.";

				// restoring the order itself.
				$order->setBaseDiscountCanceled( 0 )
				      ->setBaseShippingCanceled( 0 )
				      ->setBaseSubtotalCanceled( 0 )
				      ->setBaseTaxCanceled( 0 )
				      ->setBaseTotalCanceled( 0 )
				      ->setDiscountCanceled( 0 )
				      ->setShippingCanceled( 0 )
				      ->setSubtotalCanceled( 0 )
				      ->setTaxCanceled( 0 )
				      ->setTotalCanceled( 0 )
				      ->setState( $state )
				      ->setStatus( $status )
				      ->save();

				// The order is now saved, doing post order save work.
				if ( isset( $statusCommentConfigValue ) && $statusCommentConfigValue ) {

					// whether to add a comment about restoration of order or not and if it should be visible on the front end.
					$historyItem = $order->addStatusHistoryComment( $comment );

					if ( isset( $isCommentVisibleConfigValue ) && $isCommentVisibleConfigValue ) {
						$historyItem->setIsVisibleOnFront( true );
					} else {
						$historyItem->setIsVisibleOnFront( false );
					}

					// whether to notify customer or not about the restoration of order
					if ( isset( $notifyCustomerConfigValue ) && $notifyCustomerConfigValue ) {
						$historyItem->setIsCustomerNotified( true );
					} else {
						$historyItem->setIsCustomerNotified( false );
					}

					$historyItem->save();
				}

				return true;

			} catch ( Exception $ex ) {
				Mage::log( 'Order was not restored. ' . $ex->getMessage() );

				return false;
			}
		}

		return false;
	}
}
