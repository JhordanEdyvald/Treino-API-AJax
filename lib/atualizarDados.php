<?php
include('./conexao.php');
include('./ramais.php');

$classeRamais = new ramais(file('ramais'), file('filas'));
$objPrincipal = array(
    'info' => $classeRamais->processo(),
    'statusLigacao' => $classeRamais->pegarStatusInfo()
);
echo json_encode($objPrincipal);
//echo json_encode($classeRamais->processo());
//echo json_encode($classeRamais->pegarStatusInfo());
$objInfoRamais = $classeRamais->processo();

function compararInfo($id, $registro, $atualJson){
        include('./conexao.php');
        $comparacao = mysqli_query($con,'SELECT '.$registro.' from operadores WHERE matricula = '.$id.'');
        $comparacaoFetch = mysqli_fetch_array($comparacao)[$registro];
        if($comparacaoFetch != $atualJson){
            mysqli_query($con,'UPDATE operadores SET '.$registro.' = "'.$atualJson.'" WHERE matricula = '.$id.'');
        }
}

foreach($objInfoRamais as $item){
    $consultaMatricula = mysqli_query($con,'SELECT matricula from operadores WHERE matricula = '.$item['nome'].'');
    $consultaMatrFetch = mysqli_fetch_array($consultaMatricula)['matricula'];
    compararInfo($item['nome'] ,'numberRamal' ,$item['ramal']);
    compararInfo($item['nome'] ,'agenteName' ,$item['agente']);
    compararInfo($item['nome'] ,'IP' ,$item['ip']);
    compararInfo($item['nome'] ,'statusRamal' ,$item['status']);
    compararInfo($item['nome'] ,'conectado' ,$item['online']);
    mysqli_query($con,'INSERT INTO operadores (matricula, numberRamal, agenteName, IP, statusRamal, conectado) VALUES ('.$item['nome'].','.$item['ramal'].',"'.$item['agente'].'","'.$item['ip'].'","'.$item['status'].'",'.($item['online'] == false ? 0 : $item['online']).' )');
}
