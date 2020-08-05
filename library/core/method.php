<?php

namespace q\willow\core;

use q\core;
use q\core\helper as h;
// use q\ui;
use q\plugin;
use q\get;
use q\view;
use q\asset;

class method extends \q_willow {


	/**
	 * Get string between two placeholders
	 * 
	 * @link 	https://stackoverflow.com/questions/5696412/how-to-get-a-substring-between-two-strings-in-php
	 * @since 4.1.0
	*/
	public static function string_between( $string, $start, $end, $inclusive = false ){ 
		
		$string = " ".$string; 
		$ini = strpos( $string, $start ); 
		
		if ($ini == 0) {
			return ""; 
		}
		
		if ( ! $inclusive ) {

			$ini += strlen( $start ); 
		
		}
		
		$len = strpos( $string, $end, $ini ) - $ini; 
		
		if ( $inclusive ) {
			
			$len += strlen( $end ); 
		
		}

		$string = substr( $string, $ini, $len ); 

		// trim white spaces ##
		$string = trim( $string );

		// h::log( 'string: '.$string );

		// kick back ##
		return $string;
	
	} 


	/**
	 * 
	 * 
	 * @link https://stackoverflow.com/questions/27078259/get-string-between-find-all-occurrences-php/27078384#27078384
	*/
	public static function strings_between( $str, $startDelimiter, $endDelimiter ) {

		$contents = array();
		$startDelimiterLength = strlen($startDelimiter);
		$endDelimiterLength = strlen($endDelimiter);
		$startFrom = $contentStart = $contentEnd = 0;

		while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {

			$contentStart += $startDelimiterLength;
			$contentEnd = strpos($str, $endDelimiter, $contentStart);
			
			if (false === $contentEnd) {
				break;
			}

			$contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
			$startFrom = $contentEnd + $endDelimiterLength;

		}
	  
		return $contents;

	}



	/**
	 * Check if a string starts with a specific string
	 * 
	 * @since 4.1.0
	*/
	public static function starts_with( $haystack = null, $needle = null ){

		// sanity ##
		if (
			is_null( $haystack )
			|| is_null( $needle )
		){
			
			h::log('e:>Error in passed params');

			return false;

		}

		$length = strlen( $needle );

		// remove white spaces and line breaks ##
	    // $haystack = preg_replace( '/\s*/m', '', $haystack );
		
		return ( substr( $haystack, 0, $length ) === $needle );
	 
	}



