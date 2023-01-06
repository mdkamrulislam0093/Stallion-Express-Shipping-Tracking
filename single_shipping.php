<?php 
//Register Meta box
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'wpdocs-id', 'Shipment Dimention', 'wpdocs_field_cb', 'shop_order', 'side' );
});
 
//Meta callback function
function wpdocs_field_cb( $post ) {
    $stallion_weight = get_post_meta( $post->ID, 'stallion-weight', true );
    $stallion_length = get_post_meta( $post->ID, 'stallion-length', true );
    $stallion_width = get_post_meta( $post->ID, 'stallion-width', true );
    $stallion_height = get_post_meta( $post->ID, 'stallion-height', true );
    $stallion_size_unit = get_post_meta( $post->ID, 'stallion-side-unit', true );
    $stallion_postage = get_post_meta( $post->ID, 'stallion_postage', true );
    ?>


    <p>
    	<label fo="stallion-side-unit" style="font-weight: 700; margin-bottom: 6px; display: block; font-size: 15px; text-transform: capitalize;">Size Unit</label>
    	<select class="stallion-size-unit" name="stallion-size-unit">
    		<option value="0">Select Size Unit</option>
    		<option value="cm" <?php if ($stallion_size_unit == 'cm') echo ' selected="selected"'; ?>>cm</option>
    		<option value="in"  <?php if ($stallion_size_unit == 'in') echo ' selected="selected"'; ?>>in</option>
    	</select>
    </p>

    <p>
    	<label fo="stallion-weight" style="font-weight: 700; margin-bottom: 6px; display: block; font-size: 15px; text-transform: capitalize;">weight</label>
    	<input type="text" id="stallion-weight" name="stallion-weight" placeholder="0.6" value="<?php echo esc_attr( $stallion_weight ) ?>">
    </p>

    <p>
    	<label for="stallion-length" style="font-weight: 700; margin-bottom: 6px; display: block; font-size: 15px; text-transform: capitalize;">length</label>
    	<input type="text" id="stallion-length" name="stallion-length" placeholder="9" value="<?php echo esc_attr( $stallion_length ) ?>">
    </p>

    <p>
    	<label for="stallion-width" style="font-weight: 700; margin-bottom: 6px; display: block; font-size: 15px; text-transform: capitalize;">width</label>
    	<input type="text" id="stallion-width" name="stallion-width" placeholder="12" value="<?php echo esc_attr( $stallion_width ) ?>">
    </p>

    <p>
    	<label for="stallion-height" style="font-weight: 700; margin-bottom: 6px; display: block; font-size: 15px; text-transform: capitalize;">height</label>
    	<input type="text" id="stallion-height" name="stallion-height" placeholder="1" value="<?php echo esc_attr( $stallion_height ) ?>">
    </p>

    <?php if ( !empty($stallion_postage) ): ?>
	    <div id="stallion_postage_exist">
	    	<label for="stallion_postage">
	    		<input type="checkbox" name="stallion_postage" checked><?php echo preg_replace("/[\s_]/", " ", ucwords($stallion_postage)); ?>
	    	</label>
	    </div>
    <?php endif; ?>

    <div id="stallion_postage"></div>

    <button class="stallion_postage_btn button button-primary">Submit</button>

  <style type="text/css">
.lds-ring {
  display: inline-block;
  position: relative;
  width: 40px;
  height: 40px;
}
.lds-ring div {
  box-sizing: border-box;
  display: block;
  position: absolute;
  width: 30px;
  height: 30px;
  margin: 0;
  border: 3px solid #000;
  border-radius: 50%;
  animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  border-color: #000 transparent transparent transparent;
}
.lds-ring div:nth-child(1) {
  animation-delay: -0.45s;
}
.lds-ring div:nth-child(2) {
  animation-delay: -0.3s;
}
.lds-ring div:nth-child(3) {
  animation-delay: -0.15s;
}
div#stallion_postage label {
    display: block;
    margin-bottom: 11px;
}
.stallion_postage_btn {
	display: none !important;
}
.stallion_postage_btn.active {
	display: block !important;
}
.stallion_postage_btn:focus {
    background: #10446a !important;
}
@keyframes lds-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
    
  </style>
    <script type="text/javascript">
    	jQuery(document).ready(function($){
    		$('#stallion-height').blur(function(){
    			$('#stallion_postage').html('<div class="lds-ring"><div></div><div></div><div></div><div></div></div>');
    			$('#stallion_postage_exist').hide();

    			var weight = $('#stallion-weight').val();
    			var length = $('#stallion-length').val();
    			var width = $('#stallion-width').val();
    			var height = $('#stallion-height').val();

	        $.post(
	          '<?= admin_url('admin-ajax.php'); ?>',
	          {
	            action: 'stallion_express_postage',
	            nonce: '<?= wp_create_nonce('stallion_express_postage'); ?>',
	            weight: weight,
	            length: length,
	            width: width,
	            height: height,
	            order_id: '<?= $post->ID ?>',
	          },
	          function (result){
	          	var postage_obj = JSON.parse(result);

	          	if ( postage_obj != '' ) {
	          		var stext = '<h3>Please Select Shipping Postage</h3>';
	// usps_first_class_mail, usps_priority_mail, usps_priority_mail_express, usps_parcel_select_ground, usps_media_mail, usps_library_mail, fedex, ups_standard, stallion_express_domestic, usps_priority_mail_express_international, usps_priority_mail_international, usps_first_class_mail_international, postnl_international_packet, postnl_international_packet_tracked, apc_priority_worldwide, apc_priority_worldwide_tracked, apc_priority_worldwide_tracked_ddp_, ups_expedited, ups_express_saver, ups_worldwide_express_saver
	// str_replace(' ', '_', $string);
		          	jQuery.each(postage_obj, function(index, item){
		          		var postge = item.postage_type.toLowerCase();
		          		var postage_val = postge.replace(/ /g,"_");

		          		if ( postage_val == 'fedex_express_saver' ) {
		          			postage_val = 'fedex';
		          		}

		            	stext += '<label for="'+ postage_val +'"><input type="radio" id="'+ postage_val +'" name="stallion_express_postage" value="'+ postage_val +'">'+ item.postage_type +' : $'+ item.total +'</label>';

		            	$('#stallion_postage').html(stext);
		            	$('.stallion_postage_btn').addClass('active');
		          	});

	          	}

	          	// console.log(postage_obj);

	          }
	        ); 

    		});

    		$('.stallion_postage_btn').click(function(e){
                $('.stallion_postage_btn').after('<div class="lds-ring"><div></div><div></div><div></div><div></div></div>');

    			e.preventDefault();

		        $.post(
		          '<?= admin_url('admin-ajax.php'); ?>',
		          {
		            action: 'stallion_express_create_shipment',
		            nonce: '<?= wp_create_nonce('stallion_express_create_shipment'); ?>',
		            weight: $('#stallion-weight').val(),
		            length: $('#stallion-length').val(),
		            width: $('#stallion-width').val(),
		            height: $('#stallion-height').val(),
		            order_id: '<?= $post->ID ?>',
		           	stallion_postage: $('#stallion_postage input[name="stallion_express_postage"]:checked').val(), 
		           	size_unit: $('.stallion-size-unit').val()       
		          },
		          function (result){
                    $('.lds-ring').hide();

		        	if ( result == 'Success' ) {
		        		location.reload();
		        	} else {
                        $('.stallion_postage_btn').after('<div>'+ result +'</div>');
                    }
					console.log(result);
		          }
		        );   

    		});
    	});
    </script>

    <?php
}


