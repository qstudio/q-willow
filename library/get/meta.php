<?php

namespace willow\get;

// Q ##
use willow\core;
use willow\core\helper as h;
use willow\get;
use willow\render;

class meta extends \willow\get {


	/**
     * link to parent, works for single WP Post or page objects
     *
     * @since       1.0.1
     * @return      string   HTML
     */
    public static function parent( $args = null ) {

		// h::log( 'here..' );
		// h::log( $args );

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

        // set-up new array ##
		$array = [];
		
		// pages might have a parent
		if ( 
			'page' == $args['config']['post']->post_type
			&& $args['config']['post']->post_parent
			&& \get_post( $args['config']['post']->post_parent )
		) {

			$parent = \get_post( $args['config']['post']->post_parent );

            $array['permalink'] = \get_permalink( $parent->ID );
            $array['slug'] = $parent->post_name;
            $array['title'] = $parent->post_title;

		// is singular post ##
		} elseif ( \is_single( $args['config']['post'] ) ) {

			// h::log( 'd:>Get category title..' );

			// $args->ID = $the_post->post_parent;
			if ( 
				! $terms = get\post::object_terms([ 
					'config' 		=> [ 
						'post'		=> $args['config']['post']
					],
					'taxonomy'		=> 'category',
					'args' 			=> [
						'number'	=> 1
					]
				])
					
			){

				h::log( 'e:>Returned terms empty' );

				return false;

			}

			// h::log( $terms );

			// we expect an array with 1 key [0] of WP_Term object - validate ##
			if (
			 	! is_array( $terms )
			 	|| ! isset( $terms[0] )
			 	|| ! $terms[0] instanceof \WP_Term
			){

			 	h::log( 'e:>Error in returned terms data' );

			 	return false;

			}

			$array['permalink'] = \get_category_link( $terms[0] );
			$array['slug'] = $terms[0]->slug;
			$array['title'] = $terms[0]->name;

		}

		// h::log( $array );

        // return ##
		return get\method::prepare_return( $args, $array );

	}



	
	/**
     * Get Post meta field from acf, format if required and markup
     *
     * @since       4.1.0
     */
    public static function field( $args = null )
    {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
			// || ! isset( $args['field'] )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

		// pst ID ##
		$post_id = isset( $args['config']['post'] ) ? $args['config']['post']->ID : null ;

		// get field ##
		if ( $value = \get_field( $args['task'], $post_id ) ) {

			// h::log( $value );

			return $value;

		}

		h::log( 'e:>get_field retuned no data - field: "'.$args['task'].'"');
		
		// return ##
		return false;

	}

	

	/**
	 * Get post author
	 * 
	 * @since 4.1.0
	*/
	public static function author( $args = null ) {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

		// get post ##
		$post = $args['config']['post'];
		
		// get author ##
		$author = $post->post_author;
		$authordata = \get_userdata( $author );

		// validate ##
		if (
			! $authordata
		) {

			h::log( 'd:>Error in returned author data' );

			return false;

		}

		// get author name ##
		$author_name = $authordata && isset( $authordata->display_name ) ? $authordata->display_name : 'Author' ;

		// new array ##
		$array = [];

		// assign values ##
		$array['permalink'] = \esc_url( \get_author_posts_url( $author ) );
		$array['slug'] = $authordata->user_login;
		$array['title'] = $author_name;

		// h::log( $array );

		// return array ##
		// return $array;

		// return ##
		return get\method::prepare_return( $args, $array );

	}



	public static function comment( $args = null ){

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

		// get post ##
		$post = $args['config']['post'];

		// comments ##
		if ( 
			core\config::get([ 'context' => 'global', 'task' => 'config', 'property' => 'allow_comments' ])
			&& 'open' == $post->comment_status // comments are open
		) {
			
			// new array ##
			$array = [];

			// get number of comments ##
			$comments_number = \get_comments_number( $post->ID );

			if ( $comments_number == 0 ) {
				$comments = __( 'Comment', self::text_domain );
			} elseif ( $comments_number > 1 ) {
				$comments = $comments_number.' '.__( 'Comments', self::text_domain );
			} else {
				$comments = '1 '.__( 'Comment', self::text_domain );
			}

			// assign ##
			$array['title'] = $comments;
			$array['count'] = $comments_number;

			if ( \is_single() ) {

				$array['permalink'] = \get_the_permalink( $post->ID ).'#/scroll/comments';

			} else {

				$array['permalink'] = \get_the_permalink( $post->ID ).'#/scroll/comments'; // variable link ##

			}

			// h::log( $array );

			// return ##
			return get\method::prepare_return( $args, $array );

		}

		// comments are closed ##
		return false;

	}



	/**
    * Return the_date with specified format
    *
    * @since       1.0.1
    * @return      string       HTML
    */
    public static function date( $args = null )
    {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'Error in passed args' );

			return false;

		}

        // set-up new array ##
		$array = [];

		// get the post_content with filters applied ##
		$array['date'] = 
			\get_the_date( 
				isset( $args['config']['date_format'] ) ? 
				$args['date_format']['config'] : // take from value passed by caller ##
					core\config::get([ 'context' => 'global', 'task' => 'config', 'property' => 'date_format' ]) ?: // take from global config ##
					\apply_filters( 'q/format/date', 'F j, Y' ), // standard ##
				$args['config']['post']->ID
			);

		// h::log( $array );

		// return ##
		return get\method::prepare_return( $args, $array );

	}



	/**
    * Return the_date with specified format
    *
    * @since       1.0.1
    * @return      string       HTML
    */
    public static function date_human( $args = null )
    {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'Error in passed args' );

			return false;

		}

		// get the_post ##
		$post = $args['config']['post'];

        // starts with an empty array ##
		$array = [];

		// post time in @since format ##
		$array['date_human'] = \human_time_diff( \get_the_date( 'U', $post->ID ), \current_time('timestamp') );

		// h::log( $array );

		// return ##
		return get\method::prepare_return( $args, $array );

	}



	
    /**
    * The Post Data ( meta.. )
    *
    * @since       1.0.2
    */
    public static function data( $args = null )
    {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'Error in passed args' );

			return false;

		}

        // get the_post ##
		$post = $args['config']['post'];

        // test ID ##
        #h::log( $post->ID );

		// starts with an empty array ##
		$array = [];

		// post time in @since format ##
		$array['date_human'] = \human_time_diff( \get_the_date( 'U', $post->ID ), \current_time('timestamp') );

		// post time ##
		$array['date'] = get\post::meta_date( $args );

		// post author ##
		$array = render\method::extract( get\meta::author( $args ), 'author_', $array );

		// category will be an array, so create category_title, permalink and slug fields ##
		$array = render\method::extract( get\taxonomy::category( $args ), 'category_', $array );

		// tag will be an array, so create tag_title, permalink and slug fields ##
		$array = render\method::extract( get\taxonomy::tag( $args ), 'tag_', $array );

		// tags will be an array, we'll let the rendered deal with this via a section tag.. ##
		$array['tags'] = get\taxonomy::tags( $args );

		// comment will be an array, so create comment_count, link ##
		$array = render\method::extract( get\meta::comment( $args ), 'comment_', $array );

		// h::log( $array );

		// return
		return get\method::prepare_return( $args, $array );

	}
	


}