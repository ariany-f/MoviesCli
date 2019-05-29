<?php

/*
 * Utilidades minhas
 *
 * @acesso		public
 * @package       Cake.Controller.Component
 * @autor		Anderson Carlos (anderson.carlos@tecnoprog.com.br)
 * @copyright	Copyright (c) 2015, Vida Class (http://www.vidaclass.com.br)
 * @criado		2015-11-20
 * @versão      1.0
 *
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use DateTime;
use Phinx\Util\Util;

class UtilsComponent extends Component
{
    /**
     * @param $var
     * @param bool $stop
     * @param bool $html
     */
    public static function pr($var, $stop = false, $html = false)
    {
        $status = debug_backtrace();
        if (Configure::read('debug') == true)
        {
            if($html)
            {
                echo "<pre>";
                echo "File: " . $status[0]['file'] . "\n";
                echo "Line: " . $status[0]['line'] . "\n";
                echo "\n";
                print_r($var);
                echo "</pre>";
            }
            else
            {
                echo "File: " . $status[0]['file'] . "\n";
                echo "Line: " . $status[0]['line'] . "\n";
                echo "\n";
                print_r($var);
            }

            if ($stop)
            {
                die;
            }
        }
    }

    /**
     * @param $var
     * @param bool $stop
     * @param bool $html
     */
    public static function vd($var, $stop = FALSE, $html = false)
    {
        $status = debug_backtrace();
        if (!Configure::read('debug') == 0)
        {
            if($html)
            {
                echo "<pre>";
                echo "File: " . $status[0]['file'] . "\n";
                echo "Line: " . $status[0]['line'] . "\n";
                echo "\n";
                var_dump($var);
                echo "</pre>";
            }
            else
            {
                echo "File: " . $status[0]['file'] . "\n";
                echo "Line: " . $status[0]['line'] . "\n";
                echo "\n";
                var_dump($var);
            }

            if ($stop)
            {
                die;
            }
        }
    }

    /**
     * Salva em arquivo um debug
     * @param $file
     * @param $data
     */
    public static function saveLogFile($file, $data)
    {
        if (Configure::read('debug') != false)
        {
            $status = debug_backtrace();
            $dir = (php_sapi_name() === 'cli' ? "logs/" : "../logs/");
            $log = fopen($dir . $file, "a+");
            fwrite($log, date('Y-m-d H:i:s') . "\n" . print_r(['file' => $status[0]['file'], 'line' => $status[0]['line'], 'data' => $data], true) . "\n\n");
            fclose($log);
        }
    }

    /**
     * @param $var
     * @return array|mixed
     */
    public static function escape($var)
    {
        if(is_array($var))
        {
            return array_map(__METHOD__, $var);
        }


        if(!empty($var) && is_string($var))
        {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $var);
        }

        return $var;
    }

    /**
     * Gera uma chave qualquer
     * @param int $dig
     * @return string
     */
    public static function genKey($dig = 32)
    {
        $chars = array( "a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J","k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T","u","U","v","V","w","W","x","X","y","Y","z","Z","1","2","3","4","5","6","7","8","9","0");
        $max_elements = count($chars) - 1;
        $newpw = $chars[rand(0,$max_elements)];

        if ( $dig < 4 )
        {
            $dig = 4;
        }

        for( $a = 1 ; $a < $dig ; $a++ )
        {
            $newpw .= $chars[rand( 0, $max_elements )];
        }
        return $newpw;
    }

    /**
     * Convert o resulte em algo usavel
     * @param $result
     * @return mixed
     */
    public static function objToArray($result)
    {
        $json = json_encode($result);
        return (array) json_decode($json, true);
    }

    /**
     * Acerta a data para uso geral
     * @param $date
     * @param bool $datetime
     * @return bool|string
     */
    public static function dateUseCommon($date, $datetime = true)
    {
        /**
         * 2015-12-31T00:55:12-0200
         * 2015-12-31 00:55:12
         */
        $length = ($datetime) ? 19 : 10;
        $date = substr(str_replace("T", " ", $date), 0, $length);
        return $date;
    }

    /**
     * Devido ao porco que salvou a data de forma errada no banco
     * @param null $date
     * @return mixed
     */
    public static function dateToDate($date = null)
    {
        $data_new = DateTime::createFromFormat('d/m/Y', trim($date));
        return $data_new->format('Y-m-d');
    }

    /**
     *  $mensagem = array(
     *       'prioridade' => 5,
     *       'assunto' => 'Redefinir senha.',
     *         'para' => array (
     *             email => nome
     *       ),
     *       'mensagem' => $emailBody
     *  );
     *
     * @param array $mensagem
     * @return bool|string
     */
    public static function Mail($mensagem = array())
    {
        switch(Configure::read('service_mode'))
        {
            case 'production':
                $mail_mode = 'production';
                break;

            case 'producao':
                $mail_mode = 'production';
                break;

            case 'homolog':
                $mail_mode = 'homolog';
                break;

            default:
                $mail_mode = 'homolog';
        }



        $smtp_server = isset($mensagem['smtp_server']) ? $mensagem['smtp_server'] : Configure::read('email')[$mail_mode]['smtp_server'];
        $smtp_port = isset($mensagem['smtp_port']) ? $mensagem['smtp_port'] : Configure::read('email')[$mail_mode]['smtp_port'];
        $smtp_user = isset($mensagem['smtp_user']) ? $mensagem['smtp_user'] : Configure::read('email')[$mail_mode]['smtp_user'];
        $smtp_passwd = isset($mensagem['smtp_paswd']) ? $mensagem['smtp_paswd'] : Configure::read('email')[$mail_mode]['smtp_paswd'];
        $send_by_mail = isset($mensagem['send_by_mail']) ? $mensagem['send_by_mail'] : Configure::read('email')[$mail_mode]['send_by_mail'];
        $send_by_name = isset($mensagem['send_by_name']) ? $mensagem['send_by_name'] : Configure::read('email')[$mail_mode]['send_by_name'];


        /**
         * Se nao definir a prioridade, segue com prioridade normal.
         */
        $prioridade = isset($mensagem['prioridade']) ? $mensagem['prioridade'] : 3;

        /**
         * Assunto é obrigatorio
         */
        if (isset($mensagem['assunto']))
        {
            $assunto = $mensagem['assunto'];
        }
        else
        {
            return FALSE;
        }

        /**
         * Destinatario é obrigatorio
         */
        if (isset($mensagem['para']))
        {
            $para = $mensagem['para'];
        }
        else
        {
            return FALSE;
        }

        /**
         * Mensagem é obrigatorio
         */
        if (isset($mensagem['mensagem']))
        {
            $msg = $mensagem['mensagem'];
        }
        else
        {
            return FALSE;
        }

        /**
         * Manda o email ou retorna FALSE
         */
        try{
            if(Configure::read('email')[$mail_mode]['smtp_ssl'])
            {
                $transport = \Swift_SmtpTransport::newInstance( $smtp_server, $smtp_port, 'ssl' );
            }
            else
            {
                $transport = \Swift_SmtpTransport::newInstance( $smtp_server, $smtp_port );
            }

            $transport->setUsername( $smtp_user );
            $transport->setPassword( $smtp_passwd );
            $mailer = \Swift_Mailer::newInstance( $transport );
            $message = \Swift_Message::newInstance();
            $message->setPriority( $prioridade );
            $message->setSubject( $assunto );
            $message->setFrom( array( $send_by_mail => $send_by_name ) );
            $message->setTo( $para );
            if (isset($mensagem['bcc']))
            {
                $message->setBcc( $mensagem['bcc'] );
            }
            $message->addPart( $msg, 'text/html','UTF-8');
            if (isset($mensagem['anexo']))
            {
                $message->attach(\Swift_Attachment::fromPath($mensagem['anexo']));
            }
            $mailer->send($message);

            //prevenção (too many emails per second)
            sleep(1);
            return TRUE;
        }
        catch (Swift_TransportException $erro)
        {
            return "Verifique os dados.";
        }
    }

    /**
     * Converte a chave ou valor para minúsculo
     * @param $param
     * @return mixed
     */
    public static function objToLower($param, $key = FALSE, $row = FALSE)
    {
        $result = array();
        foreach ($param as $k => $r) {
            $k = $key ? mb_strtolower($k) : $k;
            $r = $row ? mb_strtolower($r) : $r;
            $result[$k] = $r;
        }
        return $result;
    }
    
    /**
     * @param $start
     * @param $end
     * @return bool|\DateInterval
     */
    public static function dateDiff($start, $end)
    {
        $date_start = new DateTime($start);
        $date_end = new DateTime($end);
        return $date_start->diff($date_end);
    }

    /**
     * Verifica se o email e valido
     * @param $mail
     * @return bool
     */
    public static function isMail($mail)
    {
        if (filter_var($mail, FILTER_VALIDATE_EMAIL) === FALSE)
        {
            $validate = FALSE;
        }
        else
        {
            $validate = TRUE;
        }
        return $validate;
    }


    /**
     * Verifica se e um phone
     * @param $phone
     * @return bool
     */
    public static function isPhone($phone)
    {
        if((is_numeric($phone)) AND (strlen($phone) >= 10) AND (strlen($phone) <= 11))
        {
            $validate = TRUE;
        }
        else
        {
            $validate = FALSE;
        }
        return $validate;
    }

    /**
     * Verifica se e um numero
     * @param $numeric
     * @return bool
     */
    public static function isNumeric($numeric)
    {
        if (is_numeric($numeric))
        {
            $validate = TRUE;
        }
        else
        {
            $validate = FALSE;
        }
        return $validate;
    }

    /**
     * Para limpar CNPJ / CPF / RG / TELFONES
     * @param $str
     * @return mixed
     */
    public static function clearString($str)
    {
        $remove = array(' ', '.', ',','-',':', '(', ')','/','\\');
        $str = str_replace($remove, '', trim($str));
        return $str;
    }

    /**
     * @param null $cpf
     * @return mixed
     */
    public static function isCpf($cpf = null)
    {
        if(empty($cpf))
        {
            return false;
        }

        $cpf = UtilsComponent::clearString($cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        if (strlen($cpf) != 11)
        {
            return false;
        }
        else if (
            $cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999'
        )
        {
            return false;
        }
        else
        {
            for ($t = 9; $t < 11; $t++)
            {
                for ($d = 0, $c = 0; $c < $t; $c++)
                {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d)
                {
                    return false;
                }
            }
            return true;
        }
    }

    /**
     * @param $value
     * @return DateTime
     */
    public static function dateConvert($value)
    {
        $value = DateTime::createFromFormat('d/m/Y', $value);
        return $value;
    }

    /**
     * Remove os acentos
     * @param $string
     * @return string
     */
    public static function removeAcento($string) {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $chars = array(
            // Latin-1
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's'
        );

        $string = strtr($string, $chars);
        return $string;
    }

    /**
     * @param array $valores
     * @param $input
     * @return mixed
     */
    static function decorator($valores = array(), $input)
    {
        foreach ($valores as $procura => $troca)
        {
            $search[] = $procura;
            $replace[] = $troca;
        }

        $output = str_replace( $search, $replace, $input );
        return $output;
    }

    /**
     * @param null $date
     * @return mixed
     */
    public static function isDate($date = null)
    {
        $var = strlen(trim($date));
        if($var)
        {
            DateTime::createFromFormat('d/m/Y', $date);
            return DateTime::getLastErrors()['warning_count'] == 0 ? true : false;
        }
        else
        {
            return false;
        }
    }

    /**
     * @param null $gender
     * @return mixed
     */
    public static function isGender($gender = null)
    {
        return $gender == 'M' || $gender == 'F' ? true : false;
    }

    /**
     * Verifica se e um numero inteiro
     * @param $int
     * @return bool
     */
    public static function isInteger($int)
    {
        if (is_int($int))
        {
            $validate = TRUE;
        }
        else
        {
            $validate = FALSE;
        }
        return $validate;
    }

    /**
     * Formata o número de pagamento para float
     * @param $val
     * @return int
     */
    public static function formataFloat($val)
    {
        if($val < 100)
            $val = str_pad($val, 3, '0', STR_PAD_LEFT);
        return (float) substr($val, 0, -2).'.'.substr($val, -2);
    }

    /**
     * Formata o número para pagamento
     * @param $val
     * @return int
     */
    public static function formataPagamento($val)
    {
        return (integer) str_replace('.','',  number_format($val,2,'',''));
    }

    /**
     * @param $texto
     * @return mixed|string
     */
    public static function slug($texto)
    {
        $isso = array('.', ',','-',':', '(', ')','/','\\', '&');
        $por = array('', '',' ','', '', '','','', 'e');
        $texto = mb_strtolower($texto);
        $texto = str_replace($isso, $por, $texto);
        $texto = preg_replace('!\s+!', ' ', $texto);
        $texto = UtilsComponent::removeAcento($texto);
        $texto = str_replace(' ', '-', $texto);
        return $texto;
    }

    /**
     * Rertorna dados da imagem
     * @param $url
     * @return array|bool
     */
    public static function isImage($url)
    {
        $img_info = @getimagesize($url);
        if($img_info)
        {
            switch ($img_info[2])
            {
                case 1:
                    $img_type = 'gif';
                break;
                case 2:
                    $img_type = 'jpg';
                break;
                case 3:
                    $img_type = 'png';
                break;
                case 4:
                    $img_type = 'swf';
                break;
                case 5:
                    $img_type = 'psd';
                break;
                case 6:
                    $img_type = 'bmp';
                break;
                case 7:
                    $img_type = 'tiff'; // intel byte order
                break;
                case 8:
                    $img_type = 'tiff'; // motorola byte order
                break;
                case 9:
                    $img_type = 'jpc';
                break;
                case 10:
                    $img_type = 'jp2';
                break;
                case 11:
                    $img_type = 'jpx';
                break;
                case 12:
                    $img_type = 'jb2';
                break;
                case 13:
                    $img_type = 'swc';
                break;
                case 14:
                    $img_type = 'iff';
                break;
                case 15:
                    $img_type = 'wbmp';
                break;
                case 16:
                    $img_type = 'xbm';
                break;

                default:
                    $img_type = 'xxx';
            }

            return [
                'url' => $url,
                'width' => $img_info[0],
                'height' => $img_info[1],
                'ext' => $img_type,
                'bits' => $img_info['bits'],
                'mime' => $img_info['mime']
            ];
        }
        else
        {
            return false;
        }
    }

    /**
     * Valida se a url é valida
     * @param $url
     * @return bool
     */
    public static function isUrl($url)
    {
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'ignore_errors' => true,
                'timeout' => 5
            ),
            'ssl' => [
                //'cafile' => "/etc/ssl/certs/ca-certificates.crt",
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        );

        $context  = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        $header = (isset($http_response_header)) ? $http_response_header : [];
        if( count($header) and ($header[0] == 'HTTP/1.1 200 OK' or $header[0] == 'HTTP/1.0 302 Found'))
        {
            $check = true;
        }
        else
        {
            $check = false;
        }
        return $check;
    }

    /**
     * Retira uma mascara do valor
     * Ex: ###.###.###-## para CPF
     * @param $formato
     * @param $valor
     * @return mixed
     */
    public static function retiramascara($valor)
    {
        $search = [' ', '.', '+', '-', '/', '(', ')'];
        $replace = ['', '', '', '', '', '', ''];
        $valor = str_replace($search, $replace, $valor);

        return $valor;
    }

    /**
     * Acerta o nome do sujeito
     * @param $name
     * @return mixed
     */
    public static function capitalize($name)
    {
        $de = [' E ', ' De ', ' Da ', ' Das ', ' Do ', ' Dos '];
        $para = [' e ', ' de ', ' da ', ' das ', ' do ', ' dos '];
        $name = ucwords(mb_strtolower($name));
        $name = str_replace($de, $para, $name);
        return $name;
    }

    /**
     * Aplica uma mascara no valor
     * Ex: ###.###.###-## para CPF
     * @param $formato
     * @param $valor
     * @return mixed
     */
    public static function mascara($formato, $valor)
    {
        $search = [' ', '.', '+', '-', '/', '(', ')'];
        $replace = ['', '', '', '', '', '', ''];
        $valor = str_replace($search, $replace, $valor);
        $valor_count = strlen($valor);
        $formato_array = explode("#", $formato);

        $formato_alt = "";
        for($a = 0; $a < $valor_count; $a++)
        {
            $formato_alt .= (string) $formato_array[$a];
            $formato_alt .= '#';
        }

        $mascarado = vsprintf(str_replace("#", "%s", $formato_alt), str_split($valor));
        if($mascarado)
        {
            $output = $mascarado;
        }
        else
        {
            $output = $valor;
        }
        return $output;
    }

    /**
     * Busca o nome do país pela sigla
     */
    public static function getCountryBySigla($country){
        switch(strtolower(UtilsComponent::removeAcento($country))){
            case 'br':
                return 'Brazil';
        }
        return $country;
    }

    /**
     * Transforma Sigla ou Nome de país em ISO-alpha3
     */
    public static function countryToAlpha3($country){
        switch(strtolower(UtilsComponent::removeAcento($country))){
            case 'br':
            case 'brazil':
            case 'brasil':
                return 'BRA';
        }
        return $country;
    }

    /**
     * Para configurar password
     * @param $pass
     * @param int $size
     * @param int $rule
     * @return array
     */
    public static function passwordRules($pass, $size = 6, $rule = 1)
    {
        $regex_options = [
            1 => [
                'regex' => "(.{" . $size . ",})",
                'description' => "Qtd minima de caracteres (" . $size . ")"
            ],
            2 => [
                'regex' => "(?=(?:.*[a-z]){1,})",
                'description' => "Ao menos uma letra minuscula"
            ],
            3 => [
                'regex' => "(?=(?:.*[A-Z]){1,})",
                'description' => "Ao menos uma letra maiscula"
            ],
            4 => [
                'regex' => "(?=(?:.*\d){1,})",
                'description' => "Ao menos um numero"
            ],
            5 => [
                'regex' => "(?=(?:.*[!@#$%^&*()\-_=+{};:,<.>]){1,})",
                'description' => "Ao menos um caracter especial"
            ]
        ];

        switch ($rule)
        {
            case 1:
                $regex = $regex_options[1]['regex'];
                $description = $regex_options[1]['description'];
                break;
            case 2:
                $regex = $regex_options[2]['regex'] . $regex_options[1]['regex'];
                $description = $regex_options[1]['description'] . "\n" . $regex_options[2]['description'];
                break;
            case 3:
                $regex = $regex_options[2]['regex']. $regex_options[3]['regex'] . $regex_options[1]['regex'];
                $description = $regex_options[1]['description'] . "\n" . $regex_options[2]['description'] . "\n" . $regex_options[3]['description'];
                break;
            case 4:
                $regex = $regex_options[2]['regex'] . $regex_options[3]['regex'] . $regex_options[4]['regex'] . $regex_options[1]['regex'];
                $description = $regex_options[1]['description'] . "\n" . $regex_options[2]['description'] . "\n" . $regex_options[3]['description'] . "\n" . $regex_options[4]['description'];
               break;
            case 5:
                $regex = $regex_options[2]['regex'] . $regex_options[3]['regex'] . $regex_options[4]['regex'] . $regex_options[5]['regex'] . $regex_options[1]['regex'];
                $description = $regex_options[1]['description'] . "\n" . $regex_options[2]['description'] . "\n" . $regex_options[3]['description'] . "\n" . $regex_options[4]['description'] . "\n" . $regex_options[5]['description'];
                break;
            default:
                $regex = $regex_options[1]['regex'];
                $description = $regex_options[1]['description'];
        }

        if (preg_match("/" . $regex . "/", $pass))
        {
            $check = [
                'status' => true,
                'description' => 'Atende os requisitos'
            ];
        }
        else
        {
            $check = [
                'status' => false,
                'description' => $description
            ];
        }

        return $check;
    }

    public static function getMoipSubscriptionsStatus($status, $return_id = true){
        switch(strtolower($status)){
            case 'active': return $return_id ? 1 : 'Ativa';
            case 'suspended': return $return_id ? 2 : 'Suspensa';
            case 'expired': return $return_id ? 3 : 'Expirada';
            case 'overdue': return $return_id ? 4 : 'Atrasada';
            case 'canceled': return $return_id ? 5 : 'Cancelada';
            case 'trial': return $return_id ? 6 : 'Período de Teste';
        }
    }

    /**
     * @param $valor
     * @return string
     */
    public function moeda($valor)
    {
        return number_format($valor, 2, ',', '.');
    }

    /**
     * Para tratar moeda na Digi5
     * @param $number
     * @return float|mixed|string
     */
    public static function amountDigi5($number)
    {
        $number = (string) $number;
        if(strpos($number, ".") !== false)
        {
            $number = (integer) str_replace(".", "", ((float) $number * 100));
        }
        else
        {
            $number = (float) ((integer) $number / 100);
        }

        return $number;
    }

    /**
     * Criptografando dado
     * @param $value
     * @return string
     */
    public static function encrypt($value)
    {
        return @rtrim(@strtr(@base64_encode(@gzcompress($value, 9)), '+/', '-_'), '=');
    }

    /**
     * Descriptografando dado
     * @param $value
     * @return string
     */
    public static function decrypt($value)
    {
        return @gzuncompress(@base64_decode(@str_pad(@strtr($value, '-_', '+/'), @strlen($value) % 4, '=', STR_PAD_RIGHT)));
    }

    /**
     * Monta itens para o mix
     * @param array $order_itens
     * @return array
     */
    public static function taxMixProvider($order_itens = [])
    {
        $itens = [];
        if(count($order_itens))
        {
            foreach ($order_itens as $item)
            {
                $itens[] = [
                    'value_vidaclass' => (float) $item['value'],
                    'value_provider' => (float) $item['value_prestador'],
                    'qtd' => (int) $item['qtd']
                ];
            }
        }
        return $itens;
    }
}