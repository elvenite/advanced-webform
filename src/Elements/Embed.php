<?php

/**
 * Advanced Webform for Podio - A form generator for Podio
 *
 * @author      Carl-Fredrik Herö <carl-fredrik.hero@elvenite.se>
 * @copyright   2014 Carl-Fredrik Herö
 * @link        https://github.com/elvenite/advanced-webform
 * @license     https://github.com/elvenite/advanced-webform
 * @version     1.0.0
 * @package     AdvancedWebform
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace AdvancedWebform\Elements;

/**
 * Embed Field Element
 * @package AdvancedWebform
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */

class Embed extends Element{
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		$this->set_attribute('type', 'url');
		
		/**
		 * TODO
		 * check status is active
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
	
	}
	
	public function set_value($values){
		$pattern = '/^https?:\/\//i';
		
		foreach($values AS $key => &$value){
			// id exists and not null use that, otherwise create the embed
			// id must be in the a embed key like this
			// $value['embed_id']
			
			if (isset($value['embed_id']) && !empty($value['embed_id'])){
				$embed = new \PodioEmbed(array(
					'embed_id' => $value['embed'],
				));
			} else {
				if ($value['url'] === ''){
					unset($values[$key]);
					continue;
				}
                                // TODO does this check work?
				$match = preg_match($pattern, $value['url']);
				if (0 === $match){
					$value['url'] = 'http://' . $value['url'];
				} elseif (false === $match){
					unset($values[$key]);
					continue;
				}
				
				try {
					$embed = $this->create_embed($value['url']);
				} catch (Exception $e){
					continue;
				}
			}
			
			$value = $embed;

		}

		if ($values){
			parent::set_value($values);
		}
	}
	
	public function create_embed($url){
		$embed = \PodioEmbed::create(array(
			'url' => $url,
		));
		
		return $embed;
	}
	
	public function render($element = null, $default_field_decorator = 'field'){
		// output is:
		// decorator
		// element
		
		$attributes = $this->get_attributes();
		
		$attributes['name'] .= '[][url]';
		
		$element = '<input';
		
		$element .= $this->attributes_concat($attributes);
		
		$element .= '>';
		
		return parent::render($element);
	}
}

?>
