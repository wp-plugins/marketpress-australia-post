<?php
global $mp, $mp_shipping_plugins, $mp_shipping_active_plugins;

    class MP_Shipping_aust_post extends MP_Shipping_API {
	
		
	//private shipping method name. Lowercase alpha (a-z) and dashes (-) only please!
	var $plugin_name = 'austpost';

	//public name of your method, for lists and such.
	var $public_name = '';

	//set to true if you need to use the shipping_metabox() method to add per-product shipping options
	var $use_metabox = true;

	//set to true if you want to add per-product weight shipping field
	var $use_weight = true;

	//set to true if you want to add per-product extra shipping cost field
	public $use_extra = true;
	
		function on_creation() {
		//declare here for translation
		$this->public_name = __('austpost', 'mp');
		
		$this->shipcost = new Shippingss();
		
				$this->services = array(
				
						'AUS_PARCEL_REGULAR'                   => new austpost_Service('AUS_PARCEL_REGULAR',        __('AUS PARCEL REGULAR', 'mp'),        __('', 'mp') ),
						'AUS_PARCEL_REGULAR_SATCHEL_500G'                   => new austpost_Service('AUS_PARCEL_REGULAR_SATCHEL_500G',        __('PARCEL POST PLUS 500G SATCHEL', 'mp'),        __('', 'mp') ),	
						'AUS_PARCEL_REGULAR_SATCHEL_3KG'                   => new austpost_Service('AUS_PARCEL_REGULAR_SATCHEL_3KG',        __('AUS PARCEL REGULAR SATCHEL 3KG', 'mp'),        __('', 'mp') ),
						'AUS_PARCEL_REGULAR_SATCHEL_5KG'                   => new austpost_Service('AUS_PARCEL_REGULAR_SATCHEL_5KG',        __('AUS PARCEL REGULAR SATCHEL 5KG', 'mp'),        __('', 'mp') ),																								
						'AUS_PARCEL_EXPRESS'                   => new austpost_Service('AUS_PARCEL_EXPRESS',        __('AUS PARCEL EXPRESS', 'mp'),        __('', 'mp') ),
						'AUS_PARCEL_EXPRESS_SATCHEL_500G'                   => new austpost_Service('AUS_PARCEL_EXPRESS_SATCHEL_500G',        __('AUS PARCEL EXPRESS SATCHEL 500G', 'mp'),        __('', 'mp') ),
						'AUS_PARCEL_EXPRESS_SATCHEL_3KG'                   => new austpost_Service('AUS_PARCEL_EXPRESS_SATCHEL_3KG',        __('AUS PARCEL EXPRESS SATCHEL 3KG', 'mp'),        __('', 'mp') ),	
						'AUS_PARCEL_EXPRESS_SATCHEL_5KG'                   => new austpost_Service('AUS_PARCEL_EXPRESS_SATCHEL_5KG',        __('AUS PARCEL EXPRESS SATCHEL 5KG', 'mp'),        __('', 'mp') ),																				
						'AUS_PARCEL_COURIER'                   => new austpost_Service('AUS_PARCEL_COURIER',        __('AUS PARCEL COURIER', 'mp'),        __('', 'mp') ),
						'AUS_PARCEL_COURIER_SATCHEL_MEDIUM'                   => new austpost_Service('AUS_PARCEL_COURIER_SATCHEL_MEDIUM',        __('AUS PARCEL COURIER SATCHEL MEDIUM', 'mp'),        __('', 'mp') ),					
		);

		
		// Get settings for convenience sake
		$this->settings = get_option('mp_settings');
		$this->austpost_settings = $this->settings['shipping']['austpost'];
		
		}
	
		function default_boxes(){
		// Initialize the default boxes if nothing there
		if(count($this->austpost_settings['boxes']['name']) < 2)
		{
			$this->austpost_settings['boxes'] = array (
			'name' =>
			array (
			0 => 'Small (500g) Satchel',
			1 => 'Medium (3kg) Satchel',
			2 => 'Large (5kg) Satchel',
			3 => 'Mailing box, Bx1',
			4 => 'Mailing box, Bx2',
			),
			'size' =>
			array (
			0 => '34x1x25.5',
			1 => '39x1x29.5',
			2 => '49.5x1x42',
			3 => '22x16x7.7',
			4 => '31x22.5x10.2',			
			),
			'weight' =>
			array (
			0 => '.5',
			1 => '3',
			2 => '5',
			3 => '15',
			4 => '20',			
			),
			);
		}
	}	
		
		private function box_row_html($key='') {

		$name = '';
		$size = '';
		$weight = '';

		if ( is_numeric($key) ){
			$name = $this->austpost_settings['boxes']['name'][$key];
			$size = $this->austpost_settings['boxes']['size'][$key];
			$weight = $this->austpost_settings['boxes']['weight'][$key];
			if (empty($name) && empty($size) &empty($weight)) return''; //rows blank, don't need it
		}
		?>
		<tr class="variation">
			<td class="mp_box_name">
				<input type="text" name="mp[shipping][austpost][boxes][name][]" value="<?php echo esc_attr($name); ?>" size="18" maxlength="20" />
			</td>
			<td class="mp_box_dimensions">
				<label>
					<input type="text" name="mp[shipping][austpost][boxes][size][]" value="<?php echo esc_attr($size); ?>" size="10" maxlength="20" />
					<?php echo $this->get_units_length(); ?>
				</label>
			</td>
			<td class="mp_box_weight">
				<label>
					<input type="text" name="mp[shipping][austpost][boxes][weight][]" value="<?php echo esc_attr($weight); ?>" size="6" maxlength="10" />
					<?php echo $this->get_units_weight(); ?>
				</label>
			</td>
			<?php if ( is_numeric($key) ): ?>

			<td class="mp_box_remove">
				<a onclick="austpostDeleteBox(this);" href="#mp_austpost_boxes_table" title="<?php _e('Remove Box', 'mp'); ?>" ></a>
			</td>

			<?php else: ?>

			<td class="mp_box_add">
				<a onclick="austpostAddBox(this);" href="#mp_austpost_boxes_table" title="<?php _e('Add Box', 'mp'); ?>" ></a>
			</td>

			<?php endif; ?>
		</tr>
		<?php
	}	
		
  /**
   * Echo anything you want to add to the top of the shipping screen
   */
	function before_shipping_form() {

  }
  
  /**
   * Echo anything you want to add to the bottom of the shipping screen
   */
	function after_shipping_form() {

  }
  
  /**
   * Echo a table row with any extra shipping fields you need to add to the shipping checkout form
   */
	function extra_shipping_field() {

  }
  
  /**
   * Use this to process any additional field you may add. Use the $_POST global,
   *  and be sure to save it to both the cookie and usermeta if logged in.
   */
	function process_shipping_form() {

  }
	
		function shipping_settings_box($settings) {
		global $mp;
 
		$this->settings = $settings;
		$this->austpost_settings = $this->settings['shipping']['austpost'];
		$system = $this->settings['shipping']['system']; //Current Unit settings english | metric

		?>
  		<script type="text/javascript">
			//Remove a row in the Boxes table
			function austpostDeleteBox(row) {
				var i = row.parentNode.parentNode.rowIndex;
				document.getElementById('mp_shipping_boxes_table').deleteRow(i);
			}

			function austpostAddBox(row)
			{
				//Adds an Empty Row
				var clone = row.parentNode.parentNode.cloneNode(true);
				document.getElementById('mp_shipping_boxes_table').appendChild(clone);
				var fields = clone.getElementsByTagName('input');
				for(i = 0; i < fields.length; i++)
				{
					fields[i].value = '';
				}
			}
		</script>
		<div id="mp_austpost_rate" class="postbox">
			<h3 class='hndle'><span><?php _e('austpost Settings', 'mp'); ?></span></h3>
			<div class="inside">
				<img src="<?php define("WP_AUST_POST_URL", WP_PLUGIN_URL . "/aust-post"); echo WP_AUST_POST_URL; ?>/index.jpg" />
				<p class="description">
					<?php _e('Using this Aust Post Shipping calculator requires requesting an Ecommerce API key. Get your ', 'mp') ?><br />
				</p>
                	<table class="form-table">
					<tbody>
                  	<tr>
							<th scope="row"><?php _e('Aust Post API Key', 'mp') ?></th>
							<td><input type="text" name="mp[shipping][austpost][api_key]" value="<?php esc_attr_e($this->austpost_settings['api_key']); ?>" size="40" maxlength="40" /></td>
						</tr>  
                        
                        						<tr>
							<th scope="row"><?php _e('Aust Post Services', 'mp') ?></th>
							<td>
								<?php foreach($this->services as $service => $detail): ?>
								<label>
									<input type="checkbox" name="mp[shipping][austpost][services][<?php echo $service; ?>]" value="1" <?php if(!empty($this->austpost_settings['services'][$service])) : ?> checked="checked" <?php endif; ?> />&nbsp;<?php echo $detail->name . ' ' .$detail->delivery; ?>
								</label><br />
								<?php endforeach;	?>
							</td>
						</tr>
                        
                        				<tr>
							<th scope="row" colspan="2">
								<?php _e('Standard Boxes and Weight Limits', 'mp') ?>
								<p>
									<span class="description">
										<?php _e('Enter your standard box sizes as LengthxWidthxHeight', 'mp') ?>
										( <b>12x8x6</b> )
										<?php _e('For each box defined enter the maximum weight it can contain.', 'mp') ?>
										<?php _e('Total weight selects the box size used for calculating Shipping costs.', 'mp') ?>
									</span>
								</p>
							</th>
						</tr>
						<tr>
							<td colspan="2">
								<table class="widefat" id="mp_shipping_boxes_table">
									<thead>
										<tr>
											<th scope="col" class="mp_box_name"><?php _e('Box Name', 'mp'); ?></th>
											<th scope="col" class="mp_box_dimensions"><?php _e('Box Dimensions', 'mp'); ?></th>
											<th scope="col" class="mp_box_weight"><?php _e('Max Weight per Box', 'mp'); ?></th>
											<th scope="col" class="mp_box_remove"></th>
										</tr>
									</thead>
									<tbody>
										<?php
										$this->default_boxes();
										if ($this->austpost_settings['boxes']) {
											foreach ( $this->austpost_settings['boxes']['name'] as $key => $value){
												$this->box_row_html($key);
											}
										}
										//Add blank line for new entries. The non numeric $key says it's not in the array.
										$this->box_row_html('');
										?>
									</tbody>
								</table>
							</td>
						</tr>
                    </tbody>
                    </table>
                </div>
                </div>
        
        <?php } 
		
	/**
	* Filters posted data from your form. Do anything you need to the $settings['shipping']['plugin_name']
	*  array. Don't forget to return!
	*/
	function process_shipping_settings($settings) {

		return $settings;
	}

	/**
	* Echo any per-product shipping fields you need to add to the product edit screen shipping metabox
	*
	* @param array $shipping_meta, the contents of the post meta. Use to retrieve any previously saved product meta
	* @param array $settings, access saved settings via $settings array.
	*/
	function shipping_metabox($shipping_meta, $settings) {

	}

	/**
	* Save any per-product shipping fields from the shipping metabox using update_post_meta
	*
	* @param array $shipping_meta, save anything from the $_POST global
	* return array $shipping_meta
	*/
	function save_shipping_metabox($shipping_meta) {

		return $shipping_meta;
	}

	/**
	* Use this function to return your calculated price as an integer or float
	*
	* @param int $price, always 0. Modify this and return
	* @param float $total, cart total after any coupons and before tax
	* @param array $cart, the contents of the shopping cart for advanced calculations
	* @param string $address1
	* @param string $address2
	* @param string $city
	* @param string $state, state/province/region
	* @param string $zip, postal code
	* @param string $country, ISO 3166-1 alpha-2 country code
	* @param string $selected_option, if a calculated shipping module, passes the currently selected sub shipping option if set
	*
	* return float $price
	*/
	
	function calculate_shipping($price, $total, $cart, $address1, $address2, $city, $state, $zip, $country, $selected_option) {
		global $mp;


		if(! $this->crc_ok())
		{
			//Price added to this object
			$this->shipping_options($cart, $address1, $address2, $city, $state, $zip, $country);
		}

		$price = floatval($_SESSION['mp_shipping_info']['shipping_cost']);
		return $price;
	}
	
	
	function shipping_options($cart, $address1, $address2, $city, $state, $zip, $country) {

		$shipping_options = array();

		$this->address1 = $address1;
		$this->address2 = $address2;
		$this->city = $city;
		$this->state = $state;
		$this->destination_zip = $zip;
		$this->country = $country;

		$this->residential = $_SESSION['mp_shipping_info']['residential'];

		if( is_array($cart) ) {
			$shipping_meta['weight'] = (is_numeric($shipping_meta['weight']) ) ? $shipping_meta['weight'] : 0;
			foreach ($cart as $product_id => $variations) {
				$shipping_meta = get_post_meta($product_id, 'mp_shipping', true);
				foreach($variations as $variation => $product) {
					$qty = $product['quantity'];
					$weight = (empty($shipping_meta['weight']) ) ? $this->austpost_settings['default_weight'] : $shipping_meta['weight'];
			$this->weight += floatval($weight) * $qty;
				}
			}
		}


		//If whole shipment is zero weight then there's nothing to ship. Return Free Shipping
		if($this->weight == '0'){ 
		    //Nothing to ship
			$_SESSION['mp_shipping_info']['shipping_sub_option'] = __('Free Shipping', 'mp');
			$_SESSION['mp_shipping_info']['shipping_cost'] =  0;
			return array(__('Free Shipping', 'mp') => __('Free Shipping - 0.00', 'mp') );
		}

        //austpost won't accept a zero weight Package
		$this->weight = ($this->weight == 0) ? 0.1 : $this->weight;

		$max_weight = floatval($this->austpost[max_weight]);
		$max_weight = ($max_weight > 0) ? $max_weight : 22;

		if (in_array($this->settings['base_country'], array('US','UM','AS','FM','GU','MH','MP','PW','PR','PI'))){
			// Can't use zip+4
			$this->settings['base_zip'] = substr($this->settings['base_zip'], 0, 5);
		}

		if (in_array($this->country, array('US','UM','AS','FM','GU','MH','MP','PW','PR','PI'))){
			// Can't use zip+4
			$this->destination_zip = substr($this->destination_zip, 0, 5);
		}
		if ($this->country == $this->settings['base_country']) {
			$shipping_options = $this->rate_request();
		} else {
			$shipping_options = $this->rate_request(true);
		}
					
		return $shipping_options;
	}	
	


	/**
	* rate_request - Makes the actual call to austpost
	*/
	function rate_request( $international = false) {
		global $mp;


		$shipping_options = $this->austpost_settings['services'];

		//Assume equal size packages. Find the best matching box size
		$this->austpost_settings['max_weight'] = ( empty($this->austpost_settings['max_weight'])) ? 22 : $this->austpost_settings['max_weight'];
		$diff = floatval($this->austpost_settings['max_weight']);
		$found = -1;
		$largest = -1.0;

		foreach( $this->austpost_settings['boxes']['weight'] as $key => $weight ) {
			//			//Find largest
			if( $weight > $largest) {
				$largest = $weight;
				$found = $key;
		
			}
			//If weight less
			if( floatval($this->weight) <= floatval($weight) ) {
				$found = $key;
				break;
			}
		}
		
		
		
				$allowed_weight = min($this->austpost_settings['boxes']['weight'][$found], $this->austpost_settings['max_weight']);

		if($allowed_weight >= $this->weight){
			$this->pkg_count = 1;
			$this->pkg_weight = $this->weight;
		} else {
			$this->pkg_count = ceil($this->weight / $allowed_weight); // Avoid zero
			$this->pkg_weight = $this->weight / $this->pkg_count;
		}


		//found our box
		$dims = explode('x', strtolower($this->austpost_settings['boxes']['size'][$found]));
	


		//Clear any old price
		unset($_SESSION['mp_shipping_info']['shipping_cost']);


		if (is_wp_error($response)){
			return array('error' => '<div class="mp_checkout_error">' . $response->get_error_message() . '</div>');
		}
		

		//var_dump($service_set);
		if(! is_array($shipping_options)) $shipping_options = array();
		
		$mp_shipping_options = $shipping_options;
		foreach($shipping_options as $service => $option){
		
		$height = (empty($dims[0]) ) ? 0 : $dims[0];
		$width = (empty($dims[1]) ) ? 0 : $dims[1];
		$length = (empty($dims[2]) ) ? 0 : $dims[2];

 $shippings = $this->shipcost;
 

		$data = array(
		'from_postcode' => $this->settings['base_zip'],
		'to_postcode' => $this->destination_zip,
		'weight' => $this->pkg_weight,
		'height' => intval($height),
		'width' => intval($width),
		'length' => $length,
		'service_code' =>  $service
	);
	
	
   	   try{
	         $shippings->getShippingCost($data);
        }
        catch (Exception $e)
        {
                 return array('error' => '<div class="mp_checkout_error">Aust Post: '.$e->getMessage().' </div>');
        }
           
		   $rate =  floatval($this->shipcost->getShippingCost($data)) * $this->pkg_count;


			if($this->pkg_weight <= '.5' ) {
				unset($mp_shipping_options['AUS_PARCEL_REGULAR_SATCHEL_3KG']);
				unset($mp_shipping_options['AUS_PARCEL_EXPRESS_SATCHEL_3KG']);
				unset($mp_shipping_options['AUS_PARCEL_REGULAR_SATCHEL_5KG']);
				unset($mp_shipping_options['AUS_PARCEL_EXPRESS_SATCHEL_5KG']);
			}
			elseif($this->pkg_weight <= '3' ) {
				unset($mp_shipping_options['AUS_PARCEL_REGULAR_SATCHEL_500G']);
				unset($mp_shipping_options['AUS_PARCEL_EXPRESS_SATCHEL_500G']);
				unset($mp_shipping_options['AUS_PARCEL_REGULAR_SATCHEL_5KG']);
				unset($mp_shipping_options['AUS_PARCEL_EXPRESS_SATCHEL_5KG']);
			}
			elseif($this->pkg_weight <= '5' ) {
				unset($mp_shipping_options['AUS_PARCEL_REGULAR_SATCHEL_500G']);
				unset($mp_shipping_options['AUS_PARCEL_EXPRESS_SATCHEL_500G']);				
				unset($mp_shipping_options['AUS_PARCEL_REGULAR_SATCHEL_3KG']);
				unset($mp_shipping_options['AUS_PARCEL_EXPRESS_SATCHEL_3KG']);
			}
			elseif($this->pkg_weight > '5' ) {
				unset($mp_shipping_options['AUS_PARCEL_REGULAR_SATCHEL_500G']);
				unset($mp_shipping_options['AUS_PARCEL_EXPRESS_SATCHEL_500G']);				
				unset($mp_shipping_options['AUS_PARCEL_REGULAR_SATCHEL_3KG']);
				unset($mp_shipping_options['AUS_PARCEL_EXPRESS_SATCHEL_3KG']);
				unset($mp_shipping_options['AUS_PARCEL_REGULAR_SATCHEL_5KG']);
				unset($mp_shipping_options['AUS_PARCEL_EXPRESS_SATCHEL_5KG']);
			}			
			
			if($rate == 0){  //Not available for this combination
				unset($mp_shipping_options[$service]);
			}
			else
			{
				$delivery = $service_set[$service]->delivery;
				$mp_shipping_options[$service] = array('rate' => $rate, 'delivery' => $delivery);

				//match it up if there is already a selection
				if (! empty($_SESSION['mp_shipping_info']['shipping_sub_option'])){
					if ($_SESSION['mp_shipping_info']['shipping_sub_option'] == $service){
						$_SESSION['mp_shipping_info']['shipping_cost'] =  $rate;
					}
					
					
				}
			}
		}

		//Sort low to high rate
		uasort($mp_shipping_options, array($this,'compare_rates') );
		
				if( empty($_SESSION['mp_shipping_info']['shipping_cost']) ){
			//Get the first one
			reset($mp_shipping_options);
			$service = key($mp_shipping_options);
			$_SESSION['mp_shipping_info']['shipping_sub_option'] = $service;
			$_SESSION['mp_shipping_info']['shipping_cost'] =  $mp_shipping_options[$service]['rate'];
		}

		$shipping_options = array();
		foreach($mp_shipping_options as $service => $options){
 
			$shipping_options[$service] = $this->format_shipping_option($service, $options['rate'], $options['delivery']);
			}
 
					
		//Update the session. Save the currently calculated CRCs
		$_SESSION['mp_shipping_options'] = $mp_shipping_options;
		$_SESSION['mp_cart_crc'] = $this->crc($mp->get_cart_cookie());
		$_SESSION['mp_shipping_crc'] = $this->crc($_SESSION['mp_shipping_info']);
		
		return $shipping_options;
	}

	/**For uasort above
	*/
	function compare_rates($a, $b){
		if($a['rate'] == $b['rate']) return 0;
		return ($a['rate'] < $b['rate']) ? -1 : 1;
	}


	/**
	* Tests the $_SESSION cart cookie and mp_shipping_info to see if the data changed since last calculated
	* Returns true if the either the crc for cart or shipping info has changed
	*
	* @return boolean true | false
	*/
	private function crc_ok(){
		global $mp;

		//Assume it changed
		$result = false;

		//Check the shipping options to see if we already have a valid shipping price
		if(isset($_SESSION['mp_shipping_options'])){
			//We have a set of prices. Are they still valid?
			//Did the cart change since last calculation
			if ( is_numeric($_SESSION['mp_shipping_info']['shipping_cost'])){

				if($_SESSION['mp_cart_crc'] == $this->crc($mp->get_cart_cookie())){
					//Did the shipping info change
					if($_SESSION['mp_shipping_crc'] == $this->crc($_SESSION['mp_shipping_info'])){
						$result = true;
					}
				}
			}
		}
		return $result;
	}

	/**Used to detect changes in shopping cart between calculations
	* @param (mixed) $item to calculate CRC of
	*
	* @return CRC32 of the serialized item
	*/
	public function crc($item = ''){
		return crc32(serialize($item));
	}
	

	/**
	* Formats a choice for the Shipping options dropdown
	* @param array $shipping_option, a $this->services key
	* @param float $price, the price to display
	*
	* @return string, Formatted string with shipping method name delivery time and price
	*
	*/
	private function format_shipping_option($shipping_option = '', $price = '', $delivery = '', $handling=''){
		global $mp;
		if ( isset($this->services[$shipping_option])){
			$option = $this->services[$shipping_option]->name;
		}
		elseif ( isset($this->intl_services[$shipping_option])){
			$option = $this->intl_services[$shipping_option]->name;
		}

		$price = is_numeric($price) ? $price : 0;
		$handling = is_numeric($handling) ? $handling : 0;

		$option .=  sprintf(__(' %1$s - %2$s', 'mp'), $delivery, $mp->format_currency('', $price + $handling) );
		return $option;
	}

	/**
	* Returns an inch measurement depending on the current setting of [shipping] [system]
	* @param float $units
	*
	* @return float, Converted to the current units_used
	*/
	private function as_inches($units){
		$units = ($this->settings['shipping']['system'] == 'metric') ? floatval($units) / 2.54 : floatval($units);
		return round($units,2);
	}

	/**
	* Returns a pounds measurement depending on the current setting of [shipping] [system]
	* @param float $units
	*
	* @return float, Converted to pounds
	*/
	private function as_pounds($units){
		$units = ($this->settings['shipping']['system'] == 'metric') ? floatval($units) * 2.2 : floatval($units);
		return round($units, 2);
	}

	/**
	* Returns a the string describing the units of weight for the [mp_shipping][system] in effect
	*
	* @return string
	*/
	private function get_units_weight(){
		return ($this->settings['shipping']['system'] == 'english') ? __('Pounds','mp') : __('Kilograms', 'mp');
	}

	/**
	* Returns a the string describing the units of length for the [mp_shipping][system] in effect
	*
	* @return string
	*/
	private function get_units_length(){
		return ($this->settings['shipping']['system'] == 'english') ? __('Inches','mp') : __('Centimeters', 'mp');
	}
	


}

