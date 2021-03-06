<?php

namespace willow\context;

use willow\core\helper as h;
use willow;

class partial {

	private
		$plugin = null // this
	;

	/**
	 * 
     */
    public function __construct(){

		// grab passed plugin object ## 
		$this->plugin = willow\plugin::get_instance();

	}

	/**
     * Generic Getter - looks for properties in config matching context->task
	 * can be loaded as a string in context/ui file
     *
     * @param       Array       $args
     * @since       1.4.1
     * @return      Array
     */
    public function get( $args = null ) {

		return $this->plugin->config->get([ 'context' => $args['context'], 'task' => $args['task'] ]);

	}


}
