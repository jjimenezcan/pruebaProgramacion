<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;


class ApiController extends Controller
{
    //

    public function shorturls(Request $request){

        $token = $request->bearerToken();
        if (!$this->validaEntrada($token)){
            $response["status"] = 0;
            $response["msg"] = "Usuario invalido";

            return $response;
        }
        $url = $request->get('url');
        
        $endpoint = config("constants.API_ENDPOINT");

        $client = new Client();
        $res = $client->request('GET', $endpoint.'?url=http://'.$url, []);

        $response = [ "url" => (string)$res->getBody() ];

        return response()->json($response);
        
    }

    private function validaEntrada ($entrada){
        $caracteresValidos = array ("[" => 1, "(" => 1, "{" => 1, "}" => 1, "]" => 1, ")" => 1);
        $caracteresCierre = array ("]" => "[", ")" => "(", "}" => "{");
        $valido = true; $pila = array();
        for ($i=0; $i<strlen($entrada); $i++){
            $letra = $entrada[$i];
            if (!isset($caracteresValidos[$letra])){
                $valido = false;
                break;
            }
            if (isset($caracteresCierre[$letra]) and count($pila)>0){
                $elemento = array_pop($pila);
                if ($elemento!=$letra and $elemento != $caracteresCierre[$letra]){
                    $valido = false;
                    break;
                }
            }else{
                array_push($pila, $letra);
            }
        }

        return  ($valido and count($pila)==0);
    }

}
