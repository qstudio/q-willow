# Willow #
**Contributors:** qlstudio  
**Tags:** Simple, logic-less, procedural, semantic, markup, language  
**Requires at least:** 5.0  
**Tested up to:** 5.5.3  
**Stable tag:** 2.0.2    
**License:** GPL2  

Willow ~ A logic-less template engine built for WordPress.

Willow has been designed to meet the needs of both front-end and back-end developers by providing a small, yet powerful set of tags and tools to speed up template development and prototype iteration.

## Hello Willow

All Willow tags include a matching opening and closing pair, starting and ending with a curly bracket and one other internal character, as follows:

```
{~ ui~hello {+ Willow says <strong>{{ hello }}</strong> +} ~}
```

This tag calls the class **ui** method **hello** - ui->hello() - and wraps the returned data in any markup passed in the Willow argument: 

```php
class ui {

	function hello( $args = null ) {

		// define key + value to render ##
		return [
			'hello' => 'Hello'
		];

	}

}
```

## Return:

```
Willow says <strong>Hello<strong>
```
## Wiki

Visit the documentation site for further details -> https://qstudio.us/docs/willow/
