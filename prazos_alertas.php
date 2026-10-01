<?php

require_once 'conexao.php';

$diasAntecedencia = 3;
$mensagem = '';

/*
|--------------------------------------------------------------------------
| Salvar configuração
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_configuracao'])) {

    $diasInformados = filter_input(INPUT_POST, 'dias_antecedencia', FILTER_VALIDATE_INT);

    if ($diasInformados !== false && $diasInformados !== null && $diasInformados >= 1 && $diasInformados <= 30) {
        $diasAntecedencia = $diasInformados;
        $mensagem = "Configuração salva com sucesso.";
    } else {
        $mensagem = "Informe um número entre 1 e 30 dias.";
    }
}

/*
|--------------------------------------------------------------------------
| Datas utilizadas
|--------------------------------------------------------------------------
*/

$dataHoje = date('Y-m-d');

$dataLimite = date(
    'Y-m-d',
    strtotime("+{$diasAntecedencia} days")
);

/*
|--------------------------------------------------------------------------
| Pagamentos atrasados
|--------------------------------------------------------------------------
|
| A tabela prazos_pagamentos possui:
| funcionario_id
| descricao
| data_prazo
| status
|
*/

try {

    /*
    |--------------------------------------------------------------------------
    | Pagamentos atrasados
    |--------------------------------------------------------------------------
    */

    $sqlAtrasados = "
        SELECT
            p.id,
            p.funcionario_id,
            p.descricao,
            p.data_prazo,
            p.status,
            f.nome,
            f.cargo,
            COALESCE(pg.valor, 0) AS valor
        FROM prazos_pagamentos p
        INNER JOIN funcionarios f
            ON f.id = p.funcionario_id
        LEFT JOIN pagamentos pg
            ON pg.funcionario_id = p.funcionario_id
            AND pg.data_pagamento = p.data_prazo
        WHERE
            p.data_prazo < :hoje
            AND p.status = 'atrasado'
        ORDER BY p.data_prazo ASC
    ";

    $stmtAtrasados = $pdo->prepare($sqlAtrasados);
    $stmtAtrasados->execute([
        ':hoje' => $dataHoje
    ]);

    $pagamentosAtrasados = $stmtAtrasados->fetchAll(PDO::FETCH_ASSOC);


    /*
    |--------------------------------------------------------------------------
    | Próximos pagamentos
    |--------------------------------------------------------------------------
    */

    $sqlProximos = "
        SELECT
            p.id,
            p.funcionario_id,
            p.descricao,
            p.data_prazo,
            p.status,
            f.nome,
            f.cargo,
            COALESCE(pg.valor, 0) AS valor
        FROM prazos_pagamentos p
        INNER JOIN funcionarios f
            ON f.id = p.funcionario_id
        LEFT JOIN pagamentos pg
            ON pg.funcionario_id = p.funcionario_id
            AND pg.data_pagamento = p.data_prazo
        WHERE
            p.data_prazo >= :hoje
            AND p.data_prazo <= :limite
            AND p.status = 'pendente'
        ORDER BY p.data_prazo ASC
    ";

    $stmtProximos = $pdo->prepare($sqlProximos);

    $stmtProximos->execute([
        ':hoje' => $dataHoje,
        ':limite' => $dataLimite
    ]);

    $proximosPagamentos = $stmtProximos->fetchAll(PDO::FETCH_ASSOC);


    /*
    |--------------------------------------------------------------------------
    | Total de pagamentos do mês
    |--------------------------------------------------------------------------
    */

    $primeiroDiaMes = date('Y-m-01');
    $ultimoDiaMes = date('Y-m-t');

    $sqlTotalMes = "
        SELECT COALESCE(SUM(valor), 0)
        FROM pagamentos
        WHERE data_pagamento BETWEEN :inicio AND :fim
    ";

    $stmtTotalMes = $pdo->prepare($sqlTotalMes);

    $stmtTotalMes->execute([
        ':inicio' => $primeiroDiaMes,
        ':fim' => $ultimoDiaMes
    ]);

    $totalMes = (float) $stmtTotalMes->fetchColumn();


    /*
    |--------------------------------------------------------------------------
    | Quantidade de alertas ativos
    |--------------------------------------------------------------------------
    */

    $sqlAlertas = "
        SELECT COUNT(*)
        FROM alertas
        WHERE visualizado = 0
    ";

    $stmtAlertas = $pdo->query($sqlAlertas);
    $alertasAtivos = (int) $stmtAlertas->fetchColumn();


    /*
    |--------------------------------------------------------------------------
    | Histórico de alertas
    |--------------------------------------------------------------------------
    */

    $sqlHistorico = "
        SELECT
            id,
            mensagem,
            data_criacao,
            visualizado
        FROM alertas
        ORDER BY data_criacao DESC
        LIMIT 5
    ";

    $stmtHistorico = $pdo->query($sqlHistorico);
    $historicoAlertas = $stmtHistorico->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $pagamentosAtrasados = [];
    $proximosPagamentos = [];
    $historicoAlertas = [];

    $totalMes = 0;
    $alertasAtivos = 0;

    $mensagem = "Não foi possível carregar os dados: " . $e->getMessage();
}


