<?php

namespace q\willow\context;

use q\core\helper as h;
use q\get;
use q\willow;
use q\willow\core;
use q\willow\context;
use q\willow\render; 

class partial extends willow\context {

	/**
     * Generic Getter - looks for properties in config matching context->task
	 * can be loaded as a string in context/ui file
     *
     * @param       Array       $args
     * @since       1.4.1
	 * @uses		render\fields::define
     * @return      Array
     */
    public static function get( $args = null ) {

		return core\config::get([ 'context' => $args['context'], 'task' => $args['task'] ]);

	}


}
