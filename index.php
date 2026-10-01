<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard - Autopeças</title>

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
                class="nav-link active"
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
                class="nav-link"
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


        <header class="topbar">

            <div class="page-title">

                <h1>
                    Dashboard
                </h1>

                <p>
                    Visão geral do sistema
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
                        Sistema de Autopeças
                    </h2>

                    <p>
                        Gestão financeira e controle de pagamentos.
                    </p>

                </div>

            </div>



            <div class="cards">


                <div class="card">

                    <div class="card-icon">

                        <span class="material-symbols-outlined">
                            payments
                        </span>

                    </div>

                    <div class="card-info">

                        <div class="card-label">
                            Pagamentos
                        </div>

                        <div class="card-value">
                            Gestão
                        </div>

                    </div>

                </div>



                <div class="card">

                    <div class="card-icon">

                        <span class="material-symbols-outlined">
                            groups
                        </span>

                    </div>

                    <div class="card-info">

                        <div class="card-label">
                            Funcionários
                        </div>

                        <div class="card-value">
                            Cadastro
                        </div>

                    </div>

                </div>



                <div class="card">

                    <div class="card-icon">

                        <span class="material-symbols-outlined">
                            event
                        </span>

                    </div>

                    <div class="card-info">

                        <div class="card-label">
                            Prazos
                        </div>

                        <div class="card-value">
                            Controle
                        </div>

                    </div>

                </div>



                <div class="card">

                    <div class="card-icon">

                        <span class="material-symbols-outlined">
                            bar_chart
                        </span>

                    </div>

                    <div class="card-info">

                        <div class="card-label">
                            Relatórios
                        </div>

                        <div class="card-value">
                            Consultas
                        </div>

                    </div>

                </div>

            </div>



            <div class="table-card">

                <div class="content-header"
                     style="padding: 20px; margin-bottom: 0;">

                    <div>

                        <h2 style="margin-bottom: 4px;">
                            Menu principal
                        </h2>

                        <p>
                            Acesse as principais áreas do sistema.
                        </p>

                    </div>

                </div>


                <div style="
                    display:flex;
                    flex-wrap:wrap;
                    gap:12px;
                    padding:0 20px 25px;
                ">


                    <a
                        href="pagamentos.php"
                        class="btn-primary"
                    >

                        <span class="material-symbols-outlined">
                            payments
                        </span>

                        Pagamentos

                    </a>


                    <a
                        href="funcionarios.php"
                        class="btn-outline"
                    >

                        <span class="material-symbols-outlined">
                            groups
                        </span>

                        Funcionários

                    </a>

                </div>

            </div>

        </section>

    </main>

</div>

</body>

</html>