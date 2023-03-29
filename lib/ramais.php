<?php
header("Content-type: application/json; charset=utf-8");
/*
 Você deverá transformar em uma classe
 */

class ramais {

    public $ramais;
    public $filas;
        

    public $status_ramais = array();

    function __construct($pRamais, $pFilas){
        $this->ramais = $pRamais;
        $this->filas = $pFilas;
    }

    function processo() {

        //FOREACH PARA INDEXAR O RAMAL COM O NOME DO OPERADOR;
        $operadores_ramais = array();
        foreach($this->filas as $linhas){
            if(strstr($linhas,'SIP/')){
                $linha = explode(' ',$linhas);
                $arr = array_filter($linha);
                $ramal = explode('/', array_values($arr)[0]);
                $operador = end($linha);
                $operadores_ramais[$ramal[1]] = $operador;
            }
        }
        //FOREACH PARA MOSTRAR OS STAUS DOS RAMAIS NO ARRAY $status_ramais;
        //SENDO O INDEX DO ARRAY O NÚMERO DO RAMAL, E O RESULTADO DO STATUS DO RAMAL O VALOR DO INDEX;
        foreach($this->filas as $linhas){
            if(strstr($linhas,'SIP/')){
                if(strstr($linhas,'(Ring)')){
                    $linha = explode(' ', trim($linhas));
                    list($tech,$ramal) = explode('/',$linha[0]);
                    $status_ramais[$ramal] = array('status' => 'chamando');
                }
                if(strstr($linhas,'(In use)')){            
                    $linha = explode(' ', trim($linhas));
                    list($tech,$ramal) = explode('/',$linha[0]);
                    $status_ramais[$ramal] = array('status' => 'ocupado');    
                }
                if(strstr($linhas,'(Not in use)') AND strstr($linhas,'(paused)') == false){
                    $linha = explode(' ', trim($linhas));
                    list($tech,$ramal)  = explode('/',$linha[0]);
                    $status_ramais[$ramal] = array('status' => 'disponivel');    
                }
                //STATUS QUE ESTAVA FALTANDO E DANDO PROBLEMA NO CÓDIGO ADICIONADO!
                if(strstr($linhas,'(Unavailable)')){
                    $linha = explode(' ', trim($linhas));
                    list($tech,$ramal)  = explode('/',$linha[0]);
                    $status_ramais[$ramal] = array('status' => 'indisponivel');    
                }
                if(strstr($linhas,'(paused)')){
                    $linha = explode(' ', trim($linhas));
                    list($tech,$ramal)  = explode('/',$linha[0]);
                    $status_ramais[$ramal] = array('status' => 'pausa');    
                }
            }
        }
        $info_ramais = array();
        foreach($this->ramais as $linhas){
            $linha = array_filter(explode(' ',$linhas));
            $arr = array_values($linha);
            if(trim($arr[1]) == '(Unspecified)' AND trim($arr[4]) == 'UNKNOWN'){        
                list($name,$username) = explode('/',$arr[0]);
                $info_ramais[$name] = array(
                    'nome' => $name,
                    'ip' => 'sem IP',
                    'agente' => trim($operadores_ramais[$name]),
                    'ramal' => $username,
                    'online' => false,
                    'status' => $status_ramais[$name]['status']
                );
            }
            if(isset($arr[5]) AND trim($arr[5]) == "OK"){
                list($name,$username) = explode('/',$arr[0]);
                $info_ramais[$name] = array(
                    'nome' => $name,
                    'ip' => $arr[1],
                    'agente' => trim($operadores_ramais[$name]),
                    'ramal' => $username,
                    'online' => true,
                    'status' => $status_ramais[$name]['status']
                );
            }
        }
        return $info_ramais;
    }
    function pegarStatusInfo(){
        $info_ligacoes_dados = array();
        foreach($this->filas as $linhas){
            if(strstr($linhas,'SIP/')){
                $linhaArray = explode(' ',$linhas);
                $operador = trim(end($linhaArray));
                $tratamento_status_as = strstr($linhas,') as');
                $tratamento_status_has = strstr($linhas,') has');
                if($tratamento_status_as){
                    $info_ligacoes = strstr($tratamento_status_as,'as');
                    $resultado = 'Ainda não recebeu nenhuma chamada!';
                    $info_ligacoes_dados[$operador] = $resultado;
                }
                if($tratamento_status_has){
                    $info_ligacoes = strstr($tratamento_status_has,'has');
                    $info_ligacoes_tratamento = trim(str_replace($operador,'',$info_ligacoes));
                    if($info_ligacoes_tratamento == 'has taken no calls yet'){
                        $resultado = 'Ainda não recebeu nenhuma chamada!';
                        $info_ligacoes_dados[$operador] = $resultado;
                    }
                    if(strstr($info_ligacoes_tratamento,'(last was')){
                        $ultima_ligacao_array = explode(' ',$info_ligacoes_tratamento);
                        $resultado = $operador.' recebeu '.$ultima_ligacao_array[2].' chamadas.<br> Sua última ligação foi a:<br> '.$ultima_ligacao_array[6].' horas atrás';
                        $info_ligacoes_dados[$operador] = $resultado;
                    }
                }
            }
        }
        return $info_ligacoes_dados;
    }
}
