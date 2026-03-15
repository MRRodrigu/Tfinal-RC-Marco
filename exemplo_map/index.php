<?php
/**
 * @file index.php
 * @brief Página principal com UI baseada no template EMFMP (Themewagon) e Mapa Interativo.
 */
require __DIR__ . '/auth.php';
require_login(); // Força o login
require __DIR__ . '/db.php';

$userNome = $_SESSION['user_nome'] ?? 'Utilizador';
$userTipo = $_SESSION['user_tipo'] ?? 'normal';
$isAdmin = is_admin();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <title>GeoDados - Mapa Interativo</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,800" rel="stylesheet">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css?v=2">
    <link rel="stylesheet" type="text/css" href="assets/css/jquery.mCustomScrollbar.css">

    <!-- Inclusão da framework Bootstrap por CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    
    <!-- Font-awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet & Clusters -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css">

    <!-- jQuery & Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom CSS para o Mapa (adaptado do mapa.css original, ajustado para coexistir) -->
    <style>
        #map {
            width: 100%;
            height: calc(100vh - 200px); /* Ajuste de altura dentro do wrapper do PCoded */
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            z-index: 1; /* Para não sobrepor a navbar dropdown do PCoded */
        }
        .search-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .search-box {
            position: relative;
            flex: 1;
            min-width: 200px;
        }
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        .search-box input {
            width: 100%;
            padding: 10px 10px 10px 40px;
            border-radius: 20px;
            border: 1px solid #ccc;
            outline: none;
        }
        .filter-select {
            padding: 10px 20px;
            border-radius: 20px;
            border: 1px solid #ccc;
            outline: none;
            min-width: 200px;
        }
        /* Ajuste do PopUp do marcador para Leaflet */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
        }
    </style>
</head>