add_action( 'wp_ajax_stallion_express_create_shipment', 'stallion_express_create_shipment_callback' );
add_action( 'wp_ajax_nopriv_stallion_express_create_shipment', 'stallion_express_create_shipment_callback' );

function stallion_express_create_shipment_callback(){
	check_ajax_referer( 'stallion_express_create_shipment', 'nonce' );


	$stallion_weight = $_REQUEST['weight'];
	$stallion_length = $_REQUEST['length'];
	$stallion_width = $_REQUEST['width'];
	$stallion_height = $_REQUEST['height'];
	$stallion_postage = $_REQUEST['stallion_postage'];
	$size_unit = $_REQUEST['size_unit'];
	$order_id = $_REQUEST['order_id'];

   	$order = wc_get_order( $_REQUEST['order_id'] );
    $order_data = $order->get_data();
    $order_items = $order->get_items();


    $weight_unit = get_option('woocommerce_weight_unit');
    $dimension_unit = get_option('woocommerce_dimension_unit');
    $currency = get_option('woocommerce_currency');

    // $i = 0;

    $order_item_count = 0;
    $order_content = [];
    $order_name = '';

    foreach ($order_items as $order_it) {

        if ( $order_item_count == 0 ) {
            $order_name = $order_it->get_name();

            $order_content[] = [
               'description' => $order_it->get_name(),
               'quantity' => $order_it->get_quantity(),
               'hs_code' => '620800',
               'weight' => $stallion_weight,
               'weight_unit' => 'lbs',
               'value' => 10,
               'currency' => $currency,
               'country_of_origin' => 'CN',
            ];
            $order_item_count++;
        }
    }
 

    $order_details = [
        'name' => sprintf("%s %s", $order_data['shipping']['first_name'], $order_data['shipping']['last_name']),
        'address1' => $order_data['shipping']['address_1'],
        'address2' => $order_data['shipping']['address_2'],
        'city' => $order_data['shipping']['city'],
        'province_code' => $order_data['shipping']['state'],
        'postal_code' => $order_data['shipping']['postcode'],
        'country_code' => $order_data['shipping']['country'],
        'order_id' => $_REQUEST['order_id'],
        'weight_unit' => 'lbs',
        'weight' => $stallion_weight,
        'length' => $stallion_length,
        'width' => $stallion_width,
        'height' => $stallion_height,
        'size_unit' => $size_unit,
        'package_contents' => $order_name,
        'value' => 10,
        'currency' => $currency,
        'package_type' => 'parcel',
        'signature_confirmation' => false,
        'postage_type' => $stallion_postage,
        'label_format' => 'pdf',
        'purchase_label' => true,
        'is_fba' => false,
        'insured' => true,
        'customs_lines' => $order_content,
        'region' => 'ON',
    ];

	if ( !empty($stallion_weight) && !empty($stallion_length) && !empty($stallion_width) && !empty($stallion_height) && !empty($size_unit) ) {

	    $get_stallion_shipments = wp_remote_post(
	      'https://ship.stallionexpress.ca/api/v3/shipments', [
	        'timeout'     => 45,
	        'headers' => [
	          'accept' => 'application/json',
	          'Authorization' => 'Bearer m2yUOpdOLuDPaIJC8oBvtURm2RKKxrdC6rJTkojcCPYW2q7PhEgIWnb1JLVh',
	          'Content-Type' => 'application/json'
	        ],
	        'body' => json_encode($order_details)
	      ]
	    );
	    
	    // error_log('Quantity : ' . print_r($item->get_quantity(), true));

	    if( is_wp_error( $get_stallion_shipments ) ){
	        error_log('Failed access Token: '. print_r($get_stallion_shipments, true));

	    } else {
	        $stallion_shipments_body = json_decode( wp_remote_retrieve_body( $get_stallion_shipments ), true );
	        
	        error_log('stallion_body' . print_r($stallion_shipments_body, true));

	        if ( $stallion_shipments_body['success'] == 1 ) {
	            update_post_meta( $order_id, 'stallion_tracking_code', $stallion_shipments_body['tracking_code'] );
				update_post_meta( $order_id, 'stallion-weight', $stallion_weight );
			 	update_post_meta( $order_id, 'stallion-length', $stallion_length );
				update_post_meta( $order_id, 'stallion-width', $stallion_width );
				update_post_meta( $order_id, 'stallion-height', $stallion_height );
				update_post_meta( $order_id, 'stallion-side-unit', $size_unit );
				update_post_meta( $order_id, 'stallion_postage', $stallion_postage );

				$headers = array('Content-Type: text/html; charset=UTF-8');
				$message = "Hi {$order_data['billing']['first_name']}<br> Here is your Tracking Code :  {$stallion_shipments_body['tracking_code']}";
				wp_mail( $order_data['billing']['email'], 'The Boudoir Album Shipment Tracking Code', $message, $headers);


// kamrul

                $order = wc_get_order( $order_id );
                $order_ids = get_post_meta( $order_id, 'jk_marge_ids', true );

//                 if ( $result['success'] == 1 ) {
                    $message = 'Dear Customer, <br>
                                This is a notice to let you know that your orders: <br>
                                '. $order_id .', '. implode(", ", $order_ids) .' have been merged into one in order for you to receive your items in one shipment. <br> Here is your updated tracking code <strong><a href="https://tools.usps.com/go/TrackConfirmAction?tRef=fullpage&tLc=2&text28777=&tLabels='. get_post_meta( $order_id, 'stallion_tracking_code', true ) .'" target="_blank">'. get_post_meta( $order_id, 'stallion_tracking_code', true ) .'</a></strong><br><br>
                                Thank you for your business, <br>
                                The Boudoir Album<br>
                                <a href="mailto:info@theboudoiralbum.com">info@theboudoiralbum.com</a>';

                    wp_mail( $order->get_billing_email(), 'The Boudoir Album ordered merge', $message, [
                        'Content-Type: text/html; charset=UTF-8' ] );
//                 }
// kamrul      

				
				echo "Success";

	        } else {
                echo $stallion_shipments_body['errors'][0];
            }

	    }

	}
	die();
}









