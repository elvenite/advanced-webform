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

        $this->set_attribute('type', 'text');

        /**
         * TODO
         * check status is active
         * check visibility equals true (config['visible']
         * add delta field (delta is the sort order)
         */

    }

    public function save(){
        $values = (array) $this->get_attribute('value');
        $pattern = '/^https?:\/\//i';
        $embeds = array();

        foreach($values AS $key => &$value){
            // id exists and not null use that, otherwise create the embed
            // id must be in the a embed key like this
            // $value['embed_id']

            if (!isset($value['url']) || (isset($value['url']) && $value['url'] === '')){
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
                $embed = \PodioEmbed::create(array(
                    'url' => $value['url'],
                ));
                
                $embeds[] = $embed;
            } catch (Exception $e){
                continue;
            }
        }

        if ($embeds){
            $urls = array_map(function($v){
                return $v['url'];
            }, $values);
                $this->set_attribute('value', $urls);
                $this->item_field->values = $embeds;
        } else {
            
            $this->set_attribute('value', array(
                'url' => null,
            ));

            $this->item_field->values = null;
        }
    }

    public function render($element = null, $default_field_decorator = 'field'){
        // output is:
        // decorator
        // element


        $attributes = $this->get_attributes();
        $collection = $this->get_value();
        // TODO
        // until we support multiple link inputs in the same field
        if (count($collection)){
            $embed = $collection[0];
            $attributes['value'] = $embed->original_url;
        }


        $attributes['name'] .= '[][url]';

        $element = '<input';

        $element .= $this->attributes_concat($attributes);

        $element .= '>';

        return parent::render($element);
    }
}