<style>
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
        '09' => 'Novembro',
        '10' => 'Setembro',
        '11' => 'Outubro',
        '12' => 'Dezembro'
    );

    $lista_pronomes_orientador = array(
        0 => "o professor",
        1 => "a professora",
        2 => "e professore"
    );
    
    $lista_pronomes_aluno = array(
        0 => "aluno",
        1 => "aluna",
        2 => "alune"
    );
    
    $lista_cursos = array(
        "BCC" => "Bacharelado em Ciência da Computação",
        "BSI" => "Bacharelado em Sistemas de Informação",
    );

    $month = $mes_extenso[date("m", strtotime($data))];
    $year = date("Y", strtotime($data));
    $day = date("d", strtotime($data));

    $teacher_role = $lista_pronomes_orientador[$pronome_orientador];
    $student_role = $lista_pronomes_aluno[$pronome_aluno];
    $course_name = $lista_cursos[$curso];
?>
<div class="marca-dagua-container">
    <img class="marca-dagua" src="<?= $_ENV["BASE_URL"] ?>/resources/img/brasao-ufba.png">
</div>
<div class="brasao-container">
    <img class="brasao" src="<?= $_ENV["BASE_URL"] ?>/resources/img/brasao-ufba.png">        
</div>
<section class="report">
    <section class="header">
        <h1>MINISTÉRIO DA EDUCAÇÃO</h1>
        <h1>UNIVERSIDADE FEDERAL DA BAHIA</h1>
        <h1>INSTITUTO DE COMPUTAÇÃO</h1>
        <h1>COLEGIADO DO CURSO DE CIÊNCIA DA COMPUTAÇÃO</h1>
        <h2
            >Av. Ademar de Barros s/n – Campus Universitário de Ondina, Ondina –
            Salvador- Bahia</h2
        >
        <h2>CEP 40170-110 Tel: (071) 3283-6337 /6336</h2>
    </section>
    <section class="content">
        <h2>Declaração</h2>
        <p class="text">
            Declaro para os devidos fins, que <?= $teacher_role ?> <strong><?= $orientador ?></strong>,
            orientou e participou da banca de defesa do Projeto Final II de <?= $aluno ?>
            <?= $student_role ?> do Curso de <?= $course_name ?>
            da UFBA, intitulado “<?= $titulo_trabalho ?>”, que
            ocorreu em <?= $day ?> de <?= $month ?> de <?= $year ?>.
        </p>
    </section>
    <section class="footer">
        <p class="date">Salvador, <?= $day ?> de <?= $month ?> de <?= $year ?>.</p>
        <p class="leader">Maycon Leone Maciel Peixoto</p>
        <p>Coordenador do Curso de Bacharelado em</p>
        <p>Ciência da Computação</p>
        <p>UFBA</p>
    </section>
</section>
