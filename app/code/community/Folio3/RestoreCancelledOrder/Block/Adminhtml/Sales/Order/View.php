<?php

/**
 * Filename: View.php.
 * Author: Muhammad Shahab Hameed
 * Date: 10/10/2016
 */
class Folio3_RestoreCancelledOrder_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View {

	public function __construct() {

		parent::__construct();

		$order = $this->getOrder();

		if ( $order->isCanceled() ) {
			$this->_addButton( 'restore', array(
				'label'   => __( 'Restore Order' ),
				'onclick' => 'deleteConfirm(\'' . __( 'Do you really want to restore this cancelled order?' ) . '\', \'' . $this->getRestoreOrderUrl() . '\')',
				'class'   => 'go'
			), 0, 100, 'header', 'header' );
		}
	}

	private function getRestoreOrderUrl() {
		return $this->getUrl( '*/*/restoreorder' );
	}

}
