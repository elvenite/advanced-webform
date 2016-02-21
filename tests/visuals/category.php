<?php

/**
 * Output the Text field with help text
 */

require '../../vendor/autoload.php';

$settings = json_decode(file_get_contents('category.json'), true);

$app = new PodioApp(array(
  'id' => 123,
  'fields' => array(
    new PodioAppField(
      $settings
    )
  )
));

$podioform = new \AdvancedWebform\AdvancedWebform(array(
  'app' => $app,
  'method' => 'post',
  'action' => '',
));

$html = file_get_contents('index.html');

$html = str_replace('{{form}}', $podioform->render(), $html);

echo $html;