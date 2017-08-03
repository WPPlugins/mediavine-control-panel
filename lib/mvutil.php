<?php
if ( ! class_exists( 'MVUtil' ) ) {
	class MVUtil {
		public static function filter_null( $array ) {
			return array_filter( $array, function ( $var ) {
				return ! is_null( $var );
			} );
		}

		public static function get_or_null( $array, $index ) {
			if ( array_key_exists( $index, $array ) ) {
				return $array[ $index ];
			}

			return null;
		}
	}
}
?>
