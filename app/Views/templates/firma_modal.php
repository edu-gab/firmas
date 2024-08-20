<div class="modal fade" id="firmaModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bd-blue-800">
                <h1 class="modal-title fs-5" id="firmaModal_title">Cargar Archivo de Firma Digital</h1>
                <button id="close" type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="opcionForm" action="<?=base_url()?>/cargarFirma" class="needs-validation" novalidate autocomplete="off"  method="post" enctype="multipart/form-data">
                <div id="body" class="modal-body fuente text-center">

                    <div class="accordion accordion-flush" id="DataAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#DatosEmpleado" aria-expanded="false" aria-controls="DatosEmpleado">
                                    Datos del Empleado
                                </button>
                            </h2>
                            <div id="DatosEmpleado" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#DataAccordion">
                                <div class="accordion-body">
                                    <!--Nombres-->
                                    <div class="d-flex justify-content-between p-3 text-center align-content-center align-items-center">
                                        <label for="nombre" class="fs-6">Nombre:</label>
                                        <input id="nombre" name="nombre" type="text" class="form-control fs-6 text-center text-muted" style="width:200px;" disabled>
                                    </div>

                                    <!--Empresa-->
                                    <div class="d-flex justify-content-between p-3 text-center align-content-center align-items-center">
                                        <label for="empresa" class="fs-6">Empresa:</label>
                                        <input id="empresa" name="empresa" type="text" class="form-control fs-6 text-center text-muted" style="width:200px;" disabled>
                                    </div>

                                    <!--Ubicación Física-->
                                    <div class="d-flex justify-content-between p-3 text-center align-content-center align-items-center">
                                        <label for="localidad" class="fs-6">Ubicaci&oacute;n F&iacute;sica:</label>
                                        <input id="localidad" name="localidad" type="text" class="form-control fs-6 text-center text-muted" style="width:200px;" disabled>
                                    </div>

                                    <!--Departamento-->
                                    <div class="d-flex justify-content-between p-3 text-center align-content-center align-items-center">
                                        <label for="departamento" class="fs-6">Departamento:</label>
                                        <input id="departamento" name="departamento" type="text" class="form-control fs-6 text-center text-muted" style="width:200px;" disabled>
                                    </div>

                                    <!--Cargo-->
                                    <div class="d-flex justify-content-between p-3 text-center align-content-center align-items-center">
                                        <label for="cargo" class="fs-6">Cargo:</label>
                                        <input id="cargo" name="cargo" type="text" class="form-control fs-6 text-center text-muted" style="width:200px;" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Extensión-->
                    <div class="d-flex justify-content-between p-3 text-center align-content-center align-items-center">
                        <label for="ext" class="fs-6">Extensi&oacute;n:</label>
                        <input id="ext" name="ext" type="text" class="form-control fs-6 text-center" style="width:200px;">
                    </div>

                    <!--Correo-->
                    <div class="d-flex justify-content-between p-3 text-center align-content-center align-items-center">
                        <label for="correo" class="fs-6">Correo:</label>
                        <input id="correo" name="correo" type="text" class="form-control fs-6 text-center" style="width:200px;">
                    </div>

                    <!--Nombre archivo-->
                    <div class="d-flex justify-content-between p-3 text-center align-content-center align-items-center">
                        <label for="nom_archivo" class="fs-6">Nombre archivo:</label>
                        <input id="nom_archivo" name="nom_archivo" type="text" class="form-control fs-6 text-center text-muted" style="width:200px;" disabled>
                    </div>

                    <div class="p-3 text-center visually-hidden" id="image">
                        <img id="firmaImage" src="https://res.cloudinary.com/dtutqsucw/image/upload/v1438955603/file-upload-01.png" alt="upload-file" width="435" height="230" style="border: 1px solid #000000"/>
                        <button id="actualizar_firma" type="button" class="btn btn-primary fs-6 mt-4" onclick="actualizarFirma()">Actualizar firma de colaborador/a</button>
                    </div>

                    <div id="fileUpload" class="mb-3 p-3">
                        <input id="codigo" name="codigo" type="hidden" disabled>
                        <label for="formFile" class="form-label"></label>
                        <input class="form-control" type="file" id="formFile" name="formFile" accept=".jpg">
                    </div>

                </div>
                <div class="modal-footer">
                    <button id="cargar" type="submit" class="btn btn-primary fs-6">Guardar</button>
                    <button id="cancelar" type="button" class="btn btn-danger fs-6" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>