$(document).ready(async function () {
  $("#loadingModal").modal("show");
  await fetch(`${base_url}/firma/verificarColaboradoresNuevos`)
    .then((response) => response.json())
    .then((response) => inicialAlert(response))
    .catch((error) => inicialAlert(error))
    .finally(() => $("#loadingModal").modal("hide"));

  let table = $("#firmas").DataTable({
    pageLength: 10,
    scrollX: true,
    scrollY: true,
    searching: true,
    paging: true,
    processing: true,
    serverSide: false,
    destroy: true,
    order: [[1, "asc"]],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
    },
    ajax: {
      url: `${base_url}/empleado/getAll`,
      data: "data",
      type: "POST",
    },
    columns: [
      {
        data: null,
        render: function (data, type, row) {
          return `<td>
                                <input type="hidden" name="empleado" id="" value="${data.codigo}" class="form-check-input"/>
                            </td>`;
        },
        orderable: false,
      },
      {
        data: null,
        render: function (data, type, row) {
          return `${data.apellido} ${data.nombre}`;
        },
      },
      { data: "empresa" },
      { data: "dpto" },
      { data: "cargo" },
      { data: "localidad" },
      { data: "extension" },
      {
        data: "tiene_firma",
        render: function (data, type, row) {
          if (type === "display") {
            return `<td><img data-search="${
              data === "1" ? "con firma" : "sin firma"
            }" src="${base_url}/public/images/icons/${
              data === "1" ? "valido" : "invalido"
            }.png" alt="Asignado" width="24" height="24"></td>`;
          } else if (type === "filter") {
            return data === "1" ? "con firma" : "sin firma";
          }
          return `<td><img data-search="${
            data === "1" ? "con firma" : "sin firma"
          }" src="${base_url}/public/images/icons/${
            data === "1" ? "valido" : "invalido"
          }.png" alt="Asignado" width="24" height="24"></td>`;
        },

        orderable: true,
      },
      {
        data: null,
        render: function (data, type, row) {
          return <td><img class="my-btn" src="${base_url}/public/images/icons/exterior.png" onclick="showModal(${data.codigo})" alt="OpenModal"/></td>;
        },
        orderable: false,
      },
      {
        data: null,
        render: function (data, type, row) {
          funcion = onclick="getURL('${data.archivo}')";
          return `<td><img class="${
            data.tiene_firma === "1" ? "my-btn" : "btn-disabled"
          }" src="${base_url}/public/images/icons/enlace.png" ${
            data.tiene_firma === "1" ? funcion : ""
          } alt="FirmaLink"/></td>`;
        },
        orderable: false,
      },
      {
        data: null,
        render: function (data, type, row) {
          funcion = onclick="enviarCorreo('${data.codigo}')";
          return `<td><img class="${
            data.tiene_firma === "1" ? "my-btn" : "btn-disabled"
          }" src="${base_url}/public/images/icons/email.png" ${
            data.tiene_firma === "1" ? funcion : ""
          } alt="FirmaEnvio"/></td>`;
        },
        orderable: false,
      },
      {
        data: null,
        render: function (data, type, row) {
          return <td><img class="my-btn" src="${base_url}/public/images/icons/digital-signature.png" onclick="solicitarFirma('${data.codigo}')" alt="Solicitar Firma"/></td>;
        },
        orderable: false,
      },
    ],
  });

  $("#search_value").on("keyup change", function () {
    table.search(this.value).draw();
  });

  $("#clear_btn").on("click", () => {
    table.search("").draw();
  });

  setTimeout(() => {
    $("#firmas_filter").html("");
    $("#search_value").attr("placeholder", "Buscar empleado");
    $("#firmas_info, #firmas_length, #firmas_paginate, .pagination").addClass(
      "fuente"
    );
  }, 400);

  // Implementación de Ronny
  $("#opcionForm").on("submit", onFormSubmit(e));
});

