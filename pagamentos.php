<?php

require_once 'conexao.php';
require_once 'crud.php';


$pesquisa = isset($_GET['pesquisa'])
    ? trim($_GET['pesquisa'])
    : "";


$status_filtro = isset($_GET['status'])
    ? $_GET['status']
    : "";


$sql = "
    SELECT
        pagamentos.id,
        funcionarios.nome AS funcionario,
        funcionarios.cargo,
        pagamentos.valor,
        pagamentos.data_pagamento,
        pagamentos.tipo,
        pagamentos.forma_pagamento,
        pagamentos.status
    FROM pagamentos
    INNER JOIN funcionarios
        ON pagamentos.funcionario_id = funcionarios.id
";


$condicoes = [];
$parametros = [];


if ($pesquisa !== "") {

    $condicoes[] = "
        (
            funcionarios.nome LIKE :pesquisa
            OR funcionarios.cargo LIKE :pesquisa
        )
    ";

    $parametros[':pesquisa'] = "%" . $pesquisa . "%";

}


if (
    $status_filtro !== "" &&
    in_array(
        $status_filtro,
        ['pendente', 'pago', 'atrasado']
    )
) {

    $condicoes[] = "pagamentos.status = :status";

    $parametros[':status'] = $status_filtro;

}


if (count($condicoes) > 0) {

    $sql .= " WHERE " . implode(" AND ", $condicoes);

}


$sql .= "
    ORDER BY pagamentos.data_pagamento DESC
";


$stmt = $pdo->prepare($sql);

$stmt->execute($parametros);

$pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);



/* RESUMOS */

$total_pagamentos = $pdo
    ->query("SELECT COUNT(*) FROM pagamentos")
    ->fetchColumn();


$valor_total = $pdo
    ->query("SELECT COALESCE(SUM(valor), 0) FROM pagamentos")
    ->fetchColumn();


