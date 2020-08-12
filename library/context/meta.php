<?php

namespace q\willow\context;

use q\core\helper as h;
// use q\ui;
use q\get;
use q\willow;
use q\willow\context;
use q\willow\render; 

class meta extends willow\context {


	public static function get( $args = null ){

		// sanity ##
		if(
			is_null( $args )
			|| ! is_array( $args )
			|| ! isset( $args['context'] )
			|| ! isset( $args['task'] )
		){

			h::log( 'e:>Error in passed parameters' );

			return false;

		}

		// take task as method ##
		$method = $args['task'];

		if(
			! method_exists( '\q\get\meta', $method )
			|| ! is_callable([ '\q\get\meta', $method ])
		){

			h::log( 'e:>Class method is not callable: q\get\meta\\'.$method );

			return false;

		}

		// return \q\get\post::$method;

		// h::log( 'e:>Class method IS callable: q\get\meta\\'.$method );

		// call method ##
		$return = call_user_func_array (
				array( '\\q\\get\\meta', $method )
			,   array( $args )
		);

		// // test ##
		// h::log( $return );

		// kick back ##
		return $return;

	}



	/**
     * Get Meta field data via meta handler
     *
     * @param       Array       $args
     * @since       1.3.0
	 * @uses		define
     * @return      Array
     */
    public static function field( $args = null ) {

		// return an array with the field "task" as the placeholder key and value
		return [ $args['task'] => get\meta::field( $args ) ];

	}


}
