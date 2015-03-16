<?php

if ( ! class_exists( 'ProSites_Helper_Gateway' ) ) {

	class ProSites_Helper_Gateway {

		public static function get_gateways() {
			global $psts;

			$gateways = array();
			$active_gateways = (array) $psts->get_setting( 'gateways_enabled' );

			foreach( $active_gateways as $active_gateway ) {
				if( method_exists( $active_gateway, 'get_name' ) ) {
					$name = call_user_func( $active_gateway . '::get_name' );
					$gateways[ key( $name ) ] = array(
						'name'  => array_pop( $name ),
						'class' => $active_gateway
					);
				}
			}

			return $gateways;
		}

		public static function get_nice_name( $gateway_key ) {
			$gateway_key = strtolower( $gateway_key ); //picking up some legacy
			$gateways = self::get_gateways();
			$keys = array_keys( $gateways );
			if( in_array( $gateway_key, $keys ) ) {
				return $gateways[$gateway_key]['name'];
			} else {
				return 'trial' == $gateway_key ? __('Trial', 'psts') : $gateway_key;
			}
		}

		public static function is_only_active( $gateway_key ) {
			$gateways = self::get_gateways();
			$gateway_keys = array_keys( $gateways );

			return in_array( $gateway_key, $gateway_keys ) && 1 == count( $gateway_keys );
		}

		public static function is_last_gateway_used( $blog_id, $gateway_key ) {
			$last_gateway = ProSites_Helper_ProSite::last_gateway( $blog_id );

			if( ! empty( $last_gateway ) && $last_gateway == $gateway_key ) {
				return true;
			} else {
				return false;
			}
		}

		public static function load_gateway_currencies() {
			$gateways = ProSites_Helper_Gateway::get_gateways();

			foreach( $gateways as $key => $gateway ) {
				ProSites_Model_Data::load_currencies( $key, $gateway );
			}

		}

		public static function supports_currency( $currency_code, $gateway_slug ) {
			$currencies = ProSites_Model_Data::$currencies;
			$found = false;

			$c_keys = array_keys( $currencies );
			if( in_array( $currency_code, $c_keys ) ) {
				$found = in_array( $gateway_slug, $currencies[ $currency_code ]['supported_by'] );
			}

			return $found;
		}

	}
}