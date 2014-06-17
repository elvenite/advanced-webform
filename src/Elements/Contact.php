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
 * Element for Contact Field
 * Only Contact Fields with workspace contacts will be rendered, workspace users
 * cannot be exposed outside of Podio.
 * @package AdvancedWebform
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */
class Contact extends Element{

    /**
     *
     * @var array[SubContact] 
     */
    protected $sub_fields;

	protected $decorators = array(
		'field' => '<div class="control-group"><label class="control-label" for="%1$s">%2$s</label><div class="controls"><fieldset>%3$s%4$s</fieldsset></div></div>'
	);

    /**
     * Constructor
     * @param PodioAppField $app_field
     * @param \AdvancedWebform $form
     * @param PodioItemField $item_field
     * @throws ErrorException
     */
    public function __construct($app_field, $form, $item_field = null) {
        parent::__construct($app_field, $form, $item_field);


        if ($this->app_field->config['settings']['type'] != 'space_contacts'){
            // workspace members should not be exposed in the outside world
            //return false;
            throw new \ErrorException('Workspace members is not supported.');
        }

        // Contact Name
        $this->add_sub_field('name', new \AdvancedWebform\Elements\SubContactText(array(
            'name' => 'name',
            'required' => true, // name is required by Podio
            'placeholder' => 'Name',
            'parent' => $this,
        )));

        // Contact Title
        $this->add_sub_field('title', new \AdvancedWebform\Elements\SubContactText(array(
            'name' => 'title',
            'required' => false,
            'placeholder' => 'Title',
            'multi' => true,
            'parent' => $this,
        )));

        // Contact Organization
        $this->add_sub_field('organization', new \AdvancedWebform\Elements\SubContactText(array(
            'name' => 'organization',
            'required' => false, 
            'placeholder' => 'Organisation',
            'parent' => $this,
        )));

        // Contact Email
        $this->add_sub_field('mail', new \AdvancedWebform\Elements\SubContactEmail(array(
            'name' => 'mail',
            'required' => false, 
            'placeholder' => 'Email',
            'parent' => $this,
        )));

        // Contact Phone
        $this->add_sub_field('phone', new \AdvancedWebform\Elements\SubContactText(array(
            'name' => 'phone',
            'required' => false,
            'placeholder' => 'Phone',
            'multi' => true,
            'parent' => $this,
        )));


        /**
         * TODO
         * check status is active
         * check visibility equals true (config['visible']
         * add delta field (delta is the sort order)
         */

    }
	
    /**
     * Add sub field
     * @param string $name
     * @param SubContact $element
     */
    public function add_sub_field($name, SubContact $element){
        $this->sub_fields[$name] = $element;
    }

    /**
     * Set value
     * Sets the profile id or creates a new contact
     * @param mixed $values
     */
    public function set_value($values){
        if (is_numeric($values)){
            $profile_id = (int) $values;

            parent::set_value(array(
                'profile_id' => $profile_id,
            ));
        }

        if (isset($values['name']) && !empty($values['name'])){
            $space_id = $this->form->get_app()->space_id;
            if ($profile_values = $this->get_value()){
                $profile_id = $profile_values[0]['value']['profile_id'];
                \PodioContact::update($profile_id, $values);
            } else {
                $profile_id = \PodioContact::create($space_id, $values);
            }

            $values['profile_id'] = $profile_id;

            $this->set_attribute('value', $values);
            $this->item_field->set_value($values);
        }
    }

    /**
     * Renders the element in locked mode
     * @return string
     */
    public function render_locked(){
        $elements = array();
        foreach($this->sub_fields AS $sub_field){
            $elements[] = $sub_field->render_locked();
        }

        return implode('', $elements);
    }

    /**
     * Renders the element
     * @param string $element
     * @param string $default_field_decorator
     * @return string
     */
    public function render($element = null, $default_field_decorator = 'field'){
        $elements = array();

        foreach($this->sub_fields AS $sub_field){
            $elements[] = $sub_field->render();
        }

        if ($this->get_form()->is_sub_form()){
            $decorator_string = 'sub_parent_field';
        } else {
            $decorator_string = 'parent_field';
        }

        return parent::render(
            implode('', $elements),
            $decorator_string
        );
    }
}