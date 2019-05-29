<?php

/*
 * Trata direto com o BI
 *
 * @acesso		public
 * @package		Bi
 * @autor		Ariany Ferreira
 * @copyright	Copyright (c) 2018, Vida Class (http://www.vidaclass.com.br)
 * @criado		2018-09-10
 * @versão      1.0
 * @var         $this Component
 * @return      Array
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;

class MoviesComponent extends Component
{
    private $response_apis = [];

    /**
     * @param array $parameters
     */
    private function sendRequest(array $parameters)
    {
        $ambiente = Configure::read('service_mode');
        $url = Configure::read('Api')[$ambiente]['url'] . $parameters['action'];
        $params = json_encode($parameters['vars']);

        if($parameters['method'] == 'GET')
        {
            $url .= "?" . http_build_query($parameters['vars']);
            $params = null;
        }
        
        if(isset($parameters['auth']))
        {
            $header = "Content-Type: application/json\r\n";
            $header .= "Authorization: Bearer " . $this->tokenRequest($ambiente);
        }
        else
        {
            $header = "Content-Type: application/json\r\n";
        }

        $options = array(
            'http' => array(
                'header'  => $header,
                'method'  => $parameters['method'],
                'content' => $params,
                'ignore_errors' => true
            ),
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        );
        $context  = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);

        UtilsComponent::saveLogFile("sendRequestApi.log", [
            'header' => $header,
            'url' => $url,
            'endpoint' => $parameters['action'],
            'method' => $parameters['method'],
            'params' =>  $params,
            'response_header' => $http_response_header,
            'response' => $result
        ]);

        // Caso ocorra algum erro no Bi
        $error = [
            1 => 'Api indisponível, tente novamente'
        ];
        $result_convert =  json_decode($result, true);

        if($result_convert)
        {
            $result = $result_convert;
        }
        else
        {
            $result = [
                'error' => $error
            ];
            $emailBody = "Ola,
                        <br>
                        <br>
                        Erro geral na Apis da Digi5
                        <br>
                        <br>
                        att.
                        <br>
                        Equipe de TI";

            $mensagem = array(
                'prioridade' => 5,
                'assunto' => 'Erros em Apis Digi5',
                'para' => Configure::read('emails')['ti'],
                'mensagem' => $emailBody
            );

            UtilsComponent::Mail($mensagem);
        }

        $this->response_apis = $result;
    }

    /**
     * Para controlar o token
     * @param $ambiente
     * @return mixed|null
     */
    private function tokenRequest($ambiente)
    {
        // Abre arquivo de token
        $token_file = "../tokens/api.tk";
        $token_read = null;
        if(file_exists($token_file))
        {
            $token_open = fopen($token_file, "r");
            $token_read = fread($token_open, filesize($token_file) + 1);
            fclose($token_open);
        }
        else
        {
            $token_open = fopen($token_file, "w+");
            fwrite($token_open, "");
            fclose($token_open);
        }
        $token_arr = @unserialize($token_read);

        // Token vazio
        if(!$token_arr)
        {
            $user = Configure::read('Api')[$ambiente]['user'];
            $pass = Configure::read('Api')[$ambiente]['pass'];
            $parameters = [
                'action' => 'auth',
                'method' => 'POST',
                'vars' => [
                    'username' => $user,
                    'password' => $pass,
                    'company_id' => 2
                ]
            ];
            $this->sendRequest($parameters);

            if(isset($this->response_apis['data']['result']['token']) and isset($this->response_apis['data']['result']['expire']))
            {
                // Salva token
                $this->response_apis['expires_in'] = strtotime($this->response_apis['data']['result']['expire']);
                $this->response_apis['unix'] = time();
                $token_arr = $this->response_apis;
                $token_open = fopen($token_file, "w+");
                fwrite($token_open, serialize($token_arr));
                fclose($token_open);
            }
        }
        else
        {
            $expira_em = $token_arr['expires_in'] - time();
            if($expira_em < 120)
            {
                $token_open = fopen($token_file, "w+");
                fclose($token_open);
                return $this->tokenRequest($ambiente);
            }
        }
        return (isset($token_arr['data']['result']['token'])) ? $token_arr['data']['result']['token'] : null;
    }

    /**
     * Lista de Filmes Populares
     * @return array
     */
    public function Popular()
    {
        $parameters = [
            'action' => 'movies/popular',
            'method' => 'GET',
            'auth' => true,
            'vars' => []
        ];
        $this->sendRequest($parameters);
        return $this->response_apis;
    }
}