<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <!-- ... outros anéis do loader se necessário, mas abreviaremos para não encher muito -->
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->

    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            <nav class="navbar header-navbar pcoded-header">
                <div class="navbar-wrapper">
                    <div class="navbar-logo">
                        <a class="mobile-menu" id="mobile-collapse" href="#!">
                            <i class="ti-menu"></i>
                        </a>
                        <a href="index.php">
                            <!-- Utilizamos o logo do EMFMP copiado -->
                            <img class="img-fluid" src="assets/images/auth/Logo-EMFMP-small.png" alt="Theme-Logo" style="height: 40px;" />
                        </a>
                        <a class="mobile-options">
                            <i class="ti-more"></i>
                        </a>
                    </div>

                    <div class="navbar-container container-fluid">
                        <ul class="nav-left">
                            <li>
                                <div class="sidebar_toggle"><a href="javascript:void(0)"><i class="ti-menu"></i></a></div>
                            </li>
                            <li>
                                <a href="#!" onclick="javascript:toggleFullScreen()">
                                    <i class="ti-fullscreen"></i>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav-right">
                            <li class="user-profile header-notification">
                                <a href="#!">
                                    <span><?= htmlspecialchars($userNome) ?> (<?= $isAdmin ? 'Admin' : 'Utilizador' ?>)</span>
                                    <i class="ti-angle-down"></i>
                                </a>
                                <ul class="show-notification profile-notification">
                                    <li>
                                        <a href="logout.php">
                                            <i class="ti-layout-sidebar-left"></i> Terminar Sessão
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <!-- Menu Lateral / Sidebar -->
                    <nav class="pcoded-navbar">
                        <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
                        <div class="pcoded-inner-navbar main-menu">
                            
                            <div class="pcoded-navigatio-lavel" data-i18n="nav.category.navigation">Navegação</div>
                            <ul class="pcoded-item pcoded-left-item">
                                <li class="active">
                                    <a href="index.php">
                                        <span class="pcoded-micon"><i class="ti-map-alt"></i><b>M</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.dash.main">Mapa Interativo</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                                <!-- Adicionar Local -->
                                <li class="">
                                    <a href="#" id="btn-add-location" data-bs-toggle="modal" data-bs-target="#modal-add-local">
                                        <span class="pcoded-micon"><i class="ti-location-pin"></i><b>A</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.dash.main">Adicionar Local</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                            </ul>

                            <?php if ($isAdmin): ?>
                                <div class="pcoded-navigatio-lavel" data-i18n="nav.category.forms">Administração</div>
                                <ul class="pcoded-item pcoded-left-item">
                                    <li class="">
                                        <a href="#">
                                            <span class="pcoded-micon"><i class="ti-user"></i><b>U</b></span>
                                            <span class="pcoded-mtext" data-i18n="nav.dash.main">Gerir Utilizadores</span>
                                            <span class="pcoded-mcaret"></span>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="#">
                                            <span class="pcoded-micon"><i class="ti-layers"></i><b>C</b></span>
                                            <span class="pcoded-mtext" data-i18n="nav.dash.main">Categorias</span>
                                            <span class="pcoded-mcaret"></span>
                                        </a>
                                    </li>
                                </ul>
                            <?php endif; ?>

                            <!-- Sair -->
                            <div class="pcoded-navigatio-lavel" data-i18n="nav.category.forms">Conta</div>
                            <ul class="pcoded-item pcoded-left-item">
                                <li class="">
                                    <a href="logout.php">
                                        <span class="pcoded-micon"><i class="ti-shift-left"></i><b>S</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.dash.main">Terminar Sessão</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                            </ul>

                        </div>
                    </nav>

                    <!-- Conteúdo Principal -->
                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">
                                    
                                    <div class="page-body">
                                        <!-- Barra superior para search / filtros -->
                                        <div class="search-filters">
                                            <div class="search-box">
                                                <i class="fa-solid fa-earth-europe"></i>
                                                <input type="text" id="search-country" placeholder="Pesquisar país...">
                                            </div>
                                            <div class="search-box">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                                <input type="text" id="search-city" placeholder="Pesquisar cidade...">
                                            </div>
                                            <select id="filter-category" class="filter-select">
                                                <option value="">Todas as Categorias</option>
                                                <!-- Preenchido via JS -->
                                            </select>
                                        </div>

                                        <!-- Container do Mapa -->
                                        <div id="map"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form: Adicionar Local (substitui a lógica de popup antiga para usar Bootstrap Modal) -->
    <div class="modal fade" id="modal-add-local" tabindex="-1" aria-labelledby="modalAddLocalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddLocalLabel">Adicionar Local</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- O form_local original pode precisar de ajustes, mas vamos incluí-lo -->
                    <?php include 'form_local.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts do PCoded Theme -->
    <script type="text/javascript" src="assets/js/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="assets/js/popper.js/popper.min.js"></script>
    
    <script type="text/javascript" src="assets/js/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="assets/js/modernizr/modernizr.js"></script>
    <script type="text/javascript" src="assets/js/modernizr/css-scrollbars.js"></script>
    <!-- classie js -->
    <script type="text/javascript" src="assets/js/classie/classie.js"></script>

    <!-- Custom js scripts do UI -->
    <script type="text/javascript" src="assets/js/script.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script src="assets/js/demo-12.js"></script>
    <script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>

    <!-- Scripts do Leaflet -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    
    <!-- Lógica da Aplicação Mapa -->
    <script>
        const loggedUserId = <?= json_encode($_SESSION['user_id']) ?>;
        const isAdmin = <?= json_encode($isAdmin) ?>;

        // Custom function to open sharing modal
        function openShareEmailModal(id, nome) {
            let email = prompt(`Partilhar "${nome}" por email.\nInsira o email de destino:`);
            if(email && email.trim() !== "") {
                fetch('enviar_email.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}&email_destino=${encodeURIComponent(email)}`
                })
                .then(r => r.json())
                .then(data => {
                    alert(data.mensagem);
                })
                .catch(err => {
                    alert('Erro ao enviar email.');
                    console.error(err);
                });
            }
        }
    </script>
    <script src="js/mapa.js"></script>
    
    <script>
        // Correção visual do form que possa ser embutido (ex: `form_local.php`)
        // O original era um popup absolute. Ao colocar num modal, garantimos que o form ganhe submit sem o "fechar popup" custom.
        const originalBtnClose = document.getElementById('btn-close-form');
        if(originalBtnClose) {
            originalBtnClose.style.display = 'none'; // Já temos o btn-close do modal
        }
        
        // Refresh do tamanho do mapa quando a sidebar colapsar, para evitar grey areas no Leaflet
        document.getElementById('mobile-collapse').addEventListener('click', () => {
            setTimeout(() => { if(window.mapInstance) window.mapInstance.invalidateSize(); }, 300);
        });
    </script>

</body>
</html>
