<?php
/**
 * Helper functions, often wrapping some functionality burried in a class.
 */
 
 /**
  * What is the active sitewide sale id?
  */
 function swsales_active_sitewide_sale_id() {
     $options = \Sitewide_Sales\includes\classes\SWSales_Settings::get_options();
     return $options['active_sitewide_sale_id'];
 }
 
 /**
  * What is the template for this landing page?
  * Returns false if we're not even on a landing page.
  */
 function swsales_landing_page_template( $post_id = null ) {
     // Default to queried object.
     if ( empty( $post_id ) ) {
         $post_id = get_queried_object_id();
     }

     // Return false if no post.
     if ( empty( $post_id ) ) {
         return false;
     }
     
     // See if any Sitewide Sale CPTs have this post ID set as the Landing Page.
     $sitewide_sale_id = get_post_meta( $post_id, 'swsales_sitewide_sale_id', true );

     // Return false if not a landing page.
     if ( empty( $sitewide_sale_id ) ) {
         return false;
     }

     // Get the landing page template for the specific sale.
     $template = get_post_meta( $sitewide_sale_id, 'swsales_landing_page_template', true );
     return $template;
 }

 /**
  * What is the template for the active sitewide sale banner?
  * Returns false if there is no sale or banner.
  */
 function swsales_banner_template( ) {

    // Get the active sitewide sale or the sale being previewed.
    if ( current_user_can( 'administrator' ) && isset( $_REQUEST['swsales_preview_sale_banner'] ) ) {
        $sitewide_sale_id = intval( $_REQUEST['swsales_preview_sale_banner'] );
    } else {
        $sitewide_sale_id = swsales_active_sitewide_sale_id();
    }

     // Return false if no sale.
     if ( empty( $sitewide_sale_id ) ) {
         return false;
     }
     
     // Get the banner template for the specific sale.
     $template = get_post_meta( $sitewide_sale_id, 'swsales_banner_template', true );
     return $template;
 }

/**
 * Gets the coupon for a given sitewide sale.
 * Filterable by modules using "swsales_coupon" filter.
 *
 * @param int|SWSales_Sitewide_Sale $sitewide_sale to get coupon for. Defaults to active.
 * @return string
 */
function swsales_coupon( $sitewide_sale = null ) {
	if ( 'Sitewide_Sales\includes\classes\SWSales_Sitewide_Sale' !== get_class( $sitewide_sale ) ) {
		$sitewide_sale_id = swsales_active_sitewide_sale_id();
		if ( is_numeric( $sitewide_sale ) ) {
			$sitewide_sale_id = intval( $sitewide_sale );
		} elseif ( current_user_can( 'administrator' ) && isset( $_REQUEST['swsales_preview_sale_banner'] ) ) {
			$sitewide_sale_id = intval( $_REQUEST['swsales_preview_sale_banner'] );
		}
		$sitewide_sale = \Sitewide_Sales\includes\classes\SWSales_Sitewide_Sale::get_sitewide_sale( $sitewide_sale_id );
	}

	// Return null if no sale.
	if ( empty( $sitewide_sale ) ) {
		// Note when this happens we don't run the filter in $sitewide_sale->get_coupon().
		return null;
	}

	return $sitewide_sale->get_coupon();
}
