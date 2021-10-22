<link href='https://fonts.googleapis.com/css?family=Libre Franklin' rel='stylesheet'>
<title><?= $titulo_trabalho ?></title>

<?php

$media = 0;

$media += $nota_orientador;

?>

<style>
    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 12px;
        color: black;
    }

    .mt-2 {
        margin-top: 2rem;
    }

    .mt-1 {
        margin-top: 1rem;
    }

    .mt-1-5 {
        margin-top: 1.5rem;
    }

    .text-danger {
        color: red;
    }

    section {
        position: absolute;
        top: 0;
        right: 0;
    }

    .section {
        position: absolute;
        z-index: 999;
    }

    .cabecalho {
        font-family: 'Libre Franklin';
        width: 30em;
        font-size: 16px;
        top: 7.7rem;
        left: 26rem;
        font-weight: bold;
    }

    .cabecalho p {
        display: inline-block;
        max-width: 50%;
    }

    .informacao {
        top: 16.3rem;
        left: 20rem;
    }

    .aluno {
        top: 0px;
        left: 0px;
    }

    .titulo {
        height: 4%;
        width: 90%;
    }

    .orientador {
        margin-top: 2.1rem;
        margin-left: 0.51rem;
    }

    .banca_examinadora {
        font-size: 14px;
        top: 31.5rem;
        left: 18rem;
    }

    .primeira_avaliacao {
        font-family: 'Libre Franklin';
        font-size: 15px;
        top: 44.3rem;
        left: 23rem;
        width: 10%;
    }
</style>

<section class="page-1">
    <img src="./resources/img/page_1.jpg">
</section>

<div class="cabecalho section">
    <p class="datetime"><?= $data ?>, <span class="text-danger"><?= $horario ?></span></p>
</div>

<div class="informacao section">
    <p class="aluno"><?= $discente ?></p>
    <p class="titulo mt-1-5"><?= $titulo_trabalho ?></p>
    <p class="orientador"><?= $orientador ?></p>
</div>

<div class="banca_examinadora section">
    <p class="avaliador_1"><?= $orientador ?></p>
    <?php
    foreach ($avaliadores as $nome => $avaliador) {
    ?>
        <p class="mt-2"><?= $nome ?></p>
    <?php
    }
    ?>
</div>

<div class="primeira_avaliacao section">
    <p class="nota_1"><?= $nota_orientador ?></p>
    <?php
    foreach ($avaliadores as $nome => $nota) {
        $media += $nota;
    ?>
        <p class="mt-1 nota_avaliador"><?= isset($nota) ? $nota : '-' ?></p>
    <?php
    }
    ?>
    <p class="mt-1 media"><?= round($media / 3, 1) ?></p>
</div>

<pagebreak></pagebreak>

<section class="page-2">
    <img src="./resources/img/page_2.jpg">
</section>