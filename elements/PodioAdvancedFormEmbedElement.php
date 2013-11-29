<?php

class PodioAdvancedFormEmbedElement extends PodioAdvancedFormElement{
	
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
				$embed = new PodioEmbed(array(
					'embed_id' => $value['embed'],
				));
			} else {
				if ($value['url'] === ''){
					unset($values[$key]);
					continue;
				}
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
		$embed = PodioEmbed::create(array(
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
