<?php

require_once 'conexao.php';
require_once 'crud.php';


$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


$sql = "
    SELECT
        pagamentos.*,
        funcionarios.nome AS funcionario,
        funcionarios.cargo,
        usuarios.nome AS usuario
    FROM pagamentos

    INNER JOIN funcionarios
        ON pagamentos.funcionario_id = funcionarios.id

    INNER JOIN usuarios
        ON pagamentos.usuario_id = usuarios.id

    WHERE pagamentos.id = $id
";


$stmt = $pdo->query($sql);

$pagamento = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$pagamento) {

    die("Pagamento não encontrado.");

}


function nomeTipoVisualizar($tipo)
{
    $nomes = [
        'salario' => 'Salário',
        'bonus' => 'Bônus',
        'adiantamento' => 'Adiantamento',
        'vale' => 'Vale',
        'outro' => 'Outro'
    ];

    return $nomes[$tipo] ?? ucfirst($tipo);
}


function nomeFormaVisualizar($forma)
{
    $nomes = [
        'pix' => 'PIX',
        'transferencia' => 'Transferência',
        'dinheiro' => 'Dinheiro',
        'cartao' => 'Cartão',
        'boleto' => 'Boleto',
        'outro' => 'Outro'
    ];

    return $nomes[$forma] ?? ucfirst($forma);
}

?>


<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Visualizar pagamento</title>

    <link
        rel="stylesheet"
        href="pagamentos.css"
    >

    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"
    >

</head>


<body>

<div class="app">


    <aside class="sidebar">

        <div class="brand">

            <div class="brand-icon">

                <span class="material-symbols-outlined">
                    build
                </span>

            </div>

            <div class="brand-name">
                AUTOPEÇAS
            </div>

            <div class="brand-sub">
                SISTEMA DE GESTÃO
            </div>

        </div>


        <nav class="nav-menu">

            <a href="index.php" class="nav-link">

                <span class="material-symbols-outlined icon">
                    home
                </span>

                Dashboard

            </a>


            <a href="funcionarios.php" class="nav-link">

                <span class="material-symbols-outlined icon">
                    groups
                </span>

                Funcionários

            </a>


            <a href="pagamentos.php" class="nav-link active">

                <span class="material-symbols-outlined icon">
                    payments
                </span>

                Pagamentos

            </a>


            <a href="#" class="nav-link">

                <span class="material-symbols-outlined icon">
                    event
                </span>

                Prazos e Alertas

            </a>


            <a href="#" class="nav-link">

                <span class="material-symbols-outlined icon">
                    bar_chart
                </span>

                Relatórios

            </a>


            <a href="#" class="nav-link">

                <span class="material-symbols-outlined icon">
                    settings
                </span>

                Configurações

            </a>

        </nav>


        <div class="nav-footer">

            <a href="#" class="nav-link logout">

                <span class="material-symbols-outlined icon">
                    logout
                </span>

                Sair

            </a>

        </div>

    </aside>



    <main class="main">


        <header class="topbar">

            <div class="page-title">

                <h1>
                    Visualizar pagamento
                </h1>

                <p>
                    Detalhes do pagamento
                </p>

            </div>


            <div class="topbar-actions">

                <button class="icon-btn">

                    <span class="material-symbols-outlined">
                        notifications
                    </span>

                    <span class="badge">
                        3
                    </span>

                </button>


                <div class="user-menu">

                    <div class="avatar">

                        <span class="material-symbols-outlined">
                            person
                        </span>

                    </div>

                    <div>

                        <div class="user-name">
                            Gestor Financeiro
                        </div>

                        <div class="user-role">
                            Administrador
                        </div>

                    </div>

                    <span class="material-symbols-outlined">
                        expand_more
                    </span>

                </div>

            </div>

        </header>



        <section class="content">


            <div class="content-header">

                <div>

                    <h2>
                        Visualizar pagamento
                    </h2>

                    <p>
                        Confira todas as informações deste pagamento.
                    </p>

                </div>


                <a
                    href="pagamentos.php"
                    class="btn-outline"
                >

                    <span class="material-symbols-outlined">
                        arrow_back
                    </span>

                    Voltar

                </a>

            </div>



            <div class="details-card">


                <div class="detail-row">

                    <span class="detail-label">
                        ID
                    </span>

                    <span class="detail-value">
                        #<?= $pagamento['id'] ?>
                    </span>

                </div>


                <div class="detail-row">

                    <span class="detail-label">
                        Funcionário
                    </span>

                    <span class="detail-value">
                        <?= htmlspecialchars(
                            $pagamento['funcionario']
                        ) ?>
                    </span>

                </div>


                <div class="detail-row">

                    <span class="detail-label">
                        Cargo
                    </span>

                    <span class="detail-value">
                        <?= htmlspecialchars(
                            $pagamento['cargo']
                        ) ?>
                    </span>

                </div>


                <div class="detail-row">

                    <span class="detail-label">
                        Valor
                    </span>

                    <span class="detail-value">

                        R$
                        <?= number_format(
                            $pagamento['valor'],
                            2,
                            ',',
                            '.'
                        ) ?>

                    </span>

                </div>


                <div class="detail-row">

                    <span class="detail-label">
                        Data
                    </span>

                    <span class="detail-value">

                        <?= date(
                            'd/m/Y',
                            strtotime(
                                $pagamento['data_pagamento']
                            )
                        ) ?>

                    </span>

                </div>


                <div class="detail-row">

                    <span class="detail-label">
                        Tipo
                    </span>

                    <span class="detail-value">

                        <?= nomeTipoVisualizar(
                            $pagamento['tipo']
                        ) ?>

                    </span>

                </div>


                <div class="detail-row">

                    <span class="detail-label">
                        Forma de pagamento
                    </span>

                    <span class="detail-value">

                        <?= nomeFormaVisualizar(
                            $pagamento['forma_pagamento']
                        ) ?>

                    </span>

                </div>


                <div class="detail-row">

                    <span class="detail-label">
                        Status
                    </span>

                    <span class="detail-value">

                        <span
                            class="status-pill status-<?= $pagamento['status'] ?>"
                        >

                            <?= ucfirst(
                                $pagamento['status']
                            ) ?>

                        </span>

                    </span>

                </div>


                <div class="detail-row">

                    <span class="detail-label">
                        Usuário responsável
                    </span>

                    <span class="detail-value">

                        <?= htmlspecialchars(
                            $pagamento['usuario']
                        ) ?>

                    </span>

                </div>


                <div class="detail-row">

                    <span class="detail-label">
                        Observação
                    </span>

                    <span class="detail-value">

                        <?= htmlspecialchars(
                            $pagamento['observacao'] ?? ''
                        ) ?>

                    </span>

                </div>


            </div>

        </section>

    </main>

</div>

</body>

</html>