if(! class_exists('austpost_Service') ):
class austpost_Service
{
	public $code;
	public $name;
	public $delivery;
	public $rate;

	function __construct($code, $name, $delivery, $rate = null)
	{
		$this->code = $code;
		$this->name = $name;
		$this->delivery = $delivery;
		$this->rate = $rate;

	}
}
endif;
if(! class_exists('Shippingss') ):

	class Shippingss
{
	
	private $api = 'https://auspost.com.au/api/';
        const MAX_HEIGHT = 135; //only applies if same as width
	const MAX_WIDTH = 135; //only applies if same as height
	const MAX_WEIGHT = 22; //kgs
	const MAX_LENGTH = 1111; //cms
	const MAX_GIRTH = 140; //cms
	const MIN_GIRTH = 16; //cms
 
        public function getRemoteData($url)
	{
		global $mp;
        $austpostsettings = get_option('mp_settings');
		$thissettings = $austpostsettings ;
		$thisaustpost_settings = $thissettings['shipping']['austpost'];
		$auth_key = $thisaustpost_settings['api_key'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  'Auth-Key: ' . $auth_key
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$contents = curl_exec ($ch);
		curl_close ($ch);
		return json_decode($contents,true);
	}
 
        public function getShippingCost($data)
	{
		$edeliver_url = "{$this->api}postage/parcel/domestic/calculate.json";
		$edeliver_url = $this->arrayToUrl($edeliver_url,$data);		
		$results = $this->getRemoteData($edeliver_url);
 
		if (isset($results['error']))
			throw new Exception($results['error']['errorMessage']);
 
		return $results['postage_result']['total_cost'];
	}
 
        public function arrayToUrl($url,$array)
	{
		$first = true;
		foreach ($array as $key => $value)
		{
			$url .= $first ? '?' : '&';
			$url .= "{$key}={$value}";
			$first = false; 	
		}	
		return $url;
	}
 
        public function getGirth($height,$width)
	{
		return ($width+$height)*2;
	}
}
endif;
$settings = get_option('mp_settings');

mp_register_shipping_plugin('MP_Shipping_aust_post', 'austpost', __('Aust Post', 'mp'),true );