<?php

require_once 'conexao.php';
require_once 'crud.php';


$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


$pagamento = read(
    $pdo,
    "pagamentos",
    "id = $id"
);


if (!$pagamento) {

    die("Pagamento não encontrado.");

}


if (isset($_POST['excluir'])) {

    delete(
        $pdo,
        "pagamentos",
        "id = $id"
    );


    header("Location: pagamentos.php");

    exit();

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

    <title>Excluir pagamento</title>

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
                    Excluir pagamento
                </h1>

                <p>
                    Confirmação de exclusão
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
                        Excluir pagamento
                    </h2>

                    <p>
                        Confirme se deseja realmente excluir este pagamento.
                    </p>

                </div>

            </div>



            <div class="confirmation-card">

                <h2>
                    Confirmar exclusão
                </h2>


                <p>
                    Deseja realmente excluir este pagamento?
                </p>


                <p>

                    <strong>
                        ID:
                    </strong>

                    #<?= $pagamento['id'] ?>

                </p>


                <p>

                    <strong>
                        Valor:
                    </strong>

                    R$
                    <?= number_format(
                        $pagamento['valor'],
                        2,
                        ',',
                        '.'
                    ) ?>

                </p>



                <div class="confirmation-actions">


                    <form method="POST">

                        <button
                            type="submit"
                            name="excluir"
                            class="btn-primary"
                        >

                            <span class="material-symbols-outlined">
                                delete
                            </span>

                            Sim, excluir

                        </button>

                    </form>


                    <a
                        href="pagamentos.php"
                        class="btn-outline"
                    >

                        Cancelar

                    </a>

                </div>

            </div>

        </section>

    </main>

</div>

</body>

</html>