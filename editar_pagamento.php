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


$funcionarios = readAll(
    $pdo,
    "funcionarios",
    "status = 'ativo'"
);


$usuarios = readAll(
    $pdo,
    "usuarios",
    "ativo = 1"
);


if (isset($_POST['editar'])) {

    $funcionario_id = $_POST['funcionario_id'];
    $usuario_id = $_POST['usuario_id'];
    $valor = $_POST['valor'];
    $data_pagamento = $_POST['data_pagamento'];
    $tipo = $_POST['tipo'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $status = $_POST['status'];
    $observacao = $_POST['observacao'];


    $dados = [

        "funcionario_id" => $funcionario_id,

        "usuario_id" => $usuario_id,

        "valor" => $valor,

        "data_pagamento" => $data_pagamento,

        "tipo" => $tipo,

        "forma_pagamento" => $forma_pagamento,

        "status" => $status,

        "observacao" => $observacao

    ];


    update(
        $pdo,
        "pagamentos",
        $dados,
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

    <title>Editar pagamento</title>

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
                    Editar pagamento
                </h1>

                <p>
                    Alteração dos dados do pagamento
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
                        Editar pagamento
                    </h2>

                    <p>
                        Altere as informações do pagamento.
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



            <div class="form-card">

                <form method="POST">

                    <div class="form-grid">


                        <div class="form-group">

                            <label>
                                Funcionário
                            </label>

                            <select
                                name="funcionario_id"
                                required
                            >

                                <?php foreach ($funcionarios as $funcionario) { ?>

                                    <option
                                        value="<?= $funcionario['id'] ?>"
                                        <?= $funcionario['id'] == $pagamento['funcionario_id']
                                            ? 'selected'
                                            : '' ?>
                                    >

                                        <?= htmlspecialchars(
                                            $funcionario['nome']
                                        ) ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>



                        <div class="form-group">

                            <label>
                                Usuário responsável
                            </label>

                            <select
                                name="usuario_id"
                                required
                            >

                                <?php foreach ($usuarios as $usuario) { ?>

                                    <option
                                        value="<?= $usuario['id'] ?>"
                                        <?= $usuario['id'] == $pagamento['usuario_id']
                                            ? 'selected'
                                            : '' ?>
                                    >

                                        <?= htmlspecialchars(
                                            $usuario['nome']
                                        ) ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>



                        <div class="form-group">

                            <label>
                                Valor
                            </label>

                            <input
                                type="number"
                                name="valor"
                                step="0.01"
                                min="0"
                                value="<?= htmlspecialchars($pagamento['valor']) ?>"
                                required
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                Data do pagamento
                            </label>

                            <input
                                type="date"
                                name="data_pagamento"
                                value="<?= htmlspecialchars($pagamento['data_pagamento']) ?>"
                                required
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                Tipo
                            </label>

                            <select name="tipo">

                                <option
                                    value="salario"
                                    <?= $pagamento['tipo'] == 'salario'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Salário
                                </option>

                                <option
                                    value="bonus"
                                    <?= $pagamento['tipo'] == 'bonus'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Bônus
                                </option>

                                <option
                                    value="adiantamento"
                                    <?= $pagamento['tipo'] == 'adiantamento'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Adiantamento
                                </option>

                                <option
                                    value="vale"
                                    <?= $pagamento['tipo'] == 'vale'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Vale
                                </option>

                                <option
                                    value="outro"
                                    <?= $pagamento['tipo'] == 'outro'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Outro
                                </option>

                            </select>

                        </div>



                        <div class="form-group">

                            <label>
                                Forma de pagamento
                            </label>

                            <select name="forma_pagamento">

                                <option
                                    value="pix"
                                    <?= $pagamento['forma_pagamento'] == 'pix'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    PIX
                                </option>

                                <option
                                    value="transferencia"
                                    <?= $pagamento['forma_pagamento'] == 'transferencia'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Transferência
                                </option>

                                <option
                                    value="dinheiro"
                                    <?= $pagamento['forma_pagamento'] == 'dinheiro'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Dinheiro
                                </option>

                                <option
                                    value="outro"
                                    <?= $pagamento['forma_pagamento'] == 'outro'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Outro
                                </option>

                            </select>

                        </div>



                        <div class="form-group">

                            <label>
                                Status
                            </label>

                            <select name="status">

                                <option
                                    value="pendente"
                                    <?= $pagamento['status'] == 'pendente'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Pendente
                                </option>

                                <option
                                    value="pago"
                                    <?= $pagamento['status'] == 'pago'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Pago
                                </option>

                                <option
                                    value="atrasado"
                                    <?= $pagamento['status'] == 'atrasado'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Atrasado
                                </option>

                            </select>

                        </div>



                        <div class="form-group full">

                            <label>
                                Observação
                            </label>

                            <textarea
                                name="observacao"
                            ><?= htmlspecialchars($pagamento['observacao'] ?? '') ?></textarea>

                        </div>

                    </div>



                    <div class="form-actions">

                        <button
                            type="submit"
                            name="editar"
                            class="btn-primary"
                        >

                            <span class="material-symbols-outlined">
                                save
                            </span>

                            Salvar alterações

                        </button>


                        <a
                            href="pagamentos.php"
                            class="btn-outline"
                        >
                            Cancelar
                        </a>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>

</body>

</html>