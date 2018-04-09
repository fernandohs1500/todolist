<?php

/**
 * @author root
 *
 */
class Helpers {

    /*
     * Monta o breadcrumb e retorna uma matriz com os dados
     */
    public static function trail( $steps = array(), $addHome = false ) {

        if( empty( $steps ) )
            return array();

        $trail = new Trail( $addHome, 'Home', WEB ."/" );

        foreach( $steps as $title => $link )
            $trail->addStep( $title, $link );

        return $trail->path;

    }

    public static function getTimeInterval( $intervalo = 15, $horaInicial = 0, $horaFinal = 2400 ) {

        // intervalo de tempo. Ex.: 08:00, 08:15, 08:30, 08:45
        $x = $intervalo;
        // quantidade de vezes que o intervalo pode ser chamado em uma hora.
        $y = @round( 60 / $x - 1 );
        // contador do sistema
        $contador = 1;

        // prevenindo para que nÃ£o ultrapasse 24:00 horas
        if( $horaFinal > 2400 )
            $horaFinal = 2400;

        for( $i = $horaInicial; $i < $horaFinal; $i++ ) {

            $current = substr( $i, -2 );

            if( $i === 0 ) {
                $horas['00:00'] = '00:00';
            }
            elseif( ( $current == ( $x * $contador ) || $current == 00 ) && $current != false ) {

                if( $i < 99 )
                    $hora = '00' . $i;
                elseif( $i > 99 && $i < 999 )
                    $hora = '0' . $i;
                else
                    $hora = $i;

                if( $contador >= $y ) $contador = 1;
                elseif( $current != 00 ) $contador++;

                $horario = substr( $hora, 0, 2 ) . ':' . substr( $hora, -2 );

                $horas[$horario] = $horario;

            }

        }

        if( $horaFinal > $horario )
            $horas[substr( $horaFinal, 0, 2 ) . ':' . substr( $horaFinal, -2 )] = substr( $horaFinal, 0, 2 ) . ':' . substr( $horaFinal, -2 );

        return $horas;

    }
    /**
     * Converte um timestamp para formato de hora:minuto:segundo, a mesma funï¿½ï¿½o que o SEC_TO_TIME do mysql.
     * @param integer $timestamp
     * @return string
     */
    public static function sec_to_time( $timestamp = 0 ){

        $hora_formatada = '00:00:00';

        if( $timestamp != 0 ) {

            $hours = floor($timestamp / 3600);
            $minutes = floor($timestamp % 3600 / 60);
            $seconds = $timestamp % 60;

            $hora_formatada = self::zeroFill($hours).':'.self::zeroFill($minutes).':'.self::zeroFill($seconds);
        }

        return $hora_formatada;

    }
    /**
     * Converte um formato de hora:minuto:segundo para timestamp, a mesma funï¿½ï¿½o que o TIME_TO_SEC do mysql.
     * @param string $hour
     * @param string $time
     * @return integer
     */
    public static function time_to_sec( $time, $format = 'H:i:s' ) {

        $hours = $mins = $secs = 0;

        if( isset( $time ) ) {

            if( $format == 'i:s' ) {
                list( $mins, $secs ) = explode( ':', $time );
            } else if ( $format == 'H:i:s' ) {
                list( $hours, $mins, $secs ) = explode( ':', $time );
            }

            $hours *= 3600;
            $mins *= 60;

        }

        return $hours + $mins + $secs;
    }

    /**
     * Preenche um nï¿½mero com zeros a esquerda
     * @param int $number
     * @param int $length - tamanho que deve ficar a string; ps.: ignora caso jï¿½ tenha o tamanho desejado;
     */
    public static function zeroFill($number = 0, $length = 2){
        return str_pad($number, $length, "0", STR_PAD_LEFT);
    }

    public static function recursive_utf8_encode( &$array ) {
        if(isset($array) && !empty($array) && is_array($array)){
            array_walk_recursive($array, create_function('&$item, $key', '$item = utf8_encode((string)$item);'));
        }else{
            return false;
        }
    }

    public static function recursive_utf8_decode( &$array ) {
        if(isset($array) && !empty($array) && is_array($array)){
            array_walk_recursive($array, create_function('&$item, $key', '$item = utf8_decode((string)$item);'));
        }else{
            return false;
        }
    }
    /**
     * Formata o dado recebendo as configuraï¿½ï¿½es no mesmo padrï¿½o do colModel do jqgrid
     * @param unknown_type $value
     * @param unknown_type $options
     * @return unknown|string
     */
    public static function formatter($value, $options) {

        if( !isset( $options['formatter'] ) ) {
            return $value;
        }

        $dado = '';

        switch ($options['formatter']) {

            case 'date':
                if( isset($options['formatoptions']) ) {
                    extract($options['formatoptions'], EXTR_PREFIX_ALL, 'opt');

                    if( !isset($opt_srcformat) ) {
                        $opt_srcformat = 'd/m/Y H:i:s';
                    }
                    if( $opt_srcformat == 's' ){
                        if( $opt_newformat == 'H:i:s' ) {
                            $dado = self::sec_to_time( $value );
                        } else {
                            $objDate = new DateTime();
                            $objDate->setTimestamp($value);
                            $dado = $objDate->format($opt_newformat);
                        }
                    } else {

                        $objDate = DateTime::createFromFormat($opt_srcformat, $value);
                        if( $objDate ) {
                            $dado = $objDate->format( $opt_newformat );
                        }

                    }
                }
                break;
            case 'currency':

                //Padrï¿½o R$ 9.999,99
                $opt_prefix = 'R$ ';
                $opt_decimalPlaces = 2;
                $opt_decimalSeparator = ',';
                $opt_thousandsSeparator = '.';

                if( isset($options['formatoptions']) ) {
                    extract($options['formatoptions'], EXTR_PREFIX_ALL, 'opt');
                }

                $dado = $opt_prefix.number_format($value, $opt_decimalPlaces, $opt_decimalSeparator, $opt_thousandsSeparator);

                break;
        }

        return $dado;
    }

