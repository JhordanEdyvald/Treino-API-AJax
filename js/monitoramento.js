function chamarAjax(){
    $.ajax({
        url: "./lib/atualizarDados.php",
        type: "GET",
        success: function(data){                
            for(let i in data.info){
                var nomeAgente = data.info[i].agente;
                $('#cartoes').append(`<div class="cartao ${data.info[i].status}container">
                                    <div class='titulosInfo'><span class='statusPrint'>${data.info[i].status}</span><br>${data.info[i].agente}<br>RAMAL | ${data.info[i].nome}</div>
                                    <span class="${data.info[i].status} icone-posicao"></span>
                                    <p class="infoCalls">${data.statusLigacao[nomeAgente]}</p>
                                  </div>`)
            }
        },
        error: function(){
            console.log("Errouu!")
        }
    });
}
//SISTEMA PARA ATUALIZAR APENAS O AJAX E NÃO PÁGINA TODA.
chamarAjax();
setInterval(()=>{
    chamarAjax();
    
    if($('.cartao').get(0)){
        $('.cartao').remove();
    }
},10000);