	/**
	 * Check if a string ends with a specific string
	 * 
	 * @since 4.1.0
	*/
	public static function ends_with( $haystack = null, $needle = null ){

		// sanity ##
		if (
			is_null( $haystack )
			|| is_null( $needle )
		){
			
			h::log('e:>Error in passed params');

			return false;

		}

	    $length = strlen( $needle );
		
		if ( $length == 0 ) {

        	return true;
		
		}

		// remove white spaces and line breaks ##
		// $haystack = preg_replace( '/\s*/m', '', $haystack );

		return ( substr( $haystack, -$length ) === $needle );

	}



	
    /**
     * Recursive pass args 
     * 
     * @link    https://mekshq.com/recursive-wp-parse-args-wordpress-function/
     */
    public static function parse_args( &$args, $defaults ) {

		// sanity ##
		if(
			! $defaults
		){

			// h::log( 'e:>No $defaults passed to method' );

			return $args; // ?? TODO, is this good ? 

		}

        $args = (array) $args;
        $defaults = (array) $defaults;
        $result = $defaults;
        
        foreach ( $args as $k => &$v ) {
            if ( is_array( $v ) && isset( $result[ $k ] ) ) {
                $result[ $k ] = self::parse_args( $v, $result[ $k ] );
            } else {
                $result[ $k ] = $v;
            }
        }

        return $result;

	}
	


	
	/**
	 * 
	 * @link https://www.php.net/manual/en/function.parse-str.php
	*/
	public static function parse_str( $string = null ) {

		// sanity ##
		if(
			is_null( $string )
			|| ! is_string( $string )
		){
			
			h::log( 'e:>Passed string empty or bad format' );

			return false;

		}

		// h::log($string);

		// delimiters ##
		$operator_assign = '=';
		$operator_array = '->';
		$delimiter_key = ':';
		$delimiter_and_property = ',';
		$delimiter_and_key = '&';

		// check for "=" delimiter ##
		if( false === strpos( $string, $operator_assign ) ){

			h::log( 'e:>Passed string format does not include asssignment operator "'.$operator_assign.'" --> '.$string );

			return false;

		}

		# result array
		$array = [];
	  
		# split on outer delimiter
		$pairs = explode( $delimiter_and_key, $string );

		// h::log( $pairs );
	  
		# loop through each pair
		foreach ( $pairs as $i ) {

			# split into name and value
			list( $key, $value ) = explode( $operator_assign, $i, 2 );

			// what about array values ##
			// example -- sm:medium, lg:large
			if( false !== strpos( $value, $delimiter_key ) ){

				// temp array ##
				$value_array = [];	

				// preg_match_all( "~\'[^\"]++\"|[^,]++~", $value, $result );
				// h::log( $result );
				$value_pairs = self::quoted_explode( $value, $delimiter_and_property, '"' );
				// h::log( $value_pairs );

				// split value into an array at "," ##
				// $value_pairs = explode( $delimiter_and_property, $value );

				// h::log( $value_pairs );

				# loop through each pair
				foreach ( $value_pairs as $v_pair ) {

					// h::log( 'e:>'.$v_pair ); // 'sm:medium'

					# split into name and value
					list( $value_key, $value_value ) = explode( $delimiter_key, $v_pair, 2 );

					$value_array[ $value_key ] = $value_value;

				}

				// check if we have an array ##
				if ( is_array( $value_array ) ){

					$value = $value_array;

				}

			}
		 
			// $key might be in part__part format, so check ##
			if( false !== strpos( $key, $operator_array ) ){

				// explode, max 2 parts ##
				$md_key = explode( $operator_array, $key, 2 );

				# if name already exists
				if( isset( $array[ $md_key[0] ][ $md_key[1] ] ) ) {

					# stick multiple values into an array
					if( is_array( $array[ $md_key[0] ][ $md_key[1] ] ) ) {
					
						$array[ $md_key[0] ][ $md_key[1] ][] = $value;
					
					} else {
					
						$array[ $md_key[0] ][ $md_key[1] ] = array( $array[ $md_key[0] ][ $md_key[1] ], $value );
					
					}

				# otherwise, simply stick it in a scalar
				} else {

					$array[ $md_key[0] ][ $md_key[1] ] = $value;

				}

			} else {

				# if name already exists
				if( isset($array[$key]) ) {

					# stick multiple values into an array
					if( is_array($array[$key]) ) {
					
						$array[$key][] = $value;
					
					} else {
					
						$array[$key] = array($array[$key], $value);
					
					}

				# otherwise, simply stick it in a scalar
				} else {

					$array[$key] = $value;

				}
			  
			}
		}

		// h::log( $array );
	  
		# return result array
		return $array;

	}
	  

	/**
	 * Regex Escape values 
	*/
	public static function regex_escape( $subject ) {

		return str_replace( array( '\\', '^', '-', ']' ), array( '\\\\', '\\^', '\\-', '\\]' ), $subject );
	
	}
	
	/**
	 * Explode string, while respecting delimiters
	 * 
	 * @link https://stackoverflow.com/questions/3264775/an-explode-function-that-ignores-characters-inside-quotes/13755505#13755505
	*/
	public static function quoted_explode( $subject, $delimiter = ',', $quotes = '\"' )
	{
		$clauses[] = '[^'.self::regex_escape( $delimiter.$quotes ).']';

		foreach( str_split( $quotes) as $quote ) {

			$quote = self::regex_escape( $quote );
			$clauses[] = "[$quote][^$quote]*[$quote]";

		}

		$regex = '(?:'.implode('|', $clauses).')+';
		
		preg_match_all( '/'.str_replace('/', '\\/', $regex).'/', $subject, $matches );

		return $matches[0];

	}


}