    /**
     * Apenas um shortcut para uma formatação de data
     * Recebe Y-m-d e retorna d/m/Y
     * @param string (Y-m-D) $valor
     */
    public static function formatDateDb2Form($valor){
        return self::formatter(
            $valor,
            array(
                'formatter'=>'date',
                'formatoptions'=>array(
                    'srcformat'=>'Y-m-d',
                    'newformat'=>'d/m/Y'
                )
            )
        );
    }

    /**
     * @param string $url
     * @param array/string $post
     */
    public static function curl_post( $url, $post = null, $cookie ) {

        if( isset( $url ) && !empty( $url )  ) {

            if( isset( $post ) && !is_array( $post ) ) {
                parse_str( $post, $post );
            }

            //open connection
            $ch = curl_init( $url );

            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
            curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
            curl_setopt( $ch, CURLOPT_MAXREDIRS, 2 );
            curl_setopt( $ch, CURLOPT_FRESH_CONNECT, 1 );

            //set the url, number of POST vars, POST data
            curl_setopt( $ch, CURLOPT_HEADER, 0 );
            curl_setopt( $ch, CURLOPT_COOKIE, $cookie );

            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
            curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );

            if( isset( $post ) ) {
                curl_setopt( $ch, CURLOPT_POST, count($post) );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
            }

            //execute post
            $result = curl_exec( $ch );

            //close connection
            curl_close( $ch );

            return $result;
        }

