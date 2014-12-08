<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AdvancedWebform\Elements;

require realpath( __DIR__ . '/../../utils/recaptcha-php-1.11') . '/recaptchalib.php';

/**
 * Description of Recaptcha
 *
 * @author carlfredrikhero
 */
class Recaptcha extends Element{
    
    /**
     * (1=recaptcha, 2=decorator class, 3=description)
     *
     * @var array 
     */
    protected $decorators = array(
        'field' => '<div class="form-group %2$s">%1$s%3$s</div>',
    );
    
    
    public function __construct($app_field, $form, $item_field = null) {
        //parent::__construct($app_field, $form, $item_field);
        
        $this->set_app_field($app_field);
        $this->form = $form;


        $this->set_attribute('public_key', $app_field->config['public_key']);
        $this->set_attribute('private_key', $app_field->config['private_key']);
        
        if (isset($app_field->config['theme'])){
            $this->set_attribute('theme', $app_field->config['theme']);
        }
    }
    
    public function validate(){
        $resp = \recaptcha_check_answer ($this->get_attribute('private_key'),
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

        if (!$resp->is_valid) {
          // What happens when the CAPTCHA was entered incorrectly
            $this->throw_error("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
               "(reCAPTCHA said: " . $resp->error . ")");
        } else {
          // Your code here to handle a successful verification
          return true;
        }
    }
    
    public function render($element = null, $default_field_decorator = 'field'){
        $theme = $this->get_attribute('theme');
        
        if ($theme){
            $recaptcha = \recaptcha_get_html($this->get_attribute('public_key'), null, false, $theme);
        } else {
            $recaptcha = \recaptcha_get_html($this->get_attribute('public_key'));
        }
        
        $decorator_class = array();
                if ($this->error){
                    $decorator_class[] = 'has-error';
                    $this->set_attribute('description', $this->error_message);
                }
                
        $description_decorator = '';
		$description = $this->get_attribute('description');
		if ($description){
			$description_decorator = sprintf($this->get_decorator('field_description'),
												$description
											);
		}
		
        $decorator = sprintf($this->get_decorator($default_field_decorator), 
                                        $recaptcha,
                                        implode(' ', $decorator_class),
                                        $description_decorator
                                );

        return $decorator;

    }
}
