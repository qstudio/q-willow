<?php

namespace q\willow;

// use q\core;
use q\willow;
use q\willow\core;
use q\core\helper as h;
// use q\ui;
// use q\render; // @TODO ##

class arguments extends willow\parse {

	private static 

		$string, 
		// $flags_argument,
		// $argument_flags,
		// $field,
		// $value,
		// $tag, 
		$array

	;


	protected static function reset(){

		self::$string = false; 
		self::$flags_argument = false;
		// self::$argument_flags = false;
		// self::$field = false;
		// self::$value = false;
		// self::$tag = false;
		self::$array = false;

	}
	

	
	/*
	Decode arguments passed in string

	Requirements: 

	( new = test & config = debug:true, run:true )
	( config->debug = true & config->handle = sm:medium, lg:large )
	*/
	public static function decode( $string = null ){

		// h::log( $string );

		// sanity ##
		if(
			is_null( $string )
		){

			h::log( 'e:>Error in passed arguments' );

			return false;

		}

		// clear slate ##
		self::reset();
		
		// assign variables ##
		self::$string = $string;

		// trim string ##
		self::$string = trim( self::$string );

		// check for "<" at start and ">" at end ##
		// @todo - move to flags check for [a]
		self::$string = flags::get( self::$string, 'argument' );
		// h::log( self::$flags_argument );
		if( 
			// ! core\method::starts_with( self::$string, '@' )
			// ! isset( self::$flags_argument['a'] ) // not an array
			! self::$flags_argument
			|| ! isset( self::$flags_argument ) // not an array
			|| ! is_array( self::$flags_argument )
			// ||
			// ! render\method::ends_with( $string, ']' ) 
		){

			// h::log( 'd:>Argument string "'.self::$string.'" does not contains any flag, so returning' );

			// done here ##
			return false;

		}

		// check for "=" delimiter ##
		if( false === strpos( self::$string, '=' ) ){

			h::log( 'e:>Error in passed string format, missing delimiter "=" -- '.self::$string );

			return false;

		}

		// h::log( 'd:>string --> '.self::$string );

		// replace " with ' .... hmm ##
		// self::$string = str_replace( '"', "'", self::$string );

		// strip white spaces from data that is not passed inside quotes ( "data" ) ##
		self::$string = preg_replace( '~"[^"]*"(*SKIP)(*F)|\s+~', "", self::$string );

		// h::log( 'd:>string --> '.self::$string );
		// h::log( self::$flags_argument );

		// extract data from string ##
		self::$array = core\method::parse_str( self::$string );

		// h::log( self::$array );

		// trim leading and ending double quotes ("..") from each value in array ##
		array_walk_recursive( self::$array, function( &$v ) { $v = trim( $v, '"' ); });

		// h::log( self::$array );

		// sanity ##
		if ( 
			// ! $config_string
			! self::$array
			|| ! is_array( self::$array )
			// || ! isset( $matches[0] ) 
			// || ! $matches[0]
		){

			h::log( self::$args['task'].'~>e:>No arguments found in string: '.self::$string ); // @todo -- add "loose" lookups, for white space '@s
			// h::log( 'd:>No arguments found in string: '.self::$string ); // @todo -- add "loose" lookups, for white space '@s''

			return false;

		}

		// clear slate ##
		// self::reset();

		// kick back to function handler - it should validate if an array was returned and then deal with it ##
		return self::$array;

	}





	/**
	 * Clean up left-over argument blocks
	 * 
	 * @since 4.1.0
	*/
	public static function cleanup( $args = null, $process = 'internal' ){

		$open = trim( willow\tags::g( 'arg_o' ) );
		$close = trim( willow\tags::g( 'arg_c' ) );

		// h::log( self::$markup['template'] );

		// strip all function blocks, we don't need them now ##
		// // $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
		$regex = \apply_filters( 
		 	'q/willow/parse/argument/cleanup/regex', 
			 // "/$open.*?$close/ms" 
			//  "/$open\s+.*?\s+$close/s"
			"~\\$open\s+(.*?)\s+\\$close~"
		);

		// use callback to allow for feedback ##
		self::$markup['template'] = preg_replace_callback(
			$regex, 
			function($matches) {
				
				// h::log( $matches );
				if ( 
					! $matches 
					|| ! is_array( $matches )
					|| ! isset( $matches[1] )
				){

					return false;

				}

				// h::log( $matches );

				// get count ##
				$count = strlen($matches[1]);

				if ( $count > 0 ) {

					h::log( $count .' argument tags removed...' );

				}

				// return nothing for cleanup ##
				return "";

			}, 
			self::$markup['template'] 
		);
		
		// self::$markup['template'] = preg_replace( $regex, "", self::$markup['template'] ); 

	}


}
