<html lang="es">
    <head>
        <meta charset="UTF-8" http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta name="description" content="Sistema de gestión de firmas digitales">
        <meta name="keywords" content="HTML, CSS, JavaScript">
        <meta name="author" content="Ronny Garc&iacute;a, @rsgarcia0203">

        <!--Icon-->
        <link rel="shortcut icon" href="<?=base_url()?>/public/favicon.ico"/>

        <title> Intranet - Directorio de Firmas </title>

        <!-- Bootstrap 5 library import-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <link href="https://getbootstrap.com/docs/5.2/assets/css/docs.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

        <!-- Sweet Alert library import-->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- JQuery library import -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>

        <!-- DataTables library import-->
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
        <link href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
        <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css">
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>

        <!-- Mis estilos -->
        <link rel="stylesheet" href="<?=base_url()?>/public/css/styles.css">
    </head>
    <script>const base_url = `<?=base_url()?>`;</script>
    <script src="<?=base_url()?>/public/js/principal.js"></script>

    <body style="background-color: #E4E7EC">
        <?=$this->include('templates/navbar')?>

        <div class="container mb-4" style="font:Verdana, Arial, Helvetica, sans-serif;">
            <!--Barra de búsqueda simple-->
            <div class="card mt-4 mb-2">
                <div class="card-header p-3 bd-blue-800 text-center">
                    <h3 id="modal_title">Directorio de Empleados</h3>
                </div>
                <div class="card-body text-center align-items-center align-content-center align-content-sm-center p-4">
                    <form class="navbar-search d-flex" autocomplete="off">
                        <input id="search_value" class="form-control me-0 fs-5" type="text" placeholder=""
                               aria-label="Search" maxlength="50">
                        <button id="clear_btn" class="btn btn-outline-danger fs-5 ms-3" type="reset">Limpiar</button>
                    </form>
                </div>
            </div>
        </div>

        <section class="main container" width="85%">
            <table id="firmas" class="datatable table table-responsive table-hover bg-light" style="font:Verdana, Arial, Helvetica, sans-serif; font-size:12px;" width="100%" align="center">
                <thead class="bd-blue-800" style="padding-top:0; font-size:12px">
                <tr height="30px">
                    <td width="1%"></td>
                    <td class="encabezado" width="18%"> <b> Empleado <b> </td>
                    <td class="encabezado" width="8%"> <b> Empresa <b> </td>
                    <td class="encabezado" width="10%"> <b> Departamento <b> </td>
                    <td class="encabezado" width="14%"> <b> Cargo <b> </td>
                    <td class="encabezado" width="10%"> <b> Ubicación Física <b> </td>
                    <td class="encabezado" width="3%"> <b> Extensi&oacute;n <b> </td>
                    <td class="encabezado" width="5%"> <b> ¿Tiene firma? <b> </td>
                    <td class="encabezado" width="3%"></td>
                    <td class="encabezado" width="3%"></td>
                    <td class="encabezado" width="3%"></td>
                    <td class="encabezado" width="3%"></td>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </section>

        <?=$this->include('templates/firma_modal')?>

        <?=$this->include('templates/loading_modal')?>

        <footer>
            <div class="container">
                <div class="row">
                    <div class="bd-blue-800" style="width:100%" height="05px">
                        <table align="center" width="50%">
                            <tr height="02px">
                                <td> </td>
                            </tr>
                        </table>
                    </div>
                    <br>
                    <div class="col-xs-12 text-center">
                        <p> <b> Derechos Reservados Grupo Berlin <?php echo date('Y') ?>. </b> </p>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>