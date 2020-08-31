<?php

namespace q\willow\filter;

use q\qillow\core\helper as h;
use q\willow;
use q\willow\filter;

// load it up ##
\q\willow\filter\uppercase::run();

class uppercase extends willow\filter {

	public static function run(){

		// filter variable ##
		\add_filter( 'q/willow/render/markup/variable', [ get_class(), 'variable' ], 10, 2 );

		// filter tag ##
		\add_filter( 'q/willow/render/markup/tag', [ get_class(), 'tag' ], 10, 2 );

	}


	

	// single variable
	public static function variable( $value, $key ) {

		// global first ##
		if( isset( self::$filter[self::$args['context']][self::$args['task']]['variables'][$key]['u'] ) ){

			// h::log( 'e:>Variable tag uppercaseping on: '.self::$args['context'].'->'.self::$args['task'].'->'.$key );

			// h::log( 'd:>uppercaseping tags from value: '.$value );

			$value = strtoupper( $value );
			// $value = htmlentities( $value, ENT_QUOTES, 'UTF-8' ); 

		}

		return $value;

	}



	
	// whole tag
	public static function tag( $value, $key ) {

		// h::log( self::$args );

		// global first ##
		if( isset( self::$filter[self::$args['context']][self::$args['task']]['global']['u'] ) ){

			// h::log( 'e:>Global tag uppercaseping on: '.self::$args['context'].'->'.self::$args['task'].'->'.$key );

			// h::log( 'd:>uppercaseping tags from value: '.$value );

			$value = strtoupper( $value );
			// $value = htmlentities( $value, ENT_QUOTES, 'UTF-8' ); 

		}

		return $value;

	}


}