async function getEmpleadoData(codigo) {
  await fetch(`${base_url}/empleado/getData`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ codigo: codigo }),
  })
    .then((response) => response.json())
    .then(async (response) => await fillModal(response))
    .catch((error) => errorAlert(error));
}
async function onFormSubmit(e) {
  e.preventDefault();
  e.stopPropagation();

  const imagen = $('input[name="formFile"]')[0].files[0];
  let filename = $("#nom_archivo").val();

  /*Verificamos si se cargo algún archivo*/
  if (
    (filename === null || filename === undefined) &&
    (imagen === null || imagen === undefined)
  ) {
    return errorAlert("No se ha cargado el archivo de la firma");
  }

  let correoVal = $("#correo").val();
  let correo = correoVal === null || correoVal === undefined ? "" : correoVal;
  let codigo = $("#codigo").val();
  let extVal = $("#ext").val();
  let extension = extVal === null || extVal === undefined ? "" : extVal;
  const formData = new FormData();
  formData.append("imagen", imagen);
  formData.append("codigo", codigo);
  formData.append("nombre_archivo", filename);
  formData.append("correo", correo);
  formData.append("extension", extension);

  $("#cargar")
    .html(
      `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...`
    )
    .prop("disabled", true);

  await fetch(`${base_url}/firma/cargarFirma`, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((response) => {
      if (
        response.data === null ||
        response.data === undefined ||
        response.data === ""
      ) {
        errorAlert(response.msg);
        return 0;
      }

      // Si es primera vez que se carga la firma, disparamos el correo automáticamente
      if (response.new) {
        fetch(`${base_url}/empleado/sendMail`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ codigo: codigo }),
        })
          .then((response) => response.json())
          .then((response) => {
            console.log(response.msg);
          })
          .catch((error) => console.log(error));
      }

      let HTMLresponse = "<div>&nbsp;</div>";

      response.msg.forEach((rmsg) => {
        if (rmsg.msg.length !== 0) {
          HTMLresponse =
            HTMLresponse +
            `<div><img data-search="${
              rmsg.isSuccess ? "con firma" : "sin firma"
            }" src="${base_url}/public/images/icons/${
              rmsg.isSuccess ? "valido" : "invalido"
            }.png" alt="Estado" width="24" height="24">`;
          HTMLresponse = HTMLresponse + `&nbsp;&nbsp; ${rmsg.msg}</div>`;
          HTMLresponse = HTMLresponse + "<div>&nbsp;</div>";
        }
      });

      successAlert(HTMLresponse, true);
      $("#firmas").DataTable().ajax.reload(null, false);
      getEmpleadoData(response.data);
    })
    .catch((error) => errorAlert(error.msg))
    .finally(() => {
      $("#cargar").html(`Guardar`).prop("disabled", false);
    });
}

async function solicitarFirma(codigo) {
  Swal.fire({
    title:
      "¿Está seguro de enviar el correo de solicitud de firma para este colaborador?",
    text: "Esta acción no se podrá revertir.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, enviar",
    cancelButtonText: "No, cancelar",
  }).then(async (result) => {
    if (result.isConfirmed) {
      $("#loadingModal").modal("show");
      $("#loadingModal #modal-msg").text("Enviando correo");
      await fetch(`${base_url}/firma/sendRequestSignature`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ codigo: codigo }),
      })
        .then(async (response) => {
          if (!response.ok) {
            throw new Error(await response.text());
          }
          return response.json();
        })
        .then((data) => {
          successAlert(data.msg);
        })
        .catch((error) => {
          if (error.msg !== undefined) {
            errorAlert(error.msg);
          } else {
            errorAlert(error);
          }
        })
        .finally(() => {
          $("#loadingModal").modal("hide");
          $("#loadingModal #modal-msg").text("Espere un momento");
        });
    }
  });
}

async function showModal(codigo) {
  $("#loadingModal").modal("show");

  /*Limpiamos la ventana*/
  cleanModal();

  /*Verificamos si está abierto el accordion*/
  if (!$("#DataAccordion").hasClass("collapsed")) {
    $("#DataAccordion").removeClass("collapse show").addClass("collapsed");
  }

  /*Cargamos los datos del empleado*/
  await getEmpleadoData(codigo);

  $("#loadingModal").modal("hide");

  /*Mostramos la modal con los datos*/
  $("#firmaModal").modal("show");
}

function actualizarFirma() {
  $("#actualizar_firma").addClass("visually-hidden");
  $("#fileUpload").removeClass("visually-hidden");
}

function inicialAlert(response = null) {
  swal.fire({
    title: response.status === true ? "Proceso exitoso" : "Hubo un problema",
    text:
      response.status === true
        ? `${response.msg}, nuevos colaboradores: ${response.nuevos}`
        : "",
    icon: response.status === true ? "success" : "error",
    showConfirmButton: false,
    timer: 2500,
  });
}

function errorAlert(msg = null) {
  swal.fire({
    title: "Hubo un problema",
    text:
      msg === null
        ? "Tuvimos un problema con los datos, por favor vuelva a intentarlo.  Si el problema persiste, contactese con un analista de sistemas."
        : msg,
    icon: "error",
    showConfirmButton: false,
    timer: 2500,
  });
}

function successAlert(response = null, isHTML = false) {
  let rflag = response === null || response === undefined;
  if (isHTML) {
    swal.fire({
      title: rflag
        ? "Ocurrió un problema al realizar la operación"
        : "Operación realizada con éxito",
      html: rflag ? "" : response,
      icon: rflag ? "error" : "success",
      showConfirmButton: false,
      timer: 2500,
    });
  } else {
    swal.fire({
      title: rflag
        ? "Ocurrió un problema al realizar la operación"
        : "Operación realizada con éxito",
      text: rflag ? "" : response,
      icon: rflag ? "error" : "success",
      showConfirmButton: false,
      timer: 2500,
    });
  }
}

