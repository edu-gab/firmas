<nav class="navbar navbar-expand-lg mb-5 bd-blue-800">
    <div class="container-fluid">
        <a href="<?=base_url()?>/home" class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none rounded-circle">
            <img class="navbar-brand rounded-circle d-none d-sm-none d-md-none d-lg-inline d-xl-inline" width="210" height="80" src="<?=base_url()?>/public/images/logo_tecnova_black_new.svg" alt="Logo Tecnova" style=" filter: contrast(40%) brightness(350%)   ;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="dropdown py-2 py-sm-2 mt-sm-auto ms-auto ms-sm-auto ms-md-auto ms-sm-0 flex-shrink-1 flex-sm-shrink-1 flex-md-shrink-1 d-lg-none d-xl-none py-md-2 mt-md-auto ms-md-0">
            <a href="#" class="d-flex d-sm-flex d-md-flex align-items-center dropdown-toggle active" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <?php if(isset($imgPath)): ?>
                    <img src="https://webapps.boschecuador.com/adminIntranet/galeria/tecnova/fotos/<?= esc($imgPath) ?>" alt="Perfil de usuario" width="58" height="58" class="rounded-circle bg-light">
                <?php else: ?>
                    <img src="<?=base_url()?>/public/images/icons/perfil.png" alt="Perfil de usuario" width="58" height="58" class="rounded-circle bg-light">
                <?php endif; ?>
            </a>
            <ul class="dropdown-menu dropdown-user text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="<?=base_url()?>/account/profile">Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?=base_url()?>/account/logout">Salir</a></li>
            </ul>
        </div>

        <div class="collapse px-2 pt-4 pt-sm-4 pt-md-4 py-lg-2 py-xl-2 navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav row row-cols-md-5 row-cols-lg-auto me-auto mb-2 mb-lg-0 pe-md-4">
                <!--Botón de inicio-->
                <li class="col nav-item">
                    <a class="nav-link text-light" aria-current="page" href="<?=base_url()?>/home">Inicio</a>
                </li>
            </ul>
        </div>

        <div class="dropdown py-sm-4 mt-sm-auto ms-auto ms-sm-auto ms-md-auto ms-sm-0 d-flex d-none d-sm-none d-md-none py-md-2 mt-md-auto ms-md-0 d-lg-inline d-xl-inline position-relative">
            <div class="d-flex position-relative">
                <p class="me-3 mt-3">Hola <?= esc($nombre) ?></p>
                <a href="#" class="d-flex d-sm-flex d-md-flex align-items-center link text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php if(isset($imgPath)): ?>
                        <img src="https://webapps.boschecuador.com/adminIntranet/galeria/tecnova/fotos/<?= esc($imgPath) ?>" alt="Perfil de usuario" width="58" height="58" class="rounded-circle bg-light">
                    <?php else: ?>
                        <img src="<?=base_url()?>/public/images/icons/perfil.png" alt="Perfil de usuario" width="58" height="58" class="rounded-circle bg-light">
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-user text-small shadow ms-auto position-absolute dropdown-menu-end" aria-labelledby="dropdownUser1" style="translate: 72% 5%">
                    <li><a class="dropdown-item" href="<?=base_url()?>/account/profile">Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?=base_url()?>/account/logout">Salir</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>