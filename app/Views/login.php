<?php
$_base_url = base_url();
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="<?=$_base_url?>/public/favicon.ico"/>
        <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <!-- JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

        <!-- Sweet Alert library import-->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script src="<?=$_base_url?>/public/js/login.js"></script>
        <title>Sistema de Control (login)</title>

    </head>
    <body class = "d-flex justify-content-center align-items-center"
          style= "  min-height: 100vh;
                    background: url('<?=$_base_url?>/public/images/fondo1.png') repeat-y fixed center center;
                    background-size: cover;
                    -moz-background-size: cover;
                    -webkit-background-size: cover;
                    -o-background-size: cover;">
        <div class="mask" style="background-color: rgba(0, 0, 0, 0.4);">
            <div class="d-flex justify-content-center align-items-center min-vh-100 min-vw-100 ">
                <?php if(session()->getFlashdata('msg')):?>
                    <?= session()->getFlashdata('msg') ?>
                <?php endif;?>

                <?php echo form_open('/__/auth', ['class' => 'card w-auto h-auto px-md-4 px-lg-4 px-4 p-4 border-secondary shadow-lg', 'id'=>'login_form', 'name'=>'formulario', 'autocomplete'=>'off'])?>
                <!--<div class="form-group">-->
                    <div class="d-flex justify-content-center align-items-center m-1 border-bottom ">
                        <p class="text-md-center text-uppercase fs-5 fw-bolder">Iniciar Sesion</p>
                    </div>
                    <div class="container justify-content-center align-items-center mt-4 mb-4">
                        <div class="d-flex justify-content-center align-items-center">
                            <img src="<?=$_base_url?>/public/images/logo1.png" width="100" height="100" alt=""/>
                        </div>
                        <div class="d-flex justify-content-center align-items-center">
                            <img src="<?=$_base_url?>/public/images/banner.png" width="200" height="50" alt=""/>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                </svg>
                            </span>
                            <input name="user_name" type="text" class="form-control" id="user_name" placeholder="Ingrese su usuario">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="basic-addon1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                                    <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8zm4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5z"/>
                                    <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>
                            </span>

                            <input name="user_pass" type="password" class="form-control" id="user_pass" placeholder="Ingrese su contraseña" maxlength="60">
                        </div>
                        <small id="emailHelp" class="form-text text-muted m-4 opacity-75">No comparta su clave con nadie</small>
                    </div>

                    <div class="d-flex flex-row bd-highlight">
                        <button type="submit" class="btn btn-outline-dark m-3 p-2 w-50" onclick="valida_envia()">Ingresar</button>
                        <button type="reset" class="btn btn-outline-dark m-3 p-2 w-50">Limpiar</button>
                    </div>

                <?php echo form_close()?>
            </div>
        </div>
    </body>
</html>
