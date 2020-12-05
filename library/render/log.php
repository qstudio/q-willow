<?php

namespace Q\willow\render;

use Q\willow;
use Q\willow\core\helper as h;

class log {

	private 
		$plugin = false
	;

	/**
	 * Scan for partials in markup and convert to variables and $fields
	 * 
	 * @since 4.1.0
	*/
	public function __construct( \Q\willow\plugin $plugin ){

		// grab passed plugin object ## 
		$this->plugin = $plugin;

	}

    /**
     * Logging function
     * 
     */
    public function set( Array $args = null ){

		// h::log( 'e:>'.$args['task'] );
		// h::log( self::$args['config']['debug'] );

        if (
            isset( self::$args['config']['debug'] )
			&& 
				( 
					'1' === self::$args['config']['debug']
					||  true === self::$args['config']['debug']
				)
			// || 'false' == self::$args['config']['debug']
			// || ! self::$args['config']['debug']
        ) {

			// h::log( 'd:>Debugging is turned ON for : "'.$args['task'].'"' );

			// filter in group to debug ##
			\add_filter( 'willow/core/log/debug', function( $key ) use ( $args ){ 
				// h::log( $key );
				$return = is_array( $key ) ? array_merge( $key, [ $args['task'] ] ) : [ $key, $args['task'] ]; 
				// h::log( $return );
				return 
					$return;
				}
			);

			// return ##
			return true; 

		}

		// default ##
		// h::log( 'd:>Debugging is turned OFF for : "'.$args['task'].'"' );

		return false;

		// debug the group ##
		// return core\log::write( $args['task'] );

    }

}