/*
|--------------------------------------------------------------------------
| Funções auxiliares
|--------------------------------------------------------------------------
*/

function formatarMoeda($valor)
{
    return 'R$ ' . number_format(
        (float) $valor,
        2,
        ',',
        '.'
    );
}

function formatarData($data)
{
    if (!$data) {
        return '-';
    }

    return date('d/m/Y', strtotime($data));
}

function diasDeAtraso($data)
{
    $hoje = new DateTime(date('Y-m-d'));
    $prazo = new DateTime($data);

    return $prazo->diff($hoje)->days;
}

function diasRestantes($data)
{
    $hoje = new DateTime(date('Y-m-d'));
    $prazo = new DateTime($data);

    return $hoje->diff($prazo)->days;
}

function iniciais($nome)
{
    $partes = explode(' ', trim($nome));

    if (count($partes) === 1) {
        return strtoupper(substr($partes[0], 0, 2));
    }

    return strtoupper(
        substr($partes[0], 0, 1) .
        substr(end($partes), 0, 1)
    );
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Prazos e Alertas - AutoPeças</title>

    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0"
    >

    <link rel="stylesheet" href="css/prazos_alertas.css">

</head>

<body>

<div class="app">

    <!-- ========================================================= -->
    <!-- SIDEBAR -->
    <!-- ========================================================= -->

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
                GESTÃO DE PAGAMENTOS
            </div>

        </div>


        <nav class="nav-menu">

            <a href="dashboard.php" class="nav-link">

                <span class="material-symbols-outlined icon">
                    home
                </span>

                <span>Dashboard</span>

            </a>


            <a href="funcionarios.php" class="nav-link">

                <span class="material-symbols-outlined icon">
                    groups
                </span>

                <span>Funcionários</span>

            </a>


            <a href="pagamentos.php" class="nav-link">

                <span class="material-symbols-outlined icon">
                    payments
                </span>

                <span>Pagamentos</span>

            </a>


            <a href="prazos_alertas.php" class="nav-link active">

                <span class="material-symbols-outlined icon">
                    notifications
                </span>

                <span>Prazos e Alertas</span>

            </a>


            <a href="relatorio.php" class="nav-link">

                <span class="material-symbols-outlined icon">
                    bar_chart
                </span>

                <span>Relatórios</span>

            </a>


            <a href="#" class="nav-link">

                <span class="material-symbols-outlined icon">
                    settings
                </span>

                <span>Configurações</span>

            </a>

        </nav>


        <div class="nav-footer">

            <a href="login.php" class="nav-link logout">

                <span class="material-symbols-outlined icon">
                    logout
                </span>

                <span>Sair</span>

            </a>

        </div>

    </aside>


    <!-- ========================================================= -->
    <!-- CONTEÚDO -->
    <!-- ========================================================= -->

    <main class="main">

        <header class="topbar">

            <div class="menu-icon">

                <span class="material-symbols-outlined">
                    menu
                </span>

            </div>


            <div class="page-title">

                <h1>
                    Prazos e Alertas
                </h1>

                <p>
                    Acompanhe os prazos de pagamento e receba alertas importantes.
                </p>

            </div>


            <div class="topbar-actions">

                <button class="notification-button">

                    <span class="material-symbols-outlined">
                        notifications
                    </span>

                    <?php if ($alertasAtivos > 0): ?>

                        <span class="notification-badge">
                            <?= $alertasAtivos ?>
                        </span>

                    <?php endif; ?>

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


            <?php if ($mensagem): ?>

                <div class="mensagem">

                    <span class="material-symbols-outlined">
                        info
                    </span>

                    <?= htmlspecialchars($mensagem) ?>

                </div>

            <?php endif; ?>


            <!-- ================================================= -->
            <!-- CARDS -->
            <!-- ================================================= -->

            <section class="summary-grid">


                <!-- ATRASADOS -->

                <div class="summary-card card-danger">

                    <div class="summary-icon">

                        <span class="material-symbols-outlined">
                            error
                        </span>

                    </div>

                    <div class="summary-info">

                        <span class="summary-title">
                            Pagamentos em atraso
                        </span>

                        <strong>
                            <?= count($pagamentosAtrasados) ?>
                        </strong>

                        <small>
                            Pagamento vencido
                        </small>

                    </div>

                </div>


                <!-- PRÓXIMOS -->

                <div class="summary-card card-warning">

                    <div class="summary-icon">

                        <span class="material-symbols-outlined">
                            schedule
                        </span>

                    </div>

                    <div class="summary-info">

                        <span class="summary-title">
                            Próximos pagamentos
                        </span>

                        <strong>
                            <?= count($proximosPagamentos) ?>
                        </strong>

                        <small>
                            Nos próximos <?= $diasAntecedencia ?> dias
                        </small>

                    </div>

                </div>


                <!-- TOTAL -->

                <div class="summary-card">

                    <div class="summary-icon">

                        <span class="material-symbols-outlined">
                            calendar_month
                        </span>

                    </div>

                    <div class="summary-info">

                        <span class="summary-title">
                            Total de pagamentos no mês
                        </span>

                        <strong>
                            <?= formatarMoeda($totalMes) ?>
                        </strong>

                        <small>
                            Realizados e pendentes
                        </small>

                    </div>

                </div>


                <!-- ALERTAS -->

                <div class="summary-card">

                    <div class="summary-icon">

                        <span class="material-symbols-outlined">
                            notifications
                        </span>

                    </div>

                    <div class="summary-info">

                        <span class="summary-title">
                            Alertas ativos
                        </span>

                        <strong>
                            <?= $alertasAtivos ?>
                        </strong>

                        <small>
                            Configurados
                        </small>

                    </div>

                </div>

            </section>


            <!-- ================================================= -->
            <!-- ÁREA PRINCIPAL -->
            <!-- ================================================= -->

            <section class="dashboard-grid">


                <!-- ================================================= -->
                <!-- COLUNA ESQUERDA -->
                <!-- ================================================= -->

                <div class="left-column">


                    <!-- ============================================= -->
                    <!-- PAGAMENTOS ATRASADOS -->
                    <!-- ============================================= -->

                    <div class="panel">

                        <div class="panel-header">

                            <div class="panel-title danger-title">

                                <span class="material-symbols-outlined">
                                    error
                                </span>

                                <h2>
                                    Pagamentos em atraso
                                </h2>

                            </div>

                            <span class="counter danger-counter">

                                <?= count($pagamentosAtrasados) ?>

                                registro<?= count($pagamentosAtrasados) != 1 ? 's' : '' ?>

                            </span>

                        </div>


                        <div class="table-wrapper">

                            <table>

                                <thead>

                                    <tr>

                                        <th>Funcionário</th>
                                        <th>Cargo</th>
                                        <th>Valor</th>
                                        <th>Data prevista</th>
                                        <th>Dias de atraso</th>
                                        <th></th>

                                    </tr>

                                </thead>

                                <tbody>

                                <?php if (empty($pagamentosAtrasados)): ?>

                                    <tr>

                                        <td colspan="6" class="empty">

                                            <span class="material-symbols-outlined">
                                                check_circle
                                            </span>

                                            Nenhum pagamento em atraso.

                                        </td>

                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($pagamentosAtrasados as $pagamento): ?>

                                        <tr class="danger-row">

                                            <td>

                                                <div class="employee">

                                                    <div class="employee-avatar">
                                                        <?= iniciais($pagamento['nome']) ?>
                                                    </div>

                                                    <strong>
                                                        <?= htmlspecialchars($pagamento['nome']) ?>
                                                    </strong>

                                                </div>

                                            </td>


                                            <td>
                                                <?= htmlspecialchars($pagamento['cargo']) ?>
                                            </td>


                                            <td>
                                                <?= formatarMoeda($pagamento['valor']) ?>
                                            </td>


                                            <td>
                                                <?= formatarData($pagamento['data_prazo']) ?>
                                            </td>


                                            <td>

                                                <strong class="danger-text">

                                                    <?= diasDeAtraso($pagamento['data_prazo']) ?>

                                                    dias

                                                </strong>

                                            </td>


                                            <td>

                                                <a
                                                    href="pagamentos.php?pesquisa=<?= urlencode($pagamento['nome']) ?>"
                                                    class="details-button danger-button"
                                                >
                                                    Ver detalhes
                                                </a>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>


                    <!-- ============================================= -->
                    <!-- PRÓXIMOS PAGAMENTOS -->
                    <!-- ============================================= -->

                    <div class="panel">

                        <div class="panel-header">

                            <div class="panel-title warning-title">

                                <span class="material-symbols-outlined">
                                    schedule
                                </span>

                                <h2>
                                    Próximos pagamentos
                                </h2>

                            </div>

                            <span class="counter warning-counter">

                                <?= count($proximosPagamentos) ?>

                                registro<?= count($proximosPagamentos) != 1 ? 's' : '' ?>

                            </span>

                        </div>


                        <div class="table-wrapper">

                            <table>

                                <thead>

                                    <tr>

                                        <th>Funcionário</th>
                                        <th>Cargo</th>
                                        <th>Valor</th>
                                        <th>Data prevista</th>
                                        <th>Dias restantes</th>
                                        <th></th>

                                    </tr>

                                </thead>


                                <tbody>

                                <?php if (empty($proximosPagamentos)): ?>

                                    <tr>

                                        <td colspan="6" class="empty">

                                            <span class="material-symbols-outlined">
                                                event_available
                                            </span>

                                            Nenhum pagamento próximo.

                                        </td>

                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($proximosPagamentos as $pagamento): ?>

                                        <tr>

                                            <td>

                                                <div class="employee">

                                                    <div class="employee-avatar">
                                                        <?= iniciais($pagamento['nome']) ?>
                                                    </div>

                                                    <strong>
                                                        <?= htmlspecialchars($pagamento['nome']) ?>
                                                    </strong>

                                                </div>

                                            </td>


                                            <td>
                                                <?= htmlspecialchars($pagamento['cargo']) ?>
                                            </td>


                                            <td>
                                                <?= formatarMoeda($pagamento['valor']) ?>
                                            </td>


                                            <td>
                                                <?= formatarData($pagamento['data_prazo']) ?>
                                            </td>


                                            <td>

                                                <strong class="warning-text">

                                                    <?= diasRestantes($pagamento['data_prazo']) ?>

                                                    dias

                                                </strong>

                                            </td>


                                            <td>

                                                <a
                                                    href="pagamentos.php?pesquisa=<?= urlencode($pagamento['nome']) ?>"
                                                    class="details-button"
                                                >
                                                    Ver detalhes
                                                </a>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>


                    <!-- ============================================= -->
                    <!-- HISTÓRICO -->
                    <!-- ============================================= -->

                    <div class="panel">

                        <div class="panel-header">

                            <div class="panel-title">

                                <span class="material-symbols-outlined">
                                    notifications
                                </span>

                                <h2>
                                    Histórico de alertas
                                </h2>

                            </div>

                        </div>


                        <div class="table-wrapper">

                            <table>

                                <thead>

                                    <tr>

                                        <th>Data</th>
                                        <th>Tipo</th>
                                        <th>Descrição</th>

                                    </tr>

                                </thead>


                                <tbody>

                                <?php if (empty($historicoAlertas)): ?>

                                    <tr>

                                        <td colspan="3" class="empty">

                                            <span class="material-symbols-outlined">
                                                notifications_none
                                            </span>

                                            Nenhum alerta registrado.

                                        </td>

                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($historicoAlertas as $alerta): ?>

                                        <tr>

                                            <td>

                                                <?= date(
                                                    'd/m/Y H:i',
                                                    strtotime($alerta['data_criacao'])
                                                ) ?>

                                            </td>


                                            <td>

                                                <span class="history-type">

                                                    <span class="material-symbols-outlined">
                                                        <?= $alerta['visualizado']
                                                            ? 'info'
                                                            : 'error'
                                                        ?>
                                                    </span>

                                                    <?= $alerta['visualizado']
                                                        ? 'Lembrete'
                                                        : 'Alerta'
                                                    ?>

                                                </span>

                                            </td>


                                            <td>

                                                <?= htmlspecialchars(
                                                    $alerta['mensagem']
                                                ) ?>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- COLUNA DIREITA -->
                <!-- ================================================= -->

                <aside class="right-column">


                    <!-- ============================================= -->
                    <!-- CONFIGURAÇÃO -->
                    <!-- ============================================= -->

                    <div class="panel configuration-panel">

                        <div class="panel-title">

                            <span class="material-symbols-outlined">
                                settings
                            </span>

                            <h2>
                                Configuração de alertas
                            </h2>

                        </div>


                        <p class="configuration-description">

                            Defina com quantos dias de antecedência deseja
                            receber os alertas de próximos pagamentos.

                        </p>


                        <form method="POST">

                            <label for="dias_antecedencia">

                                Dias de antecedência

                            </label>


                            <div class="number-input">

                                <input
                                    type="number"
                                    id="dias_antecedencia"
                                    name="dias_antecedencia"
                                    value="<?= $diasAntecedencia ?>"
                                    min="1"
                                    max="30"
                                    required
                                >

                                <span>
                                    dias
                                </span>

                            </div>


                            <button
                                type="submit"
                                name="salvar_configuracao"
                                class="save-button"
                            >

                                <span class="material-symbols-outlined">
                                    save
                                </span>

                                SALVAR CONFIGURAÇÕES

                            </button>

                        </form>


                        <!-- TIPOS -->

                        <div class="alert-types">

                            <div class="alert-types-title">

                                <span class="material-symbols-outlined">
                                    notifications
                                </span>

                                Tipos de alertas

                            </div>


                            <div class="check-item">

                                <span class="material-symbols-outlined">
                                    check_circle
                                </span>

                                Pagamentos em atraso

                            </div>


                            <div class="check-item">

                                <span class="material-symbols-outlined">
                                    check_circle
                                </span>

                                Próximos pagamentos

                            </div>


                            <div class="check-item">

                                <span class="material-symbols-outlined">
                                    check_circle
                                </span>

                                Alertas personalizados

                            </div>

                        </div>


                        <!-- AVISO -->

                        <div class="privacy-warning">

                            <span class="material-symbols-outlined">
                                shield
                            </span>

                            <p>
                                As notificações são enviadas apenas para
                                usuários autorizados e mantêm a
                                confidencialidade das informações financeiras.
                            </p>

                        </div>

                    </div>

                </aside>

            </section>

        </section>

    </main>

</div>

</body>

</html>