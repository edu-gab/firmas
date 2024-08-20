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
}
