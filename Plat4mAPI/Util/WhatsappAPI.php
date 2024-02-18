<?php
// namespace Plat4mAPI\Util;
// class WhatsappAPI{

//   private $id;
//   private $key;

//   public function __construct($id, $key){

// 	$this->id = $id;
// 	$this->key = $key;

//   }

//   public function send($send_to, $message_body){
    
//     $data = array('to' => $send_to, 'msg' => $message_body);

//     $url = "https://onyxberry.com/services/wapi/Client/sendMessage";
//     $url = $url.'/'.$this->id.'/'.$this->key;
//     $ch = curl_init( $url );
//     curl_setopt( $ch, CURLOPT_POST, 1);
//     curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
//     curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
//     curl_setopt( $ch, CURLOPT_HEADER, 0);
//     curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

//     $response = curl_exec( $ch );
//     return $response;
//   }
// }


/**
 * Send WhatsApp template message directly by calling HTTP endpoint.
 *
 * For your convenience, environment variables are already pre-populated with your account data
 * like authentication, base URL and phone number.
 *
 * Send WhatsApp API reference: https://www.infobip.com/docs/api#channels/whatsapp/send-whatsapp-template-message
 *
 * Please find detailed information in the readme file.
 */

// require '../../vendor/autoload.php';

// use GuzzleHttp\Client;
// use GuzzleHttp\RequestOptions;

// $client = new Client([
//     'base_uri' => "https://vjeqem.api.infobip.com/",
//     'headers' => [
//         'Authorization' => "App 3339929cd4436588e4e76cf02dd187a6-a2d01b03-52b8-4098-8531-efce197ccf4f",
//         'Content-Type' => 'application/json',
//         'Accept' => 'application/json',
//     ]
// ]);

// $response = $client->request(
//     'POST',
//     'whatsapp/1/message/template',
//     [
//         RequestOptions::JSON => [
//             'messages' => [
//                 [
//                     'from' => '447860099299',
//                     'to' => "917008331217",
//                     'content' => [
//                         'templateName' => 'registration_success',
//                         'templateData' => [
//                             'body' => [
//                                 'placeholders' => ['sender', 'message', 'delivered', 'testing']
//                             ],
//                             'header' => [
//                                 'type' => 'IMAGE',
//                                 'mediaUrl' => 'https://api.infobip.com/ott/1/media/infobipLogo',
//                             ],
//                             'buttons' => [
//                                 ['type' => 'QUICK_REPLY', 'parameter' => 'yes-payload'],
//                                 ['type' => 'QUICK_REPLY', 'parameter' => 'no-payload'],
//                                 ['type' => 'QUICK_REPLY', 'parameter' => 'later-payload']
//                             ]
//                         ],
//                         'language' => 'en',
//                     ],
//                 ]
//             ]
//         ],
//     ]
// );

// echo("HTTP code: " . $response->getStatusCode() . PHP_EOL);
// echo("Response body: " . $response->getBody()->getContents() . PHP_EOL);

?>