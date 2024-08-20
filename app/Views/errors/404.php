<!--Cargamos nuestros templates-->
<!--Header-->
<?= $this->include('templates/header') ?>
<style>
    .contenedor:before {
        content: ' ';
        display: block;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        z-index: -3;
        opacity: 0.75;
        background-image: url(<?=base_url()?>/public/images/fondo8.png);
        background-repeat: no-repeat;
        background-position: 100% 0;
        background-size: cover;
    }
</style>
<body style="background-color: #E4E7EC">

<div class="container position-relative p-5 mt-5">
    <div class="contenedor"></div>
    <div class="d-flex align-items-center pt-5 pb-5 mt-5 mb-5 justify-content-center" style="z-index: 10;">
        <div class="text-center">
            <h1 class="display-1 fw-bold">404</h1>
            <p class="fs-3 fw-bold"> <span class="text-danger">Opps!</span> P&aacute;gina no encontrada.</p>
            <p class="lead fw-bold">
                La p&aacute;gina que est&aacute; buscando no existe.
            </p>
            <a href="<?=base_url()?>" class="btn btn-primary">Ir a Inicio</a>
        </div>
    </div>
</div>

</body>

<!--Cargamos nuestros templates-->
<!--Footer-->
<?=$this->include('templates/footer')?>