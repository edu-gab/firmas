<?php

namespace App\Controllers;

use App\Libraries\Mail;
use App\Models\EmpleadoModel;
use App\Models\FirmaModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Psr\Log\LoggerInterface;

class EmpleadoController extends BaseController
{

    /**
    * @var EmpleadoModel Modelo del Empleado
    **/
    private EmpleadoModel $empleadoModel;

    /**
     * @var FirmaModel Modelo de la Firma
     **/
    private FirmaModel $firmaModel;


    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->empleadoModel = new EmpleadoModel();
        $this->firmaModel = new FirmaModel();
    }

    /**
     * Devuelve la información de todos los empleados
     * @api
     * @return bool|string JSON
     * @throws Exception
     */
    public function getInformacion(): bool|string
    {
        try {
            # Obtenemos los datos de las firmas
            $firmas = $this->firmaModel->getFirmas();

            # Obtenemos los datos de los empleados
            $empleados = $this->empleadoModel->getData();

            $data = [];
            foreach ($empleados as $e => &$empleado) {
                $firma = $firmas[$empleado['codigo']];
                $empleado['codigo'] = $firma['codigo'];
                $empleado['apellido'] = utf8_encode($empleado['apellido']);
                $empleado['nombre'] = utf8_encode($empleado['nombre']);
                $empleado['cargo'] = utf8_encode($empleado['cargo']);
                $empleado['empresa'] = utf8_encode($empleado['empresa']);
                $empleado['localidad'] = utf8_encode($empleado['localidad']);
                $empleado['dpto'] = utf8_encode($empleado['dpto']);
                $empleado['extension'] = utf8_encode($empleado['extension']);
                $empleado['tiene_firma'] = $firma['tiene_firma'];
                $empleado['archivo'] = $firma['archivo'];

                /Caso trabajadores registrados para INMOHANSA/
                if(!in_array($empleado['codigo'], [545, 546, 547, 548, 549])) {
                    unset($empleado['correo'], $empleado['cod_emp']);

                    $data[] = $empleado;
                }
            }

            return json_encode(array("status"=>true, "data"=>$data, "msg"=>"Datos extraídos con exito"));
        } catch (Exception $e) {
            return json_encode(array("status"=>false, "data"=>[], "msg"=>$e->getMessage()));
        }
    }

    /**
     * Permite enviar los datos del empleado al cliente
     * @api
     * @return ResponseInterface JSON
     * @throws Exception
     */
    public function getEmpleado(): ResponseInterface
    {
        $params = json_decode($this->request->getBody());
        $codigo_firma = $params->codigo;

        try {
            if(!isset($codigo_firma)){
                return $this->response->setStatusCode(400)->setBody("Faltan datos");
            }

            if(!$this->firmaModel->isValidID($codigo_firma)){
                return $this->response->setStatusCode(400)->setBody('No existe una firma con el código enviado por parametro');
            }

            $codigo_empleado = $this->firmaModel->getCodigoEmpleado($codigo_firma);
            $empleado = $this->_getEmpleadoData($codigo_empleado, $codigo_firma);
            return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setBody(json_encode($empleado));
        } catch(Exception $e) {
            return $this->response->setStatusCode(400)->setBody($e->getMessage());
        }
    }

    /**
     * Permite obtener la información de un empleado e intersectarlo con la información de su firma
     * para devolver un modelo de empleado con información de su firma
     * @param string $codigo_empleado Código del empleado
     * @param int $codigo_firma Código de la firma
     * @return array Modelo del Empleado con datos de firmas
     * @throws Exception
     */
    private function _getEmpleadoData(string $codigo_empleado, int $codigo_firma):array
    {
        $empleado = $this->empleadoModel->getEmpleadoData($codigo_empleado);
        $firma = $this->firmaModel->getFirmaData($codigo_firma);

        /Obtenemos la empresa y la localidad para devolver la ruta del archivo/
        $empresa = $firma['empresa'];
        $localidad = $firma['localidad'];

        /Estructuramos la respuesta al cliente/
        $empleado['codigo'] = $firma['codigo'];
        $empleado['apellido'] = utf8_encode($empleado['apellido']);
        $empleado['nombre'] = utf8_encode($empleado['nombre']);
        $empleado['cargo'] = utf8_encode($empleado['cargo']);
        $empleado['empresa'] = utf8_encode($empleado['empresa']);
        $empleado['localidad'] = utf8_encode($empleado['localidad']);
        $empleado['dpto'] = utf8_decode($empleado['dpto']);
        $empleado['tiene_firma'] = $firma['tiene_firma'];
        if(strcmp($empleado['cargo'], 'REPRESENTANTE DE VENTAS') == 0) {
            $empleado['celular'] = $this->empleadoModel->getCelular($codigo_empleado);
        }
        $empleado['extension'] = strcmp($firma['extension'], '') != 0?utf8_encode($firma['extension']):null;
        $empleado['correo'] = strcmp($firma['correo'], '') != 0?utf8_encode($firma['correo']):null;
        $empleado['archivo'] = !empty($firma['archivo_firma'])?utf8_encode("$empresa-$localidad/".$firma['archivo_firma'].".jpg"):null;

        return $empleado;
    }

    
}