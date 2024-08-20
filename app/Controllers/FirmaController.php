<?php

namespace App\Controllers;

use App\Libraries\Mail;
use App\Models\EmpleadoModel;
use App\Models\FirmaModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Psr\Log\LoggerInterface;

class FirmaController extends BaseController
{

    private FirmaModel $firmaModel;
    private EmpleadoModel $empleadoModel;
    private UsuarioModel $userModel;


    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->firmaModel = new FirmaModel();
        $this->empleadoModel = new EmpleadoModel();
        $this->userModel = new UsuarioModel();
    }

    public function index(): string
    {
        $nombre = explode(' ', session()->get('nombre'))[0];
        $img = session()->get('profilePhoto');

        return view('home/principal', ['nombre' => $nombre, 'imgPath' => $img]);
    }

    /**
     * Permite cargar una firma de un colaborador
     *
     * @throws Exception
     */
    public function cargarFirma(): ResponseInterface
    {
        $codigo = $this->request->getPost('codigo');
        $imageFile = $this->request->getFile('imagen');
        $filename = $this->request->getPost('nombre_archivo');
        $extension = $this->request->getPost('extension');
        $correo = $this->request->getPost('correo');

        try {
            # Definimos los mensajes que serán devueltos al cliente
            $flag1 = false;
            $msg1 = '';
            $flag2 = false;
            $msg2 = '';
            $flag3 = false;
            $msg3 = '';

            /*Verificamos la estructura del request*/
            if (!isset($codigo) || !isset($filename) || !isset($extension) || !isset($correo)) {
                return $this->errorResponse("Faltan datos.", 400);
            }

            /* Verificamos si el colaborador no tiene una firma y si se ha cargado un archivo*/
            /*if( strlen($filename) == 0 && !isset($imageFile)){
                return $this->errorResponse("Debe cargar la firma del colaborador.", 400);
            }*/

            /*Validamos que el código de la firma sea válido*/
            if (!$this->firmaModel->isValidID($codigo)) {
                return $this->errorResponse("El código recibido no es válido.", 400);
            }

            $nflag = false;

            /* Verificamos si hay una imagen cargada y de ser afirmativa la respuesta, procederemos a realizar la carga*/
            if (isset($imageFile)) {
                /*Verificamos la validez del archivo recibido*/
                if (!$imageFile->isValid() && $imageFile->hasMoved()) {
                    return $this->errorResponse("Hubo un error al procesar la imagen.", 400);
                }

                /*Validamos que el archivo recibido sea una imagen y que tenga un formato válido*/
                if (!in_array($imageFile->getExtension(), ['jpg', 'jpeg', 'png'])) {
                    return $this->errorResponse("El formato de imagen no es un formato válido.", 400);
                }

                $ruta = $this->firmaModel->getFolder($codigo);
                $firma_data = $this->firmaModel->getFirmaData($codigo);
                $imageName = explode(".", $imageFile->getName())[0];
                $newName = null;

                // Verificamos si el colaborador ya tiene una firma
                if (is_null($firma_data['archivo_firma']) || strcmp($firma_data['archivo_firma'], "") == 0) {

                    // Verificamos si el nuevo nombre tiene espacios
                    if (strpos("-", $imageName)) {
                        $newName = $imageName . "." . $imageFile->getExtension();
                    } else {
                        $newName = implode("-", explode(" ", $imageName)) . "." . $imageFile->getExtension();
                    }
                } else {
                    // Verificamos si el nombre del nuevo archivo es igual al nombre registrado en la BD
                    if (strcmp($firma_data['archivo_firma'], $imageName) != 0) {
                        $newName = $firma_data['archivo_firma'] . "." . $imageFile->getExtension();
                    }
                }

                /*Movemos el archivo de la imagen y verificamos que haya sido copiada correctamente*/
                if (!$imageFile->move(ROOTPATH . "$ruta", $newName, true)) {
                    return $this->errorResponse("Ocurrió un error interno al procesar la imagen.", 500);
                }

                if (is_null($firma_data['fecha_carga'])) {
                    if (!$this->firmaModel->cargarFirma(utf8_decode($newName), $codigo)) {
                        return $this->errorResponse("Ocurrió un error interno al procesar la imagen.", 500);
                    }
                    $nflag = true;
                } else {
                    if (!$this->firmaModel->updateFirma($codigo)) {
                        return $this->errorResponse("Ocurrió un error interno al procesar la imagen.", 500);
                    }
                }

                $flag1 = true;
                $msg1 = "Firma actualizada de manera exitosa.";
            }

            /* Verificamos si debemos actualizar algún campo */
            $cod_evolution = $this->firmaModel->getCodigoEmpleado($codigo);
            $colaborador = $this->empleadoModel->getEmpleadoData($cod_evolution);
            $nombre_colaborador = ucwords(strtolower($colaborador['nombre'] . " " . $colaborador['apellido']));

            $old_correo = $this->firmaModel->getCorreo($codigo);

            /*
             * Si el correo ingresado es diferente que el que está en la base de datos y coincide con la estructura
             * de un correo corporativo la cual es nombre.apellido@grupoberlin.com, actualizamos el campo de correo
             * */
            # Verificamos la estructura del nuevo correo
            if (!preg_match("/^[a-z]+.+[a-z]+@grupoberlin.com$/", $correo)) {
                $msg2 = "El nuevo correo no coincide con la estructura del correo de la empresa.";
            } else {
                if (strcmp($old_correo, $correo) != 0) {
                    $response = $this->firmaModel->updateCorreo($codigo, $correo);
                    $flag2 = $response;
                    $msg2 = $response ? "Correo actualizado de manera exitosa." : "Ocurrió un problema al actualizar el correo.";
                    if ($response) {
                        $this->notificacionRRHH(2, $old_correo, $correo, $nombre_colaborador);
                    }
                }
            }

            $old_extension = $this->firmaModel->getExtension($codigo);

            /*
             * Si la extensión ingresada es diferente que el que está en la base de datos y la información que recibimos
             * no esta vacía, actualizamos el campo de extensión
             * */
            if (strlen($extension) != 0 and strcmp($old_extension, $extension) != 0) {
                $response = $this->firmaModel->updateExtension($codigo, $extension);
                $flag3 = $response;
                $msg3 = $response ? "Extensión actualizada de manera exitosa." : "Ocurrió un problema al actualizar la extensión.";
                if ($response) {
                    $this->notificacionRRHH(1, $old_extension, $extension, $nombre_colaborador);
                }
            }

            return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setJSON(json_encode(array(
                    "msg" => [
                        ["isSuccess" => $flag1, "msg" => $msg1],
                        ["isSuccess" => $flag2, "msg" => $msg2],
                        ["isSuccess" => $flag3, "msg" => $msg3]
                    ],
                    "data" => $codigo,
                    "new" => $nflag
                )));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    private function errorResponse(string $msg, int $status_code, mixed $data = ""): ResponseInterface
    {
        return $this->response
            ->setStatusCode($status_code)
            ->setContentType('application/json')
            ->setJSON(json_encode(array("msg" => $msg, "data" => $data)));
    }
    
    public function solicitarFirma():ResponseInterface
    {
        $request = json_decode($this->request->getBody());

        // Verificamos si el cliente ha iniciado sessión
        if(!session()->has('isLoggedIn')) {
            return $this->errorResponse("Debe iniciar sesión para poder enviar el correo", 400);
        }

        // Verificamos si el cliente envió el código del empleado
        if(!isset($request->codigo)) {
            return $this->errorResponse("Se necesita el código del colaborador", 500);
        }

        try{
            # Obtenemos el código de EVOLUTION
            $cod_infor = $this->firmaModel->getCodigoEmpleado($request->codigo);

            # Obtenemos los datos del colaborador
            $data = $this->empleadoModel->getEmpleadoData($cod_infor);

            # Obtenemos los datos del solicitante
            $solicitante = $this->userModel->getUserData(session()->get('usuario'));

            #Obtenemos los datos adicionales para la firma
            $correo = $this->firmaModel->getCorreo($request->codigo);
            $extension = $this->firmaModel->getExtension($request->codigo);

            $mail = new Mail();

            $msg = '
            <div style="background:#dcdcdc;height:100%">
                <div style="background: #dcdcdc;height: 100%;display: block;padding-top: 4%;padding-bottom: 4%">
                    <div style="width:575px;margin:0 auto;border-radius: 8px;overflow: hidden">
                        <div style="background: #fff;font-size: 35px;text-align: center;margin-bottom: 0;font-weight: 300;padding-top: 40px">
                            <img 
                                alt="" 
                                height="" 
                                src="https://boschecuador.com/dist/img/logo-tecnova-new.png" 
                                width="173" 
                                style="vertical-align:middle;border: 0" 
                            >
                        </div>
                        <div style="background:#fff;padding:40px;padding-top:1px;border-top:0;color:#242424">
                            <h4 style="font-family:Arial;color:#003399!important;font-size:18px;font-weight:normal">
                                Estimado,
                                <br>
                                <a
                                    href="#m_-6942895880420619638_" 
                                    style="color:#003399;text-decoration-line:none"
                                >
                                    Equipo de Dise&ntilde;o
                                </a>
                            </h4>
                            <div style="font-family:Arial;color:#3f3f47;line-height:24px;font-size:14px; text-align:justify">
                                Espero que se encuentren bien. A trav&eacute;s de este correo, les solicitamos amablemente su 
                                colaboraci&oacute;n para dise&ntilde;ar una firma digital para uno de nuestros colaboradores.
                            </div>
                            <br>
                            <div>
                                <h3 style="font-family:Arial;color:#003399!important;line-height: 24px;font-size: 16px;text-align: justify">Detalles de la firma:</h3>
                            </div>
                            <table style="border-collapse:collapse;border:1px solid #000000;width: 100%">
                                    <tr style="background-color: #f2f2f2;border:2px solid ">
                                        <th style="text-align:left;padding:8px;background-color:#003b6a!important;color:white;border:2px solid #000000;font-family:Arial;line-height: 24px;font-size: 14px">Nombre:</th>
                                        <td style="text-align: left;padding: 12px;font-family: Arial;color: #3f3f47!important;line-height: 24px;font-size: 14px">
                                            ' .ucwords(mb_strtolower(utf8_encode(implode(" ", [$data["nombre"], $data["apellido"]])))).'
                                        </td>
                                    </tr>
                                    <tr style="background-color: #f2f2f2;border:2px solid">
                                        <th style="text-align:left;padding:8px;background-color:#003b6a!important;color:white;border:2px solid #000000;font-family:Arial;line-height:24px;font-size:14px">Empresa:</th>
                                        <td style="text-align: left;padding: 12px;font-family: Arial;color: #3f3f47!important;line-height: 24px;font-size: 14px">
                                            '.ucwords(strtolower($data["empresa"])).'
                                        </td>
                                    </tr>
                                    <tr style="background-color: #f2f2f2;border:2px solid">
                                        <th style="text-align:left;padding:8px;background-color:#003b6a!important;color:white;border:2px solid #000000;;font-family:Arial;line-height:24px;font-size:14px">Ubicaci&oacute;n F&iacute;sica:</th>
                                        <td style="text-align: left;padding: 12px;font-family: Arial;color: #3f3f47!important;line-height: 24px;font-size: 14px">
                                            '.ucwords(mb_strtolower(utf8_encode($data["localidad"]))).'
                                        </td>
                                    </tr>
                                    <tr style="background-color: #f2f2f2;border:2px solid">
                                        <th style="text-align:left;padding:8px;background-color:#003b6a!important;color:white;border:2px solid #000000;;font-family:Arial;line-height:24px;font-size:14px">Departamento:</th>
                                        <td style="text-align: left;padding: 12px;font-family: Arial;color: #3f3f47!important;line-height: 24px;font-size: 14px">
                                            '.ucwords(mb_strtolower(utf8_encode($data["dpto"]))).'
                                        </td>
                                    </tr>
                                    <tr style="background-color: #f2f2f2;border:2px solid">
                                        <th style="text-align:left;padding:8px;background-color:#003b6a!important;color:white;border:2px solid #000000;;font-family:Arial;line-height:24px;font-size:14px">Cargo:</th>
                                        <td style="text-align: left;padding: 12px;font-family: Arial;color: #3f3f47!important;line-height: 24px;font-size: 14px">
                                            '.ucwords(mb_strtolower(utf8_encode($data["cargo"]))).'
                                        </td>
                                    </tr>
                                    <tr style="background-color: #f2f2f2;border:2px solid">
                                        <th style="text-align:left;padding:8px;background-color:#003b6a!important;color:white;border:2px solid #000000;;font-family:Arial;line-height:24px;font-size:14px">Extensi&oacute;n:</th>
                                        <td style="text-align: left;padding: 12px;font-family: Arial;color: #3f3f47!important;line-height: 24px;font-size: 14px">
                                            '.$extension.'
                                        </td>
                                    </tr>
                                    <tr style="background-color: #f2f2f2;border:2px solid">
                                        <th style="text-align:left;padding:8px;background-color:#003b6a!important;color:white;border:2px solid #000000;;font-family:Arial;line-height:24px;font-size:14px">Correo:</th>
                                        <td style="text-align: left;padding: 12px;font-family: Arial;color: #3f3f47!important;line-height: 24px;font-size: 14px">
                                            <a
                                                href="#m_-6942895880420619638_" 
                                                style="color:#3f3f47;text-decoration-line:none"
                                            >
                                                '.strtolower($correo).'
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            <div>&nbsp;</div>
                            <div style="font-family:Arial;color:#3f3f47;line-height:24px;font-size:14px; text-align:justify">
                                En caso de necesitar informaci&oacute;n adicional o tengan alguna duda, por favor comunicarse directamente con el colaborador
                                <b>'.ucwords($solicitante["nombre"]).'</b> a trav&eacute;s de la siguiente direcci&oacute;n de correo electr&oacute;nico:
                                <a  href="mailto:'.strtolower($solicitante["correo"]).'" 
                                    style="text-decoration-line:none;color:#003b6a" 
                                    target="_blank">
                                    <b>
                                        '.strtolower($solicitante["correo"]).'
                                    </b>
                                </a>
                            </div>
                            <br>
                            <div style="font-family:Arial;color:#3f3f47;line-height:24px;font-size:14px; text-align:justify">
                                Si recibiste este correo por error, puedes ignorarlo. Si encuentras inconvenientes no dudes en enviar un correo a 
                                <a  href="mailto:aplicaciones@grupoberlin.com" 
                                    style="text-decoration-line:none;color:#003b6a" 
                                    target="_blank">
                                    <b>
                                        aplicaciones@grupoberlin.com
                                    </b>
                                </a>
                            </div>
                            <div>&nbsp;</div>
                            <div>&nbsp;</div>
                            <div>&nbsp;</div>
                            <div style="font-family:Arial;color:#3f3f47;line-height:24px;font-size:14px">
                                Departamento de Sistemas 
                                <br>
                                Tecnova S.A.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ';

            $status_code = $mail->sendMail("andres.valcarcel@grupoberlin.com, andres.jimenez@grupoberlin.com", $msg, 'Solicitud de Diseño de Firma Digital para Colaborador', true)?200:500;
            $body = $status_code == 200?'Correo enviado de manera exitosa al colaborador':'Error al enviar el correo al colaborador';

            return $this->response
                ->setStatusCode($status_code)
                ->setContentType('application/json')
                ->setBody(json_encode(array('isSuccess'=>$status_code == 200,'msg'=>$body)));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    private function notificacionRRHH (int $type, string $old_data, string $new_data, string $col_name): void
    {
        try {
            # Obtenemos los datos del solicitante
            $solicitante = $this->userModel->getUserData(session()->get('usuario'));

            if ($type == 1) {
                $body = "Hemos recibido una notificaci&oacute;n de actualizaci&oacute;n de la l&iacute;nea de extensi&oacute;n telef&oacute;nica para un colaborador por parte de 
                     <b>".ucwords(strtolower($solicitante["nombre"]))."</b>.  
                     La actualizaci&oacute;n se realiz&oacute; para el colaborador <b>".ucwords(mb_strtolower(utf8_encode($col_name)))."</b>, quien antes ten&iacute;a la extensi&oacute;n 
                     <b>".strlen($old_data) == 0?'(Sin registro)':$old_data."</b> y el presente d&iacute;a le fue asignada la extensi&oacute;n <b>".$new_data."</b>.";
                $subject = "Actualización de extensión de línea telefónica para el colaborador ".ucwords(mb_strtolower(utf8_encode($col_name)));
            } else if ($type == 2) {
                $body = "Hemos recibido una notificaci&oacute;n de actualizaci&oacute;n de la direcci&oacute;n de correo electr&oacute;nico para un colaborador por parte de 
                     <b>".ucwords(strtolower($solicitante["nombre"]))."</b>.  
                     La actualizaci&oacute;n se realiz&oacute; para el colaborador <b>".ucwords(mb_strtolower(utf8_encode($col_name)))."</b>, quien antes ten&iacute;a la direcci&oacute;n de correo 
                     <b>".$old_data."</b> y el presente d&iacute;a le fue asignada la direcci&oacute;n de correo <b>".$new_data."</b>.";
                $subject = "Actualización de dirección de correo electrónico para el colaborador ".ucwords(mb_strtolower(utf8_encode($col_name)));
            } else {
                return;
            }

            $mail = new Mail();

            $msg = '
                <div style="background:#dcdcdc;height:100%">
                    <div style="background: #dcdcdc;height: 100%;display: block;padding-top: 4%;padding-bottom: 4%">
                        <div style="width:575px;margin:0 auto;border-radius: 8px;overflow: hidden">
                            <div style="background: #fff;font-size: 35px;text-align: center;margin-bottom: 0;font-weight: 300;padding-top: 40px">
                                <img 
                                    alt="" 
                                    height="" 
                                    src="https://webapps.boschecuador.com/firmas/public/images/logo-tecnova-new.png" 
                                    width="173" 
                                    style="vertical-align:middle;border: 0" 
                                >
                            </div>
                            <div style="background:#fff;padding:40px;padding-top:1px;border-top:0;color:#242424">
                                <h4 style="font-family:Arial;color:#003399!important;font-size:18px;font-weight:normal">
                                    Estimado,
                                    <br>
                                    <a
                                        href="#m_-6942895880420619638_" 
                                        style="color:#003399;text-decoration-line:none"
                                    >
                                        Equipo de Recursos Humanos
                                    </a>
                                </h4>
                                <div style="font-family:Arial;color:#3f3f47;line-height:24px;font-size:14px; text-align:justify">
                                    '.$body.'
                                </div>
                                <br>
                                <div>&nbsp;</div>
                                <div style="font-family:Arial;color:#3f3f47;line-height:24px;font-size:14px; text-align:justify">
                                    En caso de necesitar información adicional o tengan alguna duda, por favor comunicarse directamente con el colaborador
                                    <b>'.ucwords($solicitante["nombre"]).'</b> a través de la siguiente dirección de correo electrónico:
                                    <a  href="mailto:'.strtolower($solicitante["correo"]).'" 
                                        style="text-decoration-line:none;color:#003b6a" 
                                        target="_blank">
                                        <b>
                                            '.strtolower($solicitante["correo"]).'
                                        </b>
                                    </a>
                                </div>
                                <br>
                                <div style="font-family:Arial;color:#3f3f47;line-height:24px;font-size:14px; text-align:justify">
                                    Si recibiste este correo por error, puedes ignorarlo. Si encuentras inconvenientes no dudes en enviar un correo a 
                                    <a  href="mailto:aplicaciones@grupoberlin.com" 
                                        style="text-decoration-line:none;color:#003b6a" 
                                        target="_blank">
                                        <b>
                                            aplicaciones@grupoberlin.com
                                        </b>
                                    </a>
                                </div>
                                <div>&nbsp;</div>
                                <div>&nbsp;</div>
                                <div>&nbsp;</div>
                                <div style="font-family:Arial;color:#3f3f47;line-height:24px;font-size:14px">
                                    Departamento de Sistemas 
                                    <br>
                                    Tecnova S.A.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ';

            $mail->sendMail("mary.bajana@grupoberlin.com, diana.alcivar@grupoberlin.com", $msg, $subject);
            return;
        } catch (Exception $e) {
            return;
        }
    }
}