function cleanModal() {
  $("#firmaModal #nombre").val();
  $("#firmaModal #empresa").val();
  $("#firmaModal #localidad").val();
  $("#firmaModal #departamento").val();
  $("#firmaModal #cargo").val();
  $("#firmaModal #nom_archivo").val("");
  $("#firmaModal #correo").val("");
  $("#firmaModal #ext").val("");
  $("#firmaModal #firmaImage").attr(
    "src",
    `https://res.cloudinary.com/dtutqsucw/image/upload/v1438955603/file-upload-01.png`
  );
  $("#firmaModal #image").val(null).addClass("visually-hidden");
  $("#firmaModal #fileUpload").removeClass("visually-hidden");
  $("#firmaModal #actualizar_firma").removeClass("visually-hidden");
}

/**
 * Sends a configuration email to a collaborator.
 *
 * @param {string} codigo - The code of the collaborator.
 * @returns {Promise<void>} - A promise that resolves when the email is sent successfully.
 */
async function enviarCorreo(codigo) {
  Swal.fire({
    title:
      "¿Está seguro de enviar el correo de configuración para este colaborador?",
    text: "Esta acción no se podrá revertir.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, enviar",
    cancelButtonText: "No, cancelar",
  }).then(async (result) => {
    if (result.isConfirmed) {
      $("#loadingModal").modal("show");
      $("#modal-msg").text("Enviando correo");
      await fetch(`${base_url}/empleado/sendMail`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ codigo: codigo }),
      })
        .then(async (response) => {
          if (!response.ok) {
            throw new Error(await response.text());
          }
          return response.json();
        })
        .then((data) => {
          successAlert(data.msg);
        })
        .catch((error) => {
          if (error.msg !== undefined) {
            errorAlert(error.msg);
          } else {
            errorAlert(error);
          }
        })
        .finally(() => {
          $("#loadingModal").modal("hide");
          $("#loadingModal #modal-msg").text("Espere un momento");
        });
    }
  });
}

/**
 * Fills the modal with employee information.
 *
 * @param {Object} empleado - The employee object.
 * @param {string} empleado.codigo - The employee code.
 * @param {string} empleado.apellido - The employee last name.
 * @param {string} empleado.nombre - The employee first name.
 * @param {string} empleado.empresa - The employee company.
 * @param {string} empleado.localidad - The employee location.
 * @param {string} empleado.dpto - The employee department.
 * @param {string} empleado.cargo - The employee position.
 * @param {string} empleado.tiene_firma - Indicates if the employee has a signature (1 for true, 0 for false).
 * @param {string} empleado.archivo - The employee signature file path.
 * @param {string} empleado.extension - The employee signature file extension.
 * @param {string} empleado.correo - The employee email address.
 * @returns {void}
 */
async function fillModal(empleado) {
  $("#firmaModal #codigo").val(empleado.codigo);
  $("#firmaModal #nombre").val(`${empleado.apellido} ${empleado.nombre}`);
  $("#firmaModal #empresa").val(empleado.empresa);
  $("#firmaModal #localidad").val(empleado.localidad);
  $("#firmaModal #departamento").val(empleado.dpto);
  $("#firmaModal #cargo").val(empleado.cargo);

  /Verificamos si el empleado tiene firma/
  let nom_archivo;
  if (empleado.tiene_firma === "1") {
    /Verificamos si muestra la imagen/
    if ($("#firmaModal #image").hasClass("visually-hidden")) {
      $("#firmaModal #image").removeClass("visually-hidden");
    }

    /Verificamos si oculta el input para subir archivos/
    if (!$("#firmaModal #fileUpload").hasClass("visually-hidden")) {
      $("#firmaModal #fileUpload").addClass("visually-hidden");
    }

    $("#firmaModal #firmaImage").attr(
      "src",
      `https://webapps.boschecuador.com/firmas/${empleado.archivo}`
    );
    nom_archivo = empleado.archivo.slice(1, -4).split("/");
    $("#firmaModal #nom_archivo").val(nom_archivo[1] + ".jpg");
  }

  /Verificamos si tiene extensión/
  if (empleado.extension !== null || empleado.extension !== "") {
    $("#firmaModal #ext").val(empleado.extension);
  } else {
    $("#firmaModal #ext").val("No tiene");
  }

  /Verificamos si tiene correo/
  if (empleado.correo !== null || empleado.correo !== "") {
    $("#firmaModal #correo").val(empleado.correo);
  } else {
    $("#firmaModal #correo").val("No tiene");
  }
}

function getURL(link) {
  let aux = document.createElement("input");
  aux.setAttribute("value", `https://webapps.boschecuador.com/firmas/${link}`);
  document.body.appendChild(aux);
  aux.select();
  document.execCommand("copy");
  document.body.removeChild(aux);
  let css = document.createElement("style");
  let estilo = document.createTextNode(
    "#aviso {position:fixed; z-index: 9999999; top: 80%;left:50%;margin-left: -70px;padding: 20px; background: #C7C5C5;border-radius: 8px;font-family: sans-serif;opacity:0.75;}"
  );
  css.appendChild(estilo);
  document.head.appendChild(css);
  let aviso = document.createElement("div");
  aviso.setAttribute("id", "aviso");
  let contenido = document.createTextNode("URL copiada");
  aviso.appendChild(contenido);
  document.body.appendChild(aviso);
  window.load = setTimeout("document.body.removeChild(aviso)", 2000);
}
