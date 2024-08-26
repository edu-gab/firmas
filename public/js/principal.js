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
