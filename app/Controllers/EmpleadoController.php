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

                /*Caso trabajadores registrados para INMOHANSA*/
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

        /*Oabtenemos la empresa y la localidad para devolver la ruta del archivo*/
        $empresa = $firma['empresa'];
        $localidad = $firma['localidad'];

        /*Estructuramos la respuesta al cliente*/
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

    /**
     * Permite enviar un mail con la información para que el colaborador configure su firma digital
     * dentro del correo empresarial
     * @return ResponseInterface JSON
     * @throws Exception
     */
    public function sendMail():ResponseInterface
    {
        $request = json_decode($this->request->getBody());
        $codigo_firma = $request->codigo;

        # Obtenemos el código de EVOLUTION
        $cod_infor = $this->firmaModel->getCodigoEmpleado($codigo_firma);

        # Obtenemos el correo del colaborador
        $data = $this->empleadoModel->getCorreoData($cod_infor);

        # Obtenemos la URL de la firma del colaborador
        $firma_url = $this->firmaModel->getURLFirma($codigo_firma);

        $mail = new Mail();
        $col_name = $data['colaborador'];

        // Definimos la estructura del mensaje
        $msg= "
        <HTML lang='es'>
            <body> 
                <p style='font-size: 14px;'> <b>Estimado(a),</b></p>
                <p style='font-size: 14px;'> <b>$col_name</b></p>
                <p style='font-size: 14px;'> El siguiente instructivo le permitir&aacute; configurar su firma de correo electr&oacute;nico, para lo cual se adjunta el enlace a su <a href='$firma_url'>firma</a> para que pueda realizar la respectiva configuraci&oacute;n.</p>
                <br>
                <h2 style='margin-bottom: 10px'>Pasos para la configuraci&oacute;n:</h2>
                
                <p style='font-size: 14px;'><b>1.-</b> Copiar el enlace de su firma de correo.</p>
                <br>
                <p style='font-size: 14px;'><b>2.-</b> Dirigirse a <b>Gmail</b>.</p>
                <br>
                <p style='font-size: 14px;'><b>3.-</b> Dar clic en el &iacute;cono de <b style='color:blue'>Configuraci&oacute;n</b> <img src='https://webapps.boschecuador.com/firmas/public/images/instructivo/config.png' alt='configuracion icono' width='16' height='16' style='margin-bottom:-5px'> <img src='https://webapps.boschecuador.com/firmas/public/images/instructivo/arrow.png' alt='flecha' width='16' height='16' style='margin-bottom:-5px'> <b style='color:blue'>Ver todos los ajustes</b>.</p>
                <img src='https://webapps.boschecuador.com/firmas/public/images/instructivo/paso3.png' alt='Paso3'>
                <br>
                <br>
                <p style='font-size: 14px;'><b>4.-</b> En la opci&oacute;n <b style='color:blue'>Firma</b>, dar clic en el icono de <b style='color:blue'>Insertar Imagen</b>.</p>
                <img src='https://webapps.boschecuador.com/firmas/public/images/instructivo/paso4.png' alt='Paso4'>
                <br>
                <br>
                <p style='font-size: 14px;'><b>5.-</b> Se muestra la pantalla de <b style='color:blue'>A&ntilde;adir una imagen</b>, en donde debe pegar el enlace copiado del <b style='text-decoration: underline'>paso 1</b> y dar clic en <b style='color:blue'>Aceptar</b>.</p>
                <img src='https://webapps.boschecuador.com/firmas/public/images/instructivo/paso5.png' alt='Paso5'>
                <br>
                <br>
                <p style='font-size: 14px;'><b>6.-</b> Confirmar que este habilitada la opción <b style='text-decoration: underline'>Insertar esta firma antes del texto citado en las respuestas</b> y luego damos clic en <b style='color:blue'>Guardar Cambios</b>.</p>
                <img src='https://webapps.boschecuador.com/firmas/public/images/instructivo/paso6.png' alt='Paso5'>
                <br>
                <br>
                <p style='font-size: 14px;'>Atentamente,</p>
                <p style='font-size: 14px;'>Departamento de sistemas.</p>
                <br>
                <img src='https://webapps.boschecuador.com/firmas/public/images/instructivo/firma_correo.png' alt='Firma'>
            </body>
        </HTML>
        ";

        $status_code = $mail->sendMail($data['correo'], $msg, 'CONFIGURACIÓN DE FIRMA DIGITAL PARA COLABORADORES (GRUPO BERLIN)')?200:500;
        $body = $status_code == 200?'Correo enviado de manera exitosa al colaborador':'Error al enviar el correo al colaborador';

        return $this->response
            ->setStatusCode($status_code)
            ->setContentType('application/json')
            ->setBody(json_encode(array('isSuccess'=>$status_code == 200,'msg'=>$body)));
    }
}