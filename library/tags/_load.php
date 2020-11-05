<?php

namespace willow;

// use classes ##
use willow\core\helper as h;
use willow;

class tags extends \willow {

	// properties ##
	protected static 
		$filtered_tags = false
	;

	private static function map( $tag = null ){

		// sanity ##
		if ( 
			is_null( $tag )
		){

			h::log( 'e:>No tag passed to map');

			return false;

		}

		// load tags ##
		self::cache();

		// check for class property ##
		if (
			! self::$filtered_tags
		){

			h::log( 'e:>filtered_tags are not loaded..');

			return false;

		}

		// build map ##
		$tag_map = [

			'wil_o' => self::$filtered_tags['willow']['open'],
			'wil_c' => self::$filtered_tags['willow']['close'],

			'var_o' => self::$filtered_tags['variable']['open'],
			'var_c' => self::$filtered_tags['variable']['close'],
			
			'loo_o' => self::$filtered_tags['loop']['open'],
			'loo_c' => self::$filtered_tags['loop']['close'],

			'i18n_o' => self::$filtered_tags['i18n']['open'],
			'i18n_c' => self::$filtered_tags['i18n']['close'],
			
			'php_fun_o' => self::$filtered_tags['php_function']['open'],
			'php_fun_c' => self::$filtered_tags['php_function']['close'],

			'php_var_o' => self::$filtered_tags['php_variable']['open'],
			'php_var_c' => self::$filtered_tags['php_variable']['close'],
			
			'arg_o' => self::$filtered_tags['argument']['open'],
			'arg_c' => self::$filtered_tags['argument']['close'],

			'sco_o' => self::$filtered_tags['scope']['open'],
			'sco_c' => self::$filtered_tags['scope']['close'],
			
			'par_o' => self::$filtered_tags['partial']['open'],
			'par_c' => self::$filtered_tags['partial']['close'],
			
			'com_o' => self::$filtered_tags['comment']['open'],
			'com_c' => self::$filtered_tags['comment']['close'],
			
			'fla_o' => self::$filtered_tags['flag']['open'],
			'fla_c' => self::$filtered_tags['flag']['close'],

		];

		// @todo -- full back, in case not requested via shortcode ##
		// if ( ! isset( $tag_map[$tag] ) ){

			// return isset @todo...

		// }

		// search for and return matching key, if found ##
		return $tag_map[$tag] ?: false ;

	}



	/**
	 * Cache tags, and run filter once per load 
	*/
	protected static function cache(){

		// check if we have already filtered load ##
		if ( self::$filtered_tags ){

			return self::$filtered_tags;

		}
		
		// filter tags once per load ##
		return self::$filtered_tags = \apply_filters( 'willow/render/tags', self::$tags );

	}



	/**
	 * Wrap string in defined tags
	*/
	public static function wrap( $args = null ){

		// sanity ##
		if (
			! isset( $args )
			|| ! is_array( $args )
			|| ! isset( $args['open'] )
			|| ! isset( $args['value'] )
			|| ! isset( $args['close'] )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

		// check ##
		if (
			! self::map( $args['open'] )
			|| ! self::map( $args['close'] )
		){

			h::log( 'e:>Error collecting open or close tags' );

			return false;

		}

		// gather data ##
		$string = self::map( $args['open'] ).$args['value'].self::map( $args['close'] );
		
		// replace method, white space aware ##
		if ( 
			isset( $args['replace'] )
		){

			$array = [];
			$array[] = self::map( $args['open'] ).$args['value'].self::map( $args['close'] );
			$array[] = trim(self::map( $args['open'] )).$args['value'].trim(self::map( $args['close'] )); // trim all spaces in tags
			$array[] = rtrim(self::map( $args['open'] )).$args['value'].self::map( $args['close'] ); // trim right on open ##
			$array[] = self::map( $args['open'] ).$args['value'].ltrim(self::map( $args['close'] )); // trim left on close ##

			// h::log( $array );
			// h::log( 'value: "'.$args['value'].'"' );

			return $array;

		}

		// test ##
		// h::log( 'd:>'.$string );

		// return ##
		return $string;

	}

	
	/**
     * shortcut to get
	 * 
	 * @since 4.1.0
     */
    public static function g( $args = null ) {

		// we can pass shortcut ( mapped ) values -- i.e "var_o" ##
		return self::map( $args ) ?: false ;
 
	}

	
	/**
     * Get a single tag
	 * 
	 * @since 4.1.0
     */
    public static function get( $args = null ) {

		// sanity ##
		if (
			is_null( $args )
			|| ! isset( $args['tag'] )
			|| ! isset( $args['method'] )
		){

			h::log('e:> No args passed to method');

			return false;

		}

		// sanity ##
		if (
			! self::cache()
		){

			h::log('e:>Error in stored $tags');

			return false;

		}

		if (
			! isset( self::$filtered_tags[ $args['tag'] ][ $args['method'] ] )
		){

			h::log('e:>Cannot find tag: '.$args['tag'].'->'.$args['method'] );

			return false;

		}

		// h::log( self::cache() );

		// // get tags, with filter ##
		// $tags = self::cache();

		// looking for long form ##
		return self::$filtered_tags[ $args['tag'] ][ $args['method'] ] ;

	}



	/**
     * Get all tag definitions
	 * 
	 * @since 4.1.0
     */
    public static function get_all( $args = null ) {

		// sanity ##
		if (
			is_null( $args )
		){

			h::log('e:> No args passed to method');

			return false;

		}

		// sanity ##
		if (
			! isset( self::$tags )
			|| ! is_array( self::$tags )
		){

			h::log('e:>Error in stored $tags');

			return false;

		}

		// get tags, with filter ##
		$tags = self::cache();

		// looking for long form ##
		$return = 
			isset( $tags ) ?
			$tags :
			false ;

		return $return;

	}


    /**
     * Define tags on a global or per process basis
	 * 
	 * @since 4.1.0
     */
    public static function set( $args = null ) {

       // @todo ##

    }


}