/////////// Filter Postage Of Stallion Shipments ///////////

add_action( 'wp_ajax_stallion_express_postage', 'stallion_express_postage_callback' );
add_action( 'wp_ajax_nopriv_stallion_express_postage', 'stallion_express_postage_callback' );

function stallion_express_postage_callback(){
	check_ajax_referer( 'stallion_express_postage', 'nonce' );

	$stallion_weight = $_REQUEST['weight'];
	$stallion_length = $_REQUEST['length'];
	$stallion_width = $_REQUEST['width'];
	$stallion_height = $_REQUEST['height'];
	$order_id = $_REQUEST['order_id'];

	$order = wc_get_order( $order_id );
	$order_data = $order->get_data();


    // error_log('order_info : ' . print_r($order_data, true));

	$order_details = [
       	'name' => sprintf("%s %s", $order_data['shipping']['first_name'], $order_data['shipping']['last_name']),
        'address1' => $order_data['shipping']['address_1'],
        'address2' => $order_data['shipping']['address_2'],
        'city' => $order_data['shipping']['city'],
        'province_code' => $order_data['shipping']['state'],
        'postal_code' => $order_data['shipping']['postcode'],
        'country_code' => $order_data['shipping']['country'],
        'weight_unit' => 'lbs',
        'weight' => $stallion_weight,
        'length' => $stallion_length,
        'width' => $stallion_width,
        'height' => $stallion_height,
        'size_unit' => 'in',
        'package_contents' => 'hello world',
        'value' => 10,
        'currency' => 'CAD',
        'package_type' => 'parcel',
        'signature_confirmation' => false,
        'purchase_label' => true,
        'insured' => true,
        'region' => 'ON',
	];


    $get_stallion_shipments = wp_remote_post(
      'https://ship.stallionexpress.ca/api/v3/rates', [
        'timeout'     => 45,
        'headers' => [
          'accept' => 'application/json',
          'Authorization' => 'Bearer m2yUOpdOLuDPaIJC8oBvtURm2RKKxrdC6rJTkojcCPYW2q7PhEgIWnb1JLVh',
          'Content-Type' => 'application/json'
        ],
        'body' => json_encode($order_details)
      ]
    );
    
    // error_log('Quantity : ' . print_r($item->get_quantity(), true));

    if( is_wp_error( $get_stallion_shipments ) ){
        error_log('Failed access Token: '. print_r($get_stallion_shipments, true));
    } else {
        $stallion_shipments_body = json_decode( wp_remote_retrieve_body( $get_stallion_shipments ), true );

        // error_log('stallion_body' . print_r($stallion_shipments_body, true));

        if ( $stallion_shipments_body['success'] == 1 ) {
        	echo json_encode($stallion_shipments_body['rates']);
        }

    }

	die();
}

/////////// Filter Postage Of Stallion Shipments ///////////


add_action( 'add_meta_boxes', function(){
    add_meta_box( 'woocommerce-stallion_shipment', __( 'Stallion Shipments', 'woocommerce' ), 'woocommerce_stallion_shipments_func', 'shop_order', 'side', 'high' );
}, 1 );

function woocommerce_stallion_shipments_func() { ?>
    <style type="text/css">
        .add_stallion_shipmentsBtn {
            margin-top: 20px !important;
        }

        .tracking_codes {
            background: #efefef none repeat scroll 0 0;
            padding: 10px;
            position: relative;
            margin: 0;
        }        
    </style>

    <div class="tracking_codes"><strong>Stallion Express : <a href="https://tools.usps.com/go/TrackConfirmAction?tRef=fullpage&tLc=2&text28777=&tLabels=<?php echo get_post_meta( get_the_ID(), 'stallion_tracking_code', true ); ?>" target="_blank"><?php echo get_post_meta( get_the_ID(), 'stallion_tracking_code', true ); ?></a></strong></div>

    <!-- <button class="button button-primary add_stallion_shipmentsBtn">Add Stallion Shipment</button> -->
<?php }