$total_pagos = $pdo
    ->query("
        SELECT COUNT(*)
        FROM pagamentos
        WHERE status = 'pago'
    ")
    ->fetchColumn();


$total_pendentes = $pdo
    ->query("
        SELECT COUNT(*)
        FROM pagamentos
        WHERE status = 'pendente'
    ")
    ->fetchColumn();


function nomeTipo($tipo)
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


function nomeForma($forma)
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

    <title>Pagamentos - Autopeças</title>

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


    <!-- SIDEBAR -->

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

            <a
                href="index.php"
                class="nav-link"
            >

                <span class="material-symbols-outlined icon">
                    home
                </span>

                Dashboard

            </a>


            <a
                href="funcionarios.php"
                class="nav-link"
            >

                <span class="material-symbols-outlined icon">
                    groups
                </span>

                Funcionários

            </a>


            <a
                href="pagamentos.php"
                class="nav-link active"
            >

                <span class="material-symbols-outlined icon">
                    payments
                </span>

                Pagamentos

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <span class="material-symbols-outlined icon">
                    event
                </span>

                Prazos e Alertas

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <span class="material-symbols-outlined icon">
                    bar_chart
                </span>

                Relatórios

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <span class="material-symbols-outlined icon">
                    settings
                </span>

                Configurações

            </a>

        </nav>


        <div class="nav-footer">

            <a
                href="#"
                class="nav-link logout"
            >

                <span class="material-symbols-outlined icon">
                    logout
                </span>

                Sair

            </a>

        </div>

    </aside>



    <!-- MAIN -->

    <main class="main">


        <!-- TOPBAR -->

        <header class="topbar">

            <div class="page-title">

                <h1>
                    Pagamentos
                </h1>

                <p>
                    Gestão de pagamentos dos funcionários
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



        <!-- CONTENT -->

        <section class="content">


            <div class="content-header">

                <div>

                    <h2>
                        Pagamentos
                    </h2>

                    <p>
                        Registre, visualize e gerencie os pagamentos dos funcionários.
                    </p>

                </div>


                <a
                    href="cadastrar_pagamento.php"
                    class="btn-primary"
                >

                    <span class="material-symbols-outlined">
                        add
                    </span>

                    Novo pagamento

                </a>

            </div>



            <!-- FILTROS -->

            <form
                method="GET"
                class="filters-bar"
            >

                <div class="search-field">

                    <span class="material-symbols-outlined">
                        search
                    </span>

                    <input
                        type="text"
                        name="pesquisa"
                        value="<?= htmlspecialchars($pesquisa) ?>"
                        placeholder="Pesquisar funcionário ou cargo..."
                    >

                </div>


                <div class="select-field">

                    <label>
                        Status
                    </label>

                    <select name="status">

                        <option value="">
                            Todos
                        </option>

                        <option
                            value="pago"
                            <?= $status_filtro == 'pago'
                                ? 'selected'
                                : '' ?>
                        >
                            Pago
                        </option>

                        <option
                            value="pendente"
                            <?= $status_filtro == 'pendente'
                                ? 'selected'
                                : '' ?>
                        >
                            Pendente
                        </option>

                        <option
                            value="atrasado"
                            <?= $status_filtro == 'atrasado'
                                ? 'selected'
                                : '' ?>
                        >
                            Atrasado
                        </option>

                    </select>

                </div>


                <button
                    type="submit"
                    class="btn-outline"
                >

                    <span class="material-symbols-outlined">
                        filter_alt
                    </span>

                    Filtrar

                </button>


                <a
                    href="pagamentos.php"
                    class="btn-outline"
                >

                    <span class="material-symbols-outlined">
                        refresh
                    </span>

                    Limpar

                </a>

            </form>



            <!-- CARDS -->

            <div class="cards">


                <div class="card">

                    <div class="card-icon">

                        <span class="material-symbols-outlined">
                            payments
                        </span>

                    </div>


                    <div class="card-info">

                        <div class="card-label">
                            Total de pagamentos
                        </div>

                        <div class="card-value">
                            <?= $total_pagamentos ?>
                        </div>

                    </div>

                </div>



                <div class="card">

                    <div class="card-icon">

                        <span class="material-symbols-outlined">
                            account_balance_wallet
                        </span>

                    </div>


                    <div class="card-info">

                        <div class="card-label">
                            Valor total
                        </div>

                        <div class="card-value">
                            R$
                            <?= number_format(
                                $valor_total,
                                2,
                                ',',
                                '.'
                            ) ?>
                        </div>

                    </div>

                </div>



                <div class="card">

                    <div class="card-icon">

                        <span class="material-symbols-outlined">
                            check_circle
                        </span>

                    </div>


                    <div class="card-info">

                        <div class="card-label">
                            Pagamentos pagos
                        </div>

                        <div class="card-value">
                            <?= $total_pagos ?>
                        </div>

                    </div>

                </div>



                <div class="card">

                    <div class="card-icon">

                        <span class="material-symbols-outlined">
                            schedule
                        </span>

                    </div>


                    <div class="card-info">

                        <div class="card-label">
                            Pagamentos pendentes
                        </div>

                        <div class="card-value red">
                            <?= $total_pendentes ?>
                        </div>

                    </div>

                </div>

            </div>



            <!-- TABELA -->

            <div class="table-card">

                <div class="table-scroll">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    Funcionário
                                </th>

                                <th>
                                    Cargo
                                </th>

                                <th>
                                    Valor
                                </th>

                                <th>
                                    Data
                                </th>

                                <th>
                                    Tipo
                                </th>

                                <th>
                                    Forma
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Ações
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php if (count($pagamentos) > 0) { ?>


                                <?php foreach ($pagamentos as $pagamento) { ?>

                                    <tr>


                                        <td class="id-col">
                                            #<?= $pagamento['id'] ?>
                                        </td>


                                        <td>
                                            <?= htmlspecialchars(
                                                $pagamento['funcionario']
                                            ) ?>
                                        </td>


                                        <td>
                                            <?= htmlspecialchars(
                                                $pagamento['cargo']
                                            ) ?>
                                        </td>


                                        <td>

                                            <strong>
                                                R$
                                                <?= number_format(
                                                    $pagamento['valor'],
                                                    2,
                                                    ',',
                                                    '.'
                                                ) ?>
                                            </strong>

                                        </td>


                                        <td>
                                            <?= date(
                                                'd/m/Y',
                                                strtotime(
                                                    $pagamento['data_pagamento']
                                                )
                                            ) ?>
                                        </td>


                                        <td>
                                            <?= nomeTipo(
                                                $pagamento['tipo']
                                            ) ?>
                                        </td>


                                        <td>
                                            <?= nomeForma(
                                                $pagamento['forma_pagamento']
                                            ) ?>
                                        </td>


                                        <td>

                                            <span
                                                class="status-pill status-<?= $pagamento['status'] ?>"
                                            >

                                                <?= ucfirst(
                                                    $pagamento['status']
                                                ) ?>

                                            </span>

                                        </td>


                                        <td>

                                            <div class="actions">


                                                <a
                                                    href="visualizar_pagamento.php?id=<?= $pagamento['id'] ?>"
                                                    class="action-btn"
                                                    title="Visualizar"
                                                >

                                                    <span class="material-symbols-outlined">
                                                        visibility
                                                    </span>

                                                </a>


                                                <a
                                                    href="editar_pagamento.php?id=<?= $pagamento['id'] ?>"
                                                    class="action-btn"
                                                    title="Editar"
                                                >

                                                    <span class="material-symbols-outlined">
                                                        edit
                                                    </span>

                                                </a>


                                                <a
                                                    href="excluir_pagamento.php?id=<?= $pagamento['id'] ?>"
                                                    class="action-btn delete"
                                                    title="Excluir"
                                                >

                                                    <span class="material-symbols-outlined">
                                                        delete
                                                    </span>

                                                </a>

                                            </div>

                                        </td>

                                    </tr>

                                <?php } ?>


                            <?php } else { ?>

                                <tr>

                                    <td
                                        colspan="9"
                                        class="empty"
                                    >

                                        Nenhum pagamento encontrado.

                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>


                <div class="table-footer">

                    <span>

                        Exibindo
                        <?= count($pagamentos) ?>
                        pagamento(s)

                    </span>

                </div>

            </div>

        </section>

    </main>

</div>

</body>

</html>