        return false;

    }

    /**
     *
     * Adiciona indices necessï¿½rios para o funcionamento do jquery ui autocomplete no seu array de dados
     * @author Daniel Ribeiro Franï¿½a
     * @param array $dados Registros sï¿½o passados por referï¿½ncia
     * @param array $identidade
     * @return boolean|boolean
     * @example Helpers::formatToAutoComplete($contextos, array('id' => 'cod_contexto', 'label' => 'nom_contexto', 'value' => 'nom_contexto'));
     * @Atenï¿½ï¿½o Se o array de registros jï¿½ tiver os indices id, label e value, os valores serï¿½o sobreescritos;
     */
    public static function formatToAutoComplete( &$dados, $identidade ) {

        if( isset($dados) && !empty($dados) && is_array($dados) ) {

            $id = isset($identidade['id']) ? $identidade['id'] : 0;
            $label = isset($identidade['label']) ? $identidade['label'] : 0;
            $value = isset($identidade['value']) ? $identidade['value'] : 0;

            foreach( $dados as $k => &$dado ) {

                $dado['id'] = isset( $dado[$id] ) ? $dado[$id] : NULL;

                $dado['label'] = isset( $dado[$label] ) ? $dado[$label] : NULL ;

                if( isset( $dado[$value] ) ) {
                    $dado['value'] = $dado[$value];
                } else if( isset( $dado[$label] ) ) {
                    $dado['value'] = $dado[$label];
                } else {
                    $dado['value'] = NULL;
                }
            }

            return true;
        }
        return false;
    }

    /**
     * Preenche a hora seguindo a formataï¿½ï¿½o desejada
     * @author Daniel Ribeiro Franï¿½a
     * @param string $format_
     * @param string $value_
     * @example Helpers::timeFill( 'H:i:s', '9' ) retorna '09:00:00'
     */
    public static function timeFill ( $format_ = 'H:i:s', $value_ = '00:00:00' ) {

        $vals = explode( ':', $value_ );

        switch( $format_ ) {
            case 'H:i:s' :

                $h = (isset($vals[0]) ? self::zeroFill( $vals[0] ) : '00');
                $m = (isset($vals[1]) ? self::zeroFill( $vals[1] ) : '00');
                $s = (isset($vals[2]) ? self::zeroFill( $vals[2] ) : '00');

                return "{$h}:{$m}:{$s}";

                break;
            case 'H:i' :

                $h = (isset($vals[0]) ? self::zeroFill( $vals[0] ) : '00');
                $m = (isset($vals[1]) ? self::zeroFill( $vals[1] ) : '00');

                return "{$h}:{$m}";

                break;
        }
    }

    //retorna a data e hora atuais
    public static function now($format = 'Y-m-d H:i:s'){
        return date($format);
    }

    public static function jsonEncode( $array ) {
        self::recursive_utf8_encode( $array );
        return utf8_decode( json_encode( $array ) );
    }

    public static function jsonDecode( $json ) {

        if( !is_object( $json ) || !$json instanceof stdClass )
            $json = json_decode( $json );

        if( is_array( $json ) ) {

            $array = array();

            foreach( $json as $object )
                $array[] = self::jsonDecode( $object );

            return $array;

        }

        $array = get_object_vars( $json );

        self::recursive_utf8_decode( $array );

        return $array;

    }

    /**
     *
     * Checa de forma binï¿½ria um array proposto para utilizaï¿½ï¿½o em GoToIfTime
     * @param array $check
     * @param int $sum
     * @return array
     *
     */
    public static function checkBinFilter( $check = array(), $sum ){

        if( is_array( $check ) && !empty( $check ) ) {

            $check_result = array();

            foreach( $check as $k_check => $v_check ){

                if( $v_check & $sum ){

                    $check_result[$k_check]	= $v_check;
                }
            }
        }

        return $check_result;
    }

    /**
     * Limpa uma string, retira acentos, retira qualquer outro caracter diferente de números e letras
     * @author Daniel Ribeiro França
     * @param string $string
     * @return string $new_string
     * @example Helpers::cleachString( 'Promoção 10' ) retorna 'Promocao10'
     */
    public static function cleanString($string_)
    {
        $new_string = strtr($string_, 'ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½', 'aeiouaoc');
        $new_string = preg_replace("[^A-Za-z0-9]", "", $new_string);
        return $new_string;
    }

    public static function numberToFloat($number)
    {
        return str_replace(',', '.', str_replace( '.', '', $number));
    }

    /**
     * Aplicar valor padrão caso não exista a variável
     * @param mixed $var_
     * @param mixed $default_
     * @param boolean $return_
     */
    public static function IFNULL(&$var_, $default_, $return_ = false)
    {
        $result = isset($var_) ? $var_ : $default_;
        if ($return_) {
            return $result;
        }
        $var_ = $result;
    }
    /**
     * Aplicar valor padrão caso não exista a variável e não esteja vazia
     * @param mixed $var_
     * @param mixed $default_
     * @param boolean $return_
     */
    public static function IFEMPTY(&$var_, $default_, $return_ = false)
    {
        $result = isset($var_) && !empty($var_) ? $var_ : $default_;
        if ($return_) {
            return $result;
        }
        $var_ = $result;
    }

    /**
     * @param String $_var
     * @param Mixed (String/Array) $_callback
     * Exemplo de callback
     * Helpers::IFNOTEMPTY( $_var, array( "str_replace( ',', '.', %s )" ) )
     */
    public static function IFNOTEMPTY(&$_var, $_callback)
    {
        if (!empty($_var)) {
            $_callback = sprintf( $_callback, $_var );
            eval( "\$_var = {$_callback};" );
        }
    }

    /**
     * Calcula a qual bimestre, trimestre ou semestre o mês pertence
     * @author Daniel Ribeiro França
     * @param int $mes
     * @param int $tipo 2|3|6
     * @return number
     * @example getPeriodoMes(1, 2)  returns 1; getPeriodoMes(9, 6) returns 2;
     */
    public static function getPeriodoMes($mes, $tipo)
    {
        $posicao = (int)($mes/$tipo)+($mes%$tipo ? 1 : 0);
        return $posicao;
    }

    /**
     * Retorna data e hora inicial e final de um determinado período, em timestamp
     * @author Daniel Ribeiro França
     * @param $tipo default:'mensal' options:Diário|Semanal|Quinzenal|Mensal|Bimestral|Trimestral|Semestral
     * @param $data_ref default:date('d/m/Y')
     * @return array $range
     * @example
     * $range = Helpers::getRangePeriodo('bimestral', '19/09/2011');
     * date('d/m/Y H:i:s', $range['inicio']) == '01/09/2011 00:00:00' ;
     * date('d/m/Y H:i:s', $range['fim']) == '31/10/2011 23:59:59' ;
     */
    public static function getRangePeriodo($tipo = 'mensal', $data_ref = null)
    {
        $data_ref = isset($data_ref) ? $data_ref : date('d/m/Y');
        list($dia, $mes, $ano) = explode('/',$data_ref);
        $timestamp = mktime(0,0,0, $mes, $dia, $ano);

        $ultimo_dia_mes = date('t', $timestamp );
        $dia_inicio = 1;

        if ($tipo == 'Diário' || $tipo == 1) {

            $range = array('inicio' => $timestamp, 'fim'=> mktime(23,59,59, $mes, $dia, $ano) );

        } elseif ($tipo == 'Semanal' || $tipo == 2) {

            $dia_semana = date('N', $timestamp);
            $range = array('inicio' => strtotime('-'.($dia_semana-1).' day', $timestamp), 'fim'=> strtotime('+'.(7-$dia_semana).' day 23 hours 59 minutes 59 seconds', $timestamp) );

        } elseif ($tipo == 'Quinzenal' || $tipo == 3) {

            $dia_fim = 15;
            if( $dia > 16 ) {
                $dia_inicio = 16;
                $dia_fim = $ultimo_dia_mes;
            }
            $range = array('inicio' => mktime(0,0,0, $mes, $dia_inicio, $ano), 'fim'=> mktime(23,59,59, $mes, $dia_fim, $ano) );

        } elseif ($tipo == 'Mensal' || $tipo == 4) {

            $dia_fim = $ultimo_dia_mes;
            $range = array('inicio' => mktime(0,0,0, $mes, $dia_inicio, $ano), 'fim'=> mktime(23,59,59, $mes, $dia_fim, $ano) );

        } elseif ($tipo == 'Bimestral' || $tipo == 5) {

            $posicao = Helpers::getPeriodoMes($mes, 2);
            $mes_fim = 	$posicao*2;
            $mes_inicio = $mes_fim-1;
            $inicio = mktime(0,0,0, $mes_inicio, $dia_inicio, $ano);
            $dia_fim = date('t', mktime(0,0,0, $mes_fim, 1, $ano));
            $range = array('inicio' => $inicio, 'fim'=> mktime(23,59,59, $mes_fim, $dia_fim, $ano) );

        } elseif ($tipo == 'Trimestral' || $tipo == 6) {

            $posicao = Helpers::getPeriodoMes($mes, 3);
            $mes_fim = 	$posicao*3;
            $mes_inicio = $mes_fim-2;
            $inicio = mktime(0,0,0, $mes_inicio, $dia_inicio, $ano);
            $dia_fim = date('t', mktime(0,0,0, $mes_fim, 1, $ano));
            $range = array('inicio' => $inicio, 'fim'=> mktime(23,59,59, $mes_fim, $dia_fim, $ano) );

        } elseif ($tipo == 'Semestral' || $tipo == 7) {

            $posicao = Helpers::getPeriodoMes($mes, 6);
            $mes_fim = 	$posicao*6;
            $mes_inicio = $mes_fim-5;
            $inicio = mktime(0,0,0, $mes_inicio, $dia_inicio, $ano);
            $dia_fim = date('t', mktime(0,0,0, $mes_fim, 1, $ano));
            $range = array('inicio' => $inicio, 'fim'=> mktime(23,59,59, $mes_fim, $dia_fim, $ano) );

        } elseif ($tipo == 'Anual' || $tipo == 8) {

            $mes_fim = 	12;
            $mes_inicio = 1;
            $inicio = mktime(0,0,0, $mes_inicio, $dia_inicio, $ano);
            $dia_fim = date('t', mktime(0,0,0, $mes_fim, 1, $ano));
            $range = array('inicio' => $inicio, 'fim'=> mktime(23,59,59, $mes_fim, $dia_fim, $ano) );
        }

        return $range;
    }

    public static function jqGridFilters($searchField = null, $searchOper = null, $searchString = null, &$where = array())
    {
        if (null === $searchField || null === $searchOper || null === $searchString) {
            return $where;
        }

        switch ($searchOper) {

            case 'eq':
            case 'ne':
                $searchOper = $searchOper == 'eq' ? '=' : '!=';
                $where[] = "{$searchField} {$searchOper} '{$searchString}'";
                break;
            case 'bw':
                $where[] = "{$searchField} LIKE '{$searchString}%'";
                break;
            case 'bn':
                $where[] = "{$searchField} NOT LIKE '{$searchString}%'";
                break;
            case 'ew':
                $where[] = "{$searchField} LIKE '%{$searchString}'";
                break;
            case 'bn':
                $where[] = "{$searchField} NOT LIKE '%{$searchString}'";
                break;
            case 'cn':
                foreach( $searchField as $field ) {

                    if( empty( $field ) )
                        $where[] = "";
                    else if( $searchString[$field][1] == "" ){
                        $searchString[$field][0] = utf8_decode( $searchString[$field][0] ); // decodifiacando para consulta correta no banco de dados.
                        $where[] = "{$field} LIKE '%{$searchString[$field][0]}%'";
                    }
                    else {
                        if( strpos($field, "dat_" ) !== false )
                            $where[] = "{$field} >= '{$searchString[$field][0]} 00:00:00' AND {$field} <= '{$searchString[$field][1]} 23:59:59'";
                        elseif ( strpos($field, "val_" ) !== false ){

                            $searchString[$field][0] = str_replace( ',', '.', str_replace( '.', '', $searchString[$field][0] ) );
                            $searchString[$field][1] = str_replace( ',', '.', str_replace( '.', '', $searchString[$field][1] ) );

                            $where[] = "{$field} >= '{$searchString[$field][0]}' AND {$field} <= '{$searchString[$field][1]}'";

                        }else
                            $where[] = "{$field} >= '{$searchString[$field][0]}' AND {$field} <= '{$searchString[$field][1]}'";

                    }
                }
                break;
            case 'nc':
                $where[] = "{$searchField} NOT LIKE '%{$searchString}%'";
                break;
            case 'nu':
                $where[] = "{$searchField} IS NULL";
                break;
            case 'nn':
                $where[] = "{$searchField} IS NOT NULL";
                break;
            case 'in':
                $where[] = "{$searchField} IN( '".implode( "','", explode( ',', $searchString ) )."' )";
                break;
            case 'ni':
                $where[] = "{$searchField} NOT IN( '".implode( "','", explode( ',', $searchString ) )."' )";
                break;
        }

        return $where;
    }

    public static function remove_acentos($string, $slug = false)
    {
        $string = strtolower($string);

        // Código ASCII das vogais
        $ascii['a'] = range(224, 230);
        $ascii['e'] = range(232, 235);
        $ascii['i'] = range(236, 239);
        $ascii['o'] = array_merge(range(242, 246), array(240, 248));
        $ascii['u'] = range(249, 252);

        // Código ASCII dos outros caracteres
        $ascii['b'] = array(223);
        $ascii['c'] = array(231);
        $ascii['d'] = array(208);
        $ascii['n'] = array(241);
        $ascii['y'] = array(253, 255);

        foreach ($ascii as $key=>$item) {
            $acentos = '';
            foreach ($item AS $codigo) $acentos .= chr($codigo);
            $troca[$key] = '/['.$acentos.']/i';
        }

        $string = preg_replace( array_values($troca), array_keys($troca), $string );

        // Slug?
        if ($slug) {
            // Troca tudo que nÃ£o for letra ou nÃºmero por um caractere ($slug)
            $string = preg_replace('/[^a-z0-9]/i', $slug, $string);
            // Tira os caracteres ($slug) repetidos
            $string = preg_replace('/' . $slug . '{2,}/i', $slug, $string);
            $string = trim($string, $slug);
        }

        return $string;
    }

    public static function retiraMascaraNumero($string_)
    {
        $new_string = preg_replace("/[^0-9]/", "", $string_);
        return $new_string;
    }

    public static function remove_acentos_sms($string)
    {
        //$string = strtolower($string);

        // Código ASCII das vogais
        $ascii['a'] = range(224, 230);
        $ascii['e'] = range(232, 235);
        $ascii['i'] = range(236, 239);
        $ascii['o'] = array_merge(range(242, 246), array(240, 248));
        $ascii['u'] = range(249, 252);

        // Código ASCII dos outros caracteres
        $ascii['b'] = array(223);
        $ascii['c'] = array(231);
        $ascii['d'] = array(208);
        $ascii['n'] = array(241);
        $ascii['y'] = array(253, 255);

        foreach ($ascii as $key => $item) {
            $acentos = '';
            foreach ($item AS $codigo) $acentos .= chr($codigo);
            $troca[$key] = '/['.$acentos.']/i';
        }

        $string = preg_replace( array_values($troca), array_keys($troca), $string );
        $string = preg_replace( '/[^[:print:]]/', '',$string);

        return $string;

    }

    public static function remove_caracteres_indesejados($string)
    {
        $new_string = strtr($string, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ´/", "aaaaeeiooouucAAAAEEIOOOUUC___");
        $new_string = str_replace("-", "", $new_string);
        $new_string = str_replace(",", "", $new_string);
        $new_string = str_replace("!", "", $new_string);
        $new_string = str_replace("º", "", $new_string);
        $new_string = str_replace("ª", "", $new_string);
        $new_string = str_replace("|", "", $new_string);
        $new_string = str_replace("(", "", $new_string);
        $new_string = str_replace(")", "", $new_string);
        //$new_string = preg_replace ( "[^A-Za-z0-9+]", "", $new_string );
        //return urlencode($new_string);
        return $new_string;

    }

    /**
     * Metodo original
     * @param unknown $string
     * @param string $slug
     * @param string $excecao
     * @return Ambigous <mixed, string>
     */
    public static function remover_acentos_indesejados($string, $slug = false, $excecao = '')
    {
        $low = array("Ç" => "ç", "Á" => "á", "Ã" => "ã", "É" => "é", "Í" => "í", "Ó" => "ó", "Ú" => "ú");
        $string = strtolower(strtr($string, $low));

        // Código ASCII das vogais
        $ascii['a'] = range(224, 230);
        $ascii['e'] = range(232, 235);
        $ascii['i'] = range(236, 239);
        $ascii['o'] = array_merge(range(242, 246), array(240, 248));
        $ascii['u'] = range(249, 252);

        // Código ASCII dos outros caracteres
        $ascii['b'] = array(223);
        $ascii['c'] = array(231);
        $ascii['d'] = array(208);
        $ascii['n'] = array(241);
        $ascii['y'] = array(253, 255);

        foreach ($ascii as $key=>$item) {
            $acentos = '';
            foreach ($item AS $codigo) $acentos .= chr($codigo);
            $troca[$key] = '/['.$acentos.']/i';
        }

        $string = preg_replace( array_values($troca), array_keys($troca), $string );

        // Slug?
        if ($slug) {
            // Troca tudo que não for letra ou número por um caractere ($slug)
            $string = preg_replace("/[^a-z0-9{$excecao}]/i", $slug, $string);

            // Tira os caracteres ($slug) repetidos
            $string = preg_replace('/' . $slug . '{2,}/i', $slug, $string);
            $string = trim($string, $slug);
        }

        return $string;

    }

    public static function enviaMail ($fromMail='', $fromName='', $destino, $name, $assunto, $mensagem, $cop_oculta = array(), $anexo ='', $nom_anexo ='', $bol_constantes = false){

        $mail = new PHPMailer(); // defaults to using php "mail()"
        $mail->IsSMTP (); // telling the class to use SMTP

        if ( !$bol_constantes ){

            $mail->Host =  SMTP_HOST ; // SMTP server
            $mail->SMTPDebug = 0; // enables SMTP debug information (for testing)
            $mail->SMTPAuth = true; // enable SMTP authentication
            $mail->Host = SMTP_HOST; // sets the SMTP server
            $mail->Port = SMTP_PORT; // set the SMTP port for the server
            $mail->Username = SMTP_USER ; // SMTP account username
            $mail->Password = SMTP_PASS ; // SMTP account password

            $mail->AddReplyTo ( (empty($fromMail) ? SMTP_USER : $fromMail ), ( empty($fromName) ? 'Sistema' : $fromName ) );
            $mail->SetFrom ( (empty($fromMail) ? SMTP_USER : $fromMail ), ( empty($fromName) ? 'Sistema' : $fromName ) );
        }else{

            $mail->Host =  SMTP_HOST_TESTE ; // SMTP server
            $mail->SMTPDebug = 0; // enables SMTP debug information (for testing)
            $mail->SMTPAuth = true; // enable SMTP authentication
            $mail->Host = SMTP_HOST_TESTE; // sets the SMTP server
            $mail->Port = SMTP_PORT_TESTE; // set the SMTP port for the server
            $mail->Username = SMTP_USER_TESTE ; // SMTP account username
            $mail->Password = SMTP_PASS_TESTE ; // SMTP account password

            $mail->AddReplyTo ( (empty($fromMail) ? SMTP_USER_TESTE : $fromMail ), ( empty($fromName) ? 'Sistema' : $fromName ) );
            $mail->SetFrom ( (empty($fromMail) ? SMTP_USER_TESTE : $fromMail ), ( empty($fromName) ? 'Sistema' : $fromName ) );
        }

        foreach( $cop_oculta as $email_copy )
            $mail->AddBCC( $email_copy );

        if(is_array($destino)){
            foreach($destino as $key => $val){
                $mail->AddAddress ( $val );
            }
        }
        else
            $mail->AddAddress ( $destino, (empty($name) ? $destino : $name) );

        $mail->Subject = $assunto;
        $mail->MsgHTML ( $mensagem );

        if( !empty( $anexo ) ) //Insere um anexo
            $mail->AddAttachment( $anexo, empty( $nom_anexo ) ? end( explode( DIRECTORY_SEPARATOR , $anexo ) ) : $nom_anexo );

        if ( $mail->Send () ) {
            Log::set ( 'sucesso ao enviar email;'.$assunto.';'.$fromMail.';'.$fromName.';'.(print_r($destino,1)).';'.$mail->ErrorInfo, Log::TYPE_ERROR, 'mail');
            return array ('resposta' => true, 'msg' => 'E-mail enviado!' );
        } else {
            Log::set ( 'erro ao enviar email;'.$assunto.';'.$fromMail.';'.$fromName.';'.(print_r($destino,1)).';'.$mail->ErrorInfo, Log::TYPE_ERROR, 'mail');
            return array ('resposta' => false, 'msg' => 'Erro ao enviar o e-mail!'.$mail->ErrorInfo);
        }
    }

    public static function enviaMailAnexo ($fromMail='', $fromName='', $destino, $name, $assunto, $mensagem, $cop_oculta = array(), $anexo ='', $nom_anexo =''){

        $mail = new PHPMailer(); // defaults to using php "mail()"
        $mail->IsSMTP (); // telling the class to use SMTP
        $mail->Host =  SMTP_HOST ; // SMTP server
        $mail->SMTPDebug = 0; // enables SMTP debug information (for testing)
        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->Host = SMTP_HOST; // sets the SMTP server
        $mail->Port = 587; // set the SMTP port for the server
        $mail->Username = SMTP_USER ; // SMTP account username
        $mail->Password = SMTP_PASS ; // SMTP account password

        foreach( $cop_oculta as $email_copy )
            $mail->AddBCC( $email_copy );

        $mail->AddReplyTo ( (empty($fromMail) ? SMTP_USER : $fromMail ), ( empty($fromName) ? 'Sistema' : $fromName ) );
        $mail->SetFrom ( (empty($fromMail) ? SMTP_USER : $fromMail ), ( empty($fromName) ? 'Sistema' : $fromName ) );
        if(is_array($destino)){
            foreach($destino as $key => $val){
                $mail->AddAddress ( $val );
            }
        }
        else
            $mail->AddAddress ( $destino, (empty($name) ? $destino : $name) );

        $mail->Subject = $assunto;
        $mail->MsgHTML ( $mensagem );

        if( !empty( $anexo ) ) //Insere um anexo
            $mail->AddAttachment( $anexo, empty( $nom_anexo ) ? end( explode( DIRECTORY_SEPARATOR , $anexo ) ) : $nom_anexo );

        if ( $mail->Send () ) {
            Log::set ( 'sucesso ao enviar email;'.$assunto.';'.$fromMail.';'.$fromName.';'.(print_r($destino,1)).';'.$mail->ErrorInfo, Log::TYPE_ERROR, 'mail');
            return array ('resposta' => true, 'msg' => 'E-mail enviado!' );
        } else {
            Log::set ( 'erro ao enviar email;'.$assunto.';'.$fromMail.';'.$fromName.';'.(print_r($destino,1)).';'.$mail->ErrorInfo, Log::TYPE_ERROR, 'mail');
            return array ('resposta' => false, 'msg' => 'Erro ao enviar o e-mail!'.$mail->ErrorInfo);
        }
    }

    public static function getPathTemplate($file,$modulo=''){

        if(isset($modulo) && !empty($modulo)){
            if(file_exists(FUSION_PUBLIC_TEMPLATES . DIRECTORY_SEPARATOR . $modulo . DIRECTORY_SEPARATOR . _TEMA_ . DIRECTORY_SEPARATOR . $file))
                return $modulo . DIRECTORY_SEPARATOR . _TEMA_ . DIRECTORY_SEPARATOR . $file;
            elseif(file_exists(FUSION_PUBLIC_TEMPLATES . DIRECTORY_SEPARATOR . $modulo . DIRECTORY_SEPARATOR . $file))
                return $modulo . DIRECTORY_SEPARATOR . $file;
        }
        if(file_exists(FUSION_PUBLIC_TEMPLATES . DIRECTORY_SEPARATOR . _TEMA_ . DIRECTORY_SEPARATOR . $file))
            return _TEMA_ . DIRECTORY_SEPARATOR . $file;
        else
            return $file;
    }

    /**
     * Retorna a url de acordo com a action passada
     * @param string $action - Ex: RelatorioCategoriaProduto
     * @return - relatorio-categoria-produto
     */
    public static function montarUrlReversa( $action = '' ){

        if( empty( $action ) )
            return false;

        $acao = '';

        for( $i=0; $i< strlen( $action ); $i++ ){

            if( ctype_upper( $action{$i} ) )
                $acao .= '-'.strtolower( $action{$i} );
            else
                $acao .= $action{$i};

        }

        return ltrim( $acao, '-' );

    }

    public static function validaNumeroMovel( $numero ){


        if ( strlen( $numero ) < 8 )
            $resposta = 11;
        elseif ( (strlen( $numero ) < 11) && (( substr( $numero, -8, -7) == 3 ) || ( substr( $numero, -8, -7) == 4 ) || ( substr( $numero, -8, -7) == 5 )  || ( substr( $numero, -8, -7) == 2 ) ) )
            $resposta = 11;
        elseif ( (strlen( $numero ) == 11) && (( substr( $numero, -9, -8) == 3 ) || ( substr( $numero, -9, -8) == 4 ) || ( substr( $numero, -9, -8) == 5 )  || ( substr( $numero, -9, -8) == 2 )) )
            $resposta = 11;
        else
            $resposta = 1;

        return $resposta;
    }

    public static function corrige9Digito ( $numero ) {

        if(strpos($numero, '55') === 0 && strlen( $numero ) > 11 ){
            $numero = substr( $numero, 2 );
        }

        if( in_array(substr( $numero , 0, 1), array ( 1,2 ) ) ){
            if( strlen( $numero ) == 11 ) {

                if( substr( $numero, 3, 2 ) == '78' || substr( $numero, 3, 2 ) == '77' )
                    return substr( $numero, 0, 2 ) . substr( $numero, 3 );
                else
                    return $numero;
            }
            elseif ( strlen( $numero ) == 10 ) {
                if( substr( $numero, 2, 2 ) == '78' || substr( $numero, 2, 2 ) == '77' )
                    return $numero;
                else//verificando se tem 10 digitos (ddd + numero)
                    return substr( $numero, 0, 2 ) . '9' . substr( $numero, 2 );
            }
        }

        return $numero;
    }

    public static function gerarSenha($tamanho = 8){
        $CaracteresAceitos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max = strlen($CaracteresAceitos)-1;
        $password = null;
        for($i=0; $i < $tamanho; $i++) {
            $password .= $CaracteresAceitos[mt_rand(0, $max)];
        }
        return $password;
    }

    public static function gerarHash($hash){
        return hash("sha512", $hash);
    }

    /**
     * Faz as verificacoes de senha segura (Especificadas pela algar)
     * @param string $usuario
     * @param unknown $senha
     * @param number $number
     * @return multitype:number multitype:string  |multitype:number multitype: |boolean
     */
    public static function verificarSenhaSegura($usuario = '', $senha, $number = 3)
    {
        try{

            $erros = array();

            //Validando o usuario
            if (empty($usuario)) {
                return array('success' => 0, 'erros' => array("USUARIO INVÁLIDO"));
            }

            $string = array();

            for ($i = 0; $i < strlen($usuario); $i++) {
                for ($j = 0; $j < $number; $j++) {
                    if ($i + $j >= strlen($usuario)) {
                        continue;
                    } else {
                        $string[$i][] = $usuario{$i + $j};
                    }
                }
            }

            $string_nova = "";
            foreach ($string as $value) {
                if (count($value) == $number) {
                    $string_nova .= "|" . implode("", $value);
                }
            }

            $string_nova = ltrim( $string_nova, "|" );
            $exp_usuario = '/('. $string_nova .')/';

            if( preg_match( $exp_usuario , $senha) )
                $erros[] = "Parte do nome do usuário está presente na senha";

            $number_verif = $number-1;
            //Verificando numeros sequenciais repetidos
            $exp = '/([a-zA-Z0-9])\1{'. $number_verif .',}/'; //Se tiver 3 caracteres repetidos é invalida

            if( preg_match( $exp, $senha ) )
                $erros[] = "Sequencia maxima de {$number} caracteres repetidos";

            $exp = array();

            //Numeros sequenciais crescentes de 3 em 3 Ex: (123|234|456)
            $exp[1] = Helpers::getExpRegNumeros( $number );

            //Numeros sequenciais decrescentes de 3 em 3 Ex: (654|543|321)
            $exp[2] = Helpers::getExpRegNumeros( $number, true );

            //Numeros sequenciais crescentes de 3 em 3 Ex: (abc|bcd|cde)
            $exp[3] = Helpers::getExpRegAlfabeto( $number );

            //Numeros sequenciais decrescentes de 3 em 3 Ex: (edc|dcb|cba)
            $exp[4] = Helpers::getExpRegAlfabeto( $number, true );

            //Validando todos de uma vez
            foreach( $exp as $key => $expressao ){

                if( preg_match( $expressao, $senha ) )
                    if( $key == 1 || $key == 2 )
                        $erros[] = "Numeros sequenciais encontrados";
                    else
                        $erros[] = "Caracteres sequenciais encontrados";
            }

            if( empty( $erros ) )
                return array( 'success' => 1, 'erros' => array() );

            return array( 'success' => 0, 'erros' => $erros );

        }catch( Exception $e ){
            return false;
        }
    }

    /**
     * Retorna a expressao regular para uma sequencia de caracteres seguidos crescentes ou decrescentes do alfabeto;
     * @param number $number
     * @param string $reverse
     * @author Fernando Henrique
     */
    public static function getExpRegNumeros($number = 3, $reverse = false)
    {
        if ($number <= 1 || !is_numeric($number)) {
            return false;
        }

        if (!$reverse) {

            $string = array();

            for ($i = 0; $i < 9; $i++) {
                for ($j = 0; $j < $number; $j++) {
                    if ($i + $j >= 10) {
                        continue;
                    } else {
                        $string[$i][] = $i + $j;
                    }
                }
            }

            $string_nova = "";
            foreach ($string as $value) {
                if (count($value) == $number) {
                    $string_nova .= "|" . implode("", $value);
                }
            }

            $string_nova = ltrim($string_nova, "|");
            $exp = '/('. $string_nova .')/';

        } else { //DESCENDENTE

            $string = array();

            for ($i = 9; $i > 0; $i--) {
                for ($j = 0; $j < $number; $j++) {
                    if ($i - $j < 0) {
                        continue;
                    } else {
                        $string [$i][] = $i - $j;
                    }
                }
            }

            $string_nova = "";
            foreach ($string as $value) {
                if (count($value) == $number) {
                    $string_nova .= "|" . implode("", $value);
                }
            }

            $string_nova = ltrim($string_nova, "|");
            $exp = '/('. $string_nova .')/';
        }

        return $exp;
    }

    /**
     * Retorna a expressao regular para uma sequencia de caracteres seguidos crescentes ou decrescentes do alfabeto;
     * @param number $number
     * @param string $reverse
     * @author Fernando Henrique
     */
    public static function getExpRegAlfabeto($number = 3, $reverse = false)
    {
        //NUMEROS SEQUENCIAIS ALFABETO DESCRESCENTES
        if ($number <= 1 || !is_numeric($number)) {
            return false;
        }

        $string = array();

        $alfabeto  = "abcdefghijklmnopqrstuvwxyz";

        if ($reverse) {
            $alfabeto = "zyxwvutsrqponmlkjihgfedcba";
        }

        for ($i = 0; $i < 26; $i++) {
            for ($j=0; $j<$number;$j++) {
                if ($i + $j >= 26) {
                    continue;
                } else {
                    $string [$i][] = $alfabeto{$i + $j};
                }
            }
        }

        $string_nova = "";
        foreach ($string as $value) {
            if (count($value) == $number) {
                $string_nova .= "|" . implode("", $value);
            }
        }

        $string_nova = ltrim($string_nova, "|");
        $exp = '/('. $string_nova .')/';

        return $exp;
    }

    /**
     * @param unknown_type $campo
     * @param unknown_type $casas
     * @return number
     */
    function formataCampoTxtDecimal($campo, $casas)
    {
        return floatval(substr($campo, 0, strlen($campo)-$casas). "." . substr($campo, -$casas));
    }

    public static function dateToTimestamp($dt, $format)
    {
        $dateTime = DateTime::createFromFormat($format, $dt, new DateTimeZone('America/Sao_Paulo'));
        if (!$dateTime) return '';
        return $dateTime->getTimestamp();
    }
}

?>