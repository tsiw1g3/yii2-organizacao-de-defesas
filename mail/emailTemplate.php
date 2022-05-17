<?php 
$data_hora = explode(' ',$banca["data_realizacao"]);
$timestamp = strtotime($data_hora[0]);
 
// Creating new date format from that timestamp
$data_defesa = date("d/m/Y", $timestamp);
?>
<p><?= $content ?></p>
<span>Aluno: <?= $banca["autor"] ?> </span><br>
<span>Orientador: <?= $orientador ?> </span><br>
<span>Titulo: <?= $banca["titulo_trabalho"] ?> </span><br>
<span>Data: <?= $data_defesa ?> </span><br>
<span>Hora/Local: <?= $data_hora[1]?> - <?= $banca['tipo_banca'] == "remoto" ? "Virtual - " : ""?><?= $banca["local"] ?> </span><br>
<span>Resumo: <?= $banca["resumo"] ?> </span><br>
<span>Palavras-Chave: <?= $banca["palavras_chave"] ?> </span><br>
<?php if(!$avaliadores): ?>
<span>Banca avaliadora pendente</span>
<?php else: ?>
<span>Banca avaliadora:</span><br>
<?php foreach($avaliadores as $avaliador):?>
<span><?php echo $avaliador; ?> </span><br>
<?php endforeach; ?>
<?php endif;?>
<br>
<br>
<a href="<?= $invite_google ?>"> Adicionar este evento ao Calendário Google </a>
