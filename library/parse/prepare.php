<?php

namespace willow\parse;

use willow;
use willow\core\helper as h;

class prepare {

	private 
		$plugin = false,
		$args = false,
		$process = false
	;
	
    /**
     * Apply Markup changes to passed template
     * find all placeholders in self::$markup and replace with matching values in self::$fields
	 * most complex and most likely to clash go first, then simpler last ##
     * 
     */
    public function __construct(){

		// grab passed plugin object ## 
		$this->plugin = willow\plugin::get_instance();

	}

	public function factory(){

		// include __MAGIC__ context loader ##
		require_once $this->plugin->get_plugin_path( '/library/context/_load.php' ); 

		// create parse object, pushing in each individual parser object ##
		$parse = new \stdClass();

		// generic ##
		$parse->flags = new willow\parse\flags();
		$parse->arguments = new willow\parse\arguments();
		$parse->markup = new willow\parse\markup();

		// tag specific ##
		$parse->partials = new willow\parse\partials();
		$parse->i18n = new willow\parse\i18n();
		$parse->php_functions = new willow\parse\php_functions();
		$parse->willows = new willow\parse\willows();
		$parse->loops = new willow\parse\loops();
		$parse->comments = new willow\parse\comments();
		$parse->variables = new willow\parse\variables();

		// store parsers ##
		$this->plugin->set( 'parse', $parse );

		// create render object, pushing in each individual render object ##
		$render = new \stdClass();

		$render->callback = new willow\render\callback();
		$render->format = new willow\render\format();
		$render->args = new willow\render\args();
		$render->markup = new willow\render\markup();
		$render->log = new willow\render\log();
		$render->output = new willow\render\output();
		$render->fields = new willow\render\fields();

		// store render objects ##
		$this->plugin->set( 'render', $render );

		// create type object, pushing in each individual render object ##
		$type = new \stdClass();
		
		// get types ##
		$type->method = new willow\type\method();

		$this->plugin->set( 'type', $type );

	}
	
	public function hooks( $args = null, $process = 'secondary' ){

		// assign ##
		$this->process = $process;
		$this->args = $args;

		// w__log( $this->args );
		// w__log( 'process: '.$this->process );

		// used for assigning filters ##
		if(
			isset( $this->args )
			&& is_array( $this->args )
			&& isset( $this->args['context'] )
			&& isset( $this->args['task'] )
		){

			$this->plugin->set( '_parse_context', $this->args['context'] );
			$this->plugin->set( '_parse_task', $this->args['task'] );
			// self::$parse_args = $args;

		}

		// search for partials in passed markup - these update the markup template with returned markup ##
		$this->plugin->parse->partials->match( $this->args, $this->process );

		// pre-format markup to run any >> translatable strings << ##
		// runs early and might be used to return data to arguments ##
		$this->plugin->parse->i18n->match( $this->args, $this->process );

		// pre-format markup to run any >> functions << ##
		// runs early and might be used to return data to arguments ##
		$this->plugin->parse->php_functions->match( $this->args, $this->process );

		// pre-format markup to extract data from willows ##
		$this->plugin->parse->willows->match( $this->args, $this->process );

		// pre-format markup to extract loops ##
		$this->plugin->parse->loops->match( $this->args, $this->process );

		// pre-format markup to extract comments and place in html ##
		$this->plugin->parse->comments->match( $this->args, $this->process );

		// pre-format markup to extract variable arguments - 
		// goes last, as other tags might have added new variables to prepare ##
		$this->plugin->parse->variables->match( $this->args, $this->process );




		// pre-format markup to run any >> php variables << ##
		// runs early and might be used to return data to arguments ##
		// __deprecated__ in 2.0.0
		// willow\parse\php_variables::prepare( $args, $process );

		// __deprecated__ in 2.0.0
		// remove all flags before markup is parsed ##
		// flags::cleanup( $args, $process );

	}




	

}
