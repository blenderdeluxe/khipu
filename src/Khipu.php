<?php

namespace BlenderDeluxe\Khipu;

/**
 * (c) Nicolas Moncada <nicolas.moncada@tifon.cl> y Gonzalo De Spirito <gonzalo@freshworkstudio.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * Definimos la ruta de Khipu.
 */
use Exception;

define('KHIPU_ROOT', dirname(__FILE__) . '/');

/**
 * Provee y centraliza la carga de los servicios que presta Khipu.
 */
class Khipu
{
  /**
   * Version del servicio de Khipu.
   */
  const VERSION_KHIPU_SERVICE = '1.3';

  /**
   * Version
   */
  const VERSION = '1.3';

  /**
   * Corresponde a la ID del cobrador.
   *
   * @var string
   */
  protected $receiver_id;

  /**
   * Corresponde a la llave del cobrador.
   *
   * @var string
   */
  protected $secret;

  /**
   * Identificar al cobrador que utilizara los servicios.
   *
   * No es necesario para utilizar el servicio VerifyPaymentNotification.
   *
   * @param string $receiver_id
   *   Identificador dado por el servicio Khipu.
   * @param string $secret
   *   La llave secreta del identificador.
   */
  public function authenticate($receiver_id, $secret) {
    $this->receiver_id = $receiver_id;
    $this->secret = $secret;
  }

  protected $agent = 'lib-php-1.3';

  public function setAgent($agent) {
      $this->agent = 'lib-php-1.3 - '.$agent;
      return $this;
  }

  /**
   * Carga el servicio y retorna el objeto, en caso de no existir el servicio,
   * se invoca un excepcion.
   */
  public function loadService($service_name) {
    // Definimos el nombre de la clase completa del servicio.
    $class = 'BlenderDeluxe\Khipu\KhipuService\KhipuService' . $service_name;
    // Asignamos la ruta del archivo que contiene la clase.

    // Consultamos si existe el archivo.
    if (class_exists($class)) {
      // Si existe se llama.

      $services_name = self::getAllServicesName();

      if ($services_name[$service_name]) {
        // Es requerido identificarse para usar estos servicios.
        if ($this->receiver_id && $this->secret) {
          $service = new $class($this->receiver_id, $this->secret);
        } else {
            // Invocamos un Exception
            throw new Exception("Is necessary to authenticate to use the service \"$service_name\"");
        }
      }
      else {
        $service = new $class();
      }
        $service->setAgent($this->agent);
        return $service;
    }
    // Si no existe el servicio se invoca un Exception
    throw new Exception("The service \"$service_name\" does not exist");

  }

  /**
   * Funcion que retorna las URL de los servicios de Khipu.
   *
   * @param string $service_name
   *   Nombre del servicio
   */
  public static function getUrlService($service_name) {
    $url_khipu = 'https://khipu.com/api/' . self::VERSION_KHIPU_SERVICE . '/';

    $services_name = self::getAllServicesName();

    if (array_key_exists($service_name, $services_name)) { 
       $str = $service_name;
       $str[0] = strtolower($str[0]);
       return $url_khipu . (string)$str;
    }
    return FALSE;
  }


  /**
   * Funcion que retorna los nombre de servicios que existen y si se requiere
   * identificarse.
   */
  public static function getAllServicesName() {
    return array(
      'CreateEmail' => TRUE,
      'CreatePaymentPage' => TRUE,
      'CreatePaymentURL' => TRUE,
      'VerifyPaymentNotification' => FALSE,
      'ReceiverStatus' => TRUE,
      'SetBillExpired' => TRUE,
      'SetPaidByReceiver' => TRUE,
      'SetRejectedByPayer' => TRUE,
      'PaymentStatus' => TRUE,
      'UpdatePaymentNotificationUrl' => TRUE,
      'ReceiverBanks' => TRUE,
      'GetPaymentNotification' => TRUE,
    );
  }

  /**
   * Funcion que retorna la lista de botones que da a disposición Khipu.
   */
  public static function getButtonsKhipu() {
    $url = 'https://s3.amazonaws.com/static.khipu.com';
    return array(
      '50x25'     => $url . '/buttons/2015/50x25-purple.png',
      '110x25'    => $url . '/buttons/2015/110x25-transparent.png',
      '110x50'    => $url . '/buttons/2015/110x50-transparent.png',
      '150x50'    => $url . '/buttons/2015/150x50-transparent.png',
      '200x75'    => $url . '/buttons/2015/200x75-transparent.png',
    );
  }

  /**
   * Funcion que permite cargar los servicios como si fuesen propiedades. Ej: $khipu->ReceiverBanks->consult()
   *
   */
  public function __get($resource)
  {
    return $this->loadService($resource);
  }
}