<?php

namespace BlenderDeluxe\Khipu\KhipuService;

/**
 * (c) Nicolas Moncada <nicolas.moncada@tifon.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use BlenderDeluxe\Khipu\Khipu;

/**
 * Servicio CreatePaymentPage que extiende de KhipuService.
 *
 * Este servicio facilita la creación del boton de pago.
 */
class KhipuServiceCreatePaymentPage extends KhipuService {

  /**
   * Iniciamos el servicio
   */
  public function __construct($receiver_id, $secret) {
	parent::__construct($receiver_id, $secret);
	// Iniciamos la variable apiUrl con la url del servicio.
	$this->apiUrl = Khipu::getUrlService('CreatePaymentPage');
	// Iniciamos el arreglo $data con los valores que requiere el servicio.
	$this->data = array(
	  'receiver_id' => $receiver_id,
	  'subject' => '',
	  'body' => '',
	  'amount' => 0,
	  'custom' => '',
	  'notify_url' => '',
	  'return_url' => '',
	  'cancel_url' => '',
	  'bank_id' => '',
	  'expires_date' => '',
	  'transaction_id' => '',
	  'picture_url' => '',
	  'payer_email' => '',
	);
  }

  /**
   * Método que genera el formulario de pago en HTML
   *
   * @param string $button_type
   *   Dimensión del boton a mostrar
   *
   * @return string
   *   Formulario renderizado
   */

  


  public function renderForm($options = NULL) {

	//Verify if user send options if not I declare
	if(is_null($options))
	{
	  	$options = array(
		  	'button_type' => 'image',
		  	'button_class' => 'btn-paid-khipu',
		  	'button_submit' => array( 'options' => array(
										
										'value' => 'Pagar con Khipu'
										)),
		  	'button_image' => array( 'options' => array(
										'size' => '100x50',
										'url' => NULL,
										))


	  );
	}

	$values = $this->getFormLabels();
	$html = new DOMDocument();
	$html->formatOutput = true;

	$form = $html->createElement('form');
	$form->setAttribute('action', $this->getApiUrl());
	$form->setAttribute('method', 'POST');
	foreach($values as $name => $value) {
	  	$input_hidden = $html->createElement('input');
	  	$input_hidden->setAttribute('type', 'hidden');
	  	$input_hidden->setAttribute('name', $name);
	  	$input_hidden->setAttribute('value', $value);
	  	$form->appendChild($input_hidden);
	}
	$input_hidden = $html->createElement('input');
	$input_hidden->setAttribute('type', 'hidden');
	$input_hidden->setAttribute('name', 'agent');
	$input_hidden->setAttribute('value', $this->agent);
	$form->appendChild($input_hidden);

	if($options['button_type'] === 'image')
	{
	  	//Verify if user use custom URL
	  	if(! is_null($options['button_image']['options']['url']) )
	  	{
			$submit = $html->createElement('input');
	  		$submit->setAttribute('class', $options['button_class']);
			$submit->setAttribute('type', 'image');
			$submit->setAttribute('src', $options['button_image']['options']['url']);
	  	}
	  	else
	  	{
	  		//We use default khipu button
		  	$buttons = Khipu::getButtonsKhipu();
		  	if (isset($buttons[$options['button_image']['options']['size']])) {
				$button = $buttons[$options['button_image']['options']['size']];
		  	}
		  	else {
				$button = $buttons['100x50'];
		  	}
		  	$submit = $html->createElement('input');
	  		$submit->setAttribute('class', $options['button_class']);
		  	$submit->setAttribute('type', 'image');
		  	$submit->setAttribute('src', $button);
	  	}
	  
	}
	else
	{
	  	$submit = $html->createElement('input');
	  	$submit->setAttribute('type', 'submit');
	  	$submit->setAttribute('class', $options['button_class']);
	  	$submit->setAttribute('value', $options['button_submit']['options']['value']);
	}

	$form->appendChild($submit);

	$html->appendChild($form);

	return $html->saveHTML();
  }


  /**
   * Método que retorna los datos requeridos para hacer el formulario
   * adjuntando el hash.
   */
  public function getFormLabels() {
	// Pasamos los datos a string
	$string_data = $this->dataToString();
	$values = array(
	  'hash' => $this->doHash($string_data),
	);
	foreach ($this->data as $name => $value) {
	  $values[$name] = $value;
	}
	return $values;
  }

  protected function dataToString() {
	$string = '';
	$string .= 'receiver_id=' . $this->data['receiver_id'];
	$string .= '&subject=' . $this->data['subject'];
	$string .= '&body=' . $this->data['body'];
	$string .= '&amount=' . $this->data['amount'];
	$string .= '&payer_email=' . $this->data['payer_email'];
	$string .= '&bank_id=' . $this->data['bank_id'];
	$string .= '&expires_date=' . $this->data['expires_date'];
	$string .= '&transaction_id=' . $this->data['transaction_id'];
	$string .= '&custom=' . $this->data['custom'];
	$string .= '&notify_url=' . $this->data['notify_url'];
	$string .= '&return_url=' . $this->data['return_url'];
	$string .= '&cancel_url=' . $this->data['cancel_url'];
	$string .= '&picture_url=' . $this->data['picture_url'];
	return $string;
  }
}
