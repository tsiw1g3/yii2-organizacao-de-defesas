<!-- <style>
    .report {
        padding: 2rem;        
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        height: 100%;     
        z-index: 1;   
    }    

    .header {
        text-align: center;
    }
    .uppercase {
        text-transform: uppercase;
    }
    .header h1 {
        font-size: 16px;
        line-height: 8px;
    }
    .header h2 {
        text-align: center;
        font-style: italic;
        font-size: 12px;
        font-weight: normal;
        margin: 0;
    }    

    .content {
        padding: 0 3rem;
    }
    .content h2 {
        font-weight: bold;
        text-transform: uppercase;
        text-align: center;
        margin: 6rem 0;
    }
    .content p {
        font-size: 18px;
        text-align: justify;
        line-height: 27px;
        font-weight: 500;
        margin: 4rem 0;
    }
    .content p.date {
        text-align: center;
    }

    .footer {
        text-align: center;        
    }
    .footer p {
        font-size: 18px;
        font-weight: 500;
        margin: 0;
    }
    .footer p.leader {
        font-weight: bold;
    }
    .footer p.date {
        margin: 4rem 0;
    }

    .marca-dagua-container {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;        
        text-align: center;
    }
    img.marca-dagua {
        opacity: 0.2;
        height: 50%;
        margin-top: 50%;
    }
    .brasao-container {
        position: absolute;
        top: 80px;
        left: 40px;
    }
    .logo-ic-container {
        position: absolute;
        top: 70px;
        right: 40px;
    }
    img.brasao {
        width: 120px;
    }
</style>
<?php
    $mes_extenso = array(
        '01' => 'Janeiro',
        '02' => 'Fevereiro',
        '03' => 'Marco',
        '04' => 'Abril',
        '05' => 'Maio',
        '06' => 'Junho',
        '07' => 'Julho',
        '08' => 'Agosto',
        '09' => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro'
    );

    $lista_pronomes_orientador = array(
        0 => "o professor",
        1 => "a professora",
        2 => "e professore"
    );
    
    $lista_pronomes_aluno = array(
        0 => "do aluno",
        1 => "da aluna",
        2 => "de alune"
    );

    $month = $mes_extenso[date("m", strtotime($data))];
    $year = date("Y", strtotime($data));
    $day = date("d", strtotime($data));

    $teacher_role = $lista_pronomes_orientador[$pronome_orientador];
    $student_role = $lista_pronomes_aluno[$pronome_aluno];
?>
<div class="marca-dagua-container">
    <img class="marca-dagua" src="<?= $_ENV["BASE_URL"] ?>/resources/img/brasao-ufba.png">
</div>
<div class="brasao-container">
    <img class="brasao" src="<?= $_ENV["BASE_URL"] ?>/resources/img/brasao-ufba.png">        
</div>
<div class="logo-ic-container">
    <img class="brasao" src="<?= $_ENV["BASE_URL"] ?>/resources/img/instituto_de_computacao.png">        
</div>
<section class="report">
    <section class="header">
        <h1>MINISTÉRIO DA EDUCAÇÃO</h1>
        <h1>UNIVERSIDADE FEDERAL DA BAHIA</h1>
        <h1>INSTITUTO DE COMPUTAÇÃO</h1>
        <h1 class="uppercase">COLEGIADO DO CURSO DE <?= $nome_curso ?></h1>
        <h2>IC - INSTITUTO DE COMPUTAÇÃO/UFBA</h2>
        <h2>Avenida Milton Santos, s/n - Campus de Ondina, PAF 2</h2>
        <h2>CEP: 40.170-110 Salvador-Bahia</h2>
    </section>
    <section class="content">
        <h2>Declaração</h2>
        <p class="text">
            Declaro para os devidos fins, que <?= $teacher_role ?> <strong><?= $orientador ?></strong>,
            participou como membro do Projeto Final de Curso II <?= $student_role ?> <?= $aluno ?>,
            discente do Curso de Bacharelado em <?= $nome_curso ?>
            da UFBA, intitulado “<?= $titulo_trabalho ?>”, apresentado em <?= $day ?> de <?= $month ?> de <?= $year ?>.
        </p>
    </section>
    <section class="footer">
        <p class="date">Salvador, <?= $day ?> de <?= $month ?> de <?= $year ?>.</p>
        <p class="leader"><?= $coordenacao ?></p>
        <p><?= $cargo_coordenacao ?></p>
        <p>UFBA</p>
    </section>
</section> -->
<h1>
    Teste de geração estilo.
</h1>