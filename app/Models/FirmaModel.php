<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;
use Exception;

class FirmaModel extends Model
{
    protected $table = "Parametros";

    protected $primaryKey = "codigo";

    protected $allowedFields = [
        'cod_empleado',
        'cod_empresa',
        'cod_localida',
        'tiene_firma',
        'archivo_firma',
        'estado',
        'fecha_creacion'
    ];

    public function __construct() {
        parent::__construct();
        $db = Database::connect();
        $builder = $db->table($this->table);
    }

    /**
     * Permite obtener la información de las firmas de todos los colaboradores activos
     * @return array
     * @throws Exception
     */
    public function getFirmas():array
    {
        $query = $this->db->query("SELECT * FROM firmas_info WHERE estado IN ('A', 'N')");
        $firmas_tmp = $query->getResultArray();
        $firmas = [];

        foreach ($firmas_tmp as $firma) {
            $nombre_archivo =  utf8_encode($firma['archivo_firma']);
            $empresa = $firma['empresa'];
            $localidad = $firma['localidad'];

            /*Caso ESTEFANIA LASCANO*/
            if($firma['codigo'] == 395) {
                $firma['archivo'] = "tecnova-planta/$nombre_archivo.jpg";
            } else {
                $firma['archivo'] = empty($nombre_archivo)?"":"$empresa-$localidad/$nombre_archivo.jpg";
            }

            $firma['locfirma'] = $firma['localidad'];
            $firma['emp'] = $firma['empresa'];

            $firmas[$firma['cod_empleado']] = $firma;
        }
        unset($firmas_tmp);

        return $firmas;
    }

    /**
     * Permite obtener el link de la firma de un colaborador
     * @param string $codigo Código de la firma
     * @return string URL de la firma
     * @throws Exception
     * */
    public function getURLFirma(string $codigo):string
    {
        $query = $this->db->query("SELECT * FROM firmas_info where codigo = $codigo");

        $firma = $query->getResultArray()[0];

        $nombre_archivo =  utf8_encode($firma['archivo_firma']);
        $empresa = $firma['empresa'];
        $localidad = $firma['localidad'];

        /*Caso ESTEFANIA LASCANO*/
        if($firma['codigo'] == 395) {
            $firma['archivo'] = "tecnova-planta/$nombre_archivo.jpg";
        } else {
            $firma['archivo'] = empty($nombre_archivo)?"":"$empresa-$localidad/$nombre_archivo.jpg";
        }

        return "https://webapps.boschecuador.com/firmas/".$firma['archivo'];
    }

    /**
     * Permite verificar si el identificador recibido es valido
     * @param int $codigo Código de la firma
     * @return bool
     * @throws Exception
     */
    public function isValidID(int $codigo):bool
    {
        $query = $this->db->query("SELECT codigo FROM Parametros where codigo = $codigo");

        if(!$query) { throw new Exception("Identificador de firma inválido"); }

        return $query->getNumRows() == 1;
    }

    /**
     * Permite obtener el código de evolution de un empleado
     * @param int $codigo Código de la firma del empleado
     * @return string Código de evolution del colaborador
     * @throws Exception
     */
    public function getCodigoEmpleado(int $codigo):string
    {
        $query = $this->db->query("SELECT cod_empleado FROM Parametros where codigo = $codigo");

        if(!$query) { throw new Exception("Error al ejecutar la consulta"); }

        return $query->getResultArray()[0]['cod_empleado'];
    }

    /**
     * Permite obtener los datos de la firma de un colaborador
     * @param int $codigo Código del colaborador
     * @return array Lista con los datos del colaborador
     * @throws Exception
     */
    public function getFirmaData(int $codigo): array
    {
        $query = $this->db->query("SELECT TOP 1 * FROM firmas_info where codigo = $codigo");

        if(!$query) { throw new Exception("Error al ejecutar la consulta"); }

        return $query->getResultArray()[0];
    }

    /**
     * Obtiene la carpeta donde se encuentra una firma
     * @param int $codigo Código de la firma del colaborador
     * @return string Nombre de la carpeta donde se encuentra la firma
     * @throws Exception
     */
    public function getFolder(int $codigo):string
    {
        /*Casos especiales que hay que revisar
            ESTEFANIA LASCANO GOMEZ
        */
        if($codigo == 395) {
            return 'tecnova-hamburgo';
        }

        $query = $this->db->query("
        SELECT CONCAT(empresa,'-',localidad) as ruta
        FROM firmas_info
        WHERE codigo = $codigo
        ");

        if(!$query) { throw new Exception("Error al ejecutar la consulta"); }

        return $query->getResultArray()[0]['ruta'];
    }

    public function updateFirmaWithEvolution(array $empleado):bool
    {
        $nombre = trim($empleado['nombre']);
        $apellido = trim($empleado['apellido']);
        $codigo = trim($empleado['cod_emp']);

        if(sizeof(explode(" ", $nombre)) == 2) {
            $nombre = mb_strtolower(explode(" ", $nombre)[0]);
        }

        if(sizeof(explode(" ", $apellido)) == 2) {
            $apellido = mb_strtolower(explode(" ", $apellido)[0]);
        }

        $sql = "UPDATE Parametros SET tiene_firma = 1, archivo_firma = '$nombre-$apellido' WHERE cod_empleado = '$codigo'";

        $res = odbc_exec($this->cnx, $sql);

        if(!$res) {return false;}

        return true;
    }

    /**
     * Permite actualizar la firma de un colaborador dentro de la BD
     * @param int $codigo Código de la firma
     * @throws Exception
     */
    public function updateFirma( int $codigo):bool
    {
        $query = $this->db->query("
        UPDATE Parametros
        SET fecha_actualizacion = getdate()
        WHERE codigo = '$codigo'
        ");

        if(!$query) { return false; }

        return true;
    }

    /**
     * Permite cargar la firma de un colaborador dentro de la BD
     * @param string $nomFile Nombre del archivo de la firma
     * @param int $codigo Código de la firma
     * @throws Exception
     */
    public function cargarFirma(string $nomFile, int $codigo):bool
    {
        if(sizeof(explode('.', $nomFile)) != 1) {
            $nomFile = explode('.', $nomFile)[0];
        }

        $query = $this->db->query("
        UPDATE Parametros
        SET tiene_firma = 1, archivo_firma = '$nomFile', fecha_carga = getdate()
        WHERE codigo = '$codigo'
        ");

        if(!$query) { return false; }

        return true;
    }

    /**
     * Permite insertar un nuevo registro de Evolution a la Bd de Firmas
     * @param array $empleado Datos del empleado para insertar
     * @throws Exception
     */
    public function insertData(array $empleado):bool
    {
        $cod_empleado = $empleado['codigo'];
        $cod_empresa = $empleado['empresa'];
        $cod_localidad = $empleado['localidad'];
        $extension = $empleado['extension'];
        $correo = $empleado['correo'];

        $query = $this->db->query("
        INSERT INTO Parametros(
                               COD_EMPLEADO, 
                               COD_EMPRESA, 
                               COD_LOCALIDAD,
                               ESTADO,
                               extension,
                               correo
            ) 
            VALUES (
                    '$cod_empleado',
                    '$cod_empresa',
                    '$cod_localidad',
                    'N',
                    '$extension',
                    '$correo'
            )
        ");

        if(!$query) { return false; }

        return true;
    }

    /**
     * Permite consultar si un empleado existe en la BD de Firmas
     * @param string $codigo Código del empleado
     * @return bool
     * @throws Exception
     */
    public function checkInDB(string $codigo): bool
    {
        $query = $this->db->query("
        SELECT codigo 
        FROM Parametros
        WHERE cod_empleado = $codigo
        ");

        if(!$query) { return false; }

        return $query->getNumRows() == 1;
    }

    /**
     * Permite obtener la dirección de correo electrónico de un colaborador
     * @param int $codigo Código del empleado
     * @return string Correo electrónico del empleado
     * @throws Exception
     */
    public function getCorreo(int $codigo):string
    {
        $query = $this->db->query("
        SELECT correo
        FROM Parametros
        WHERE codigo = $codigo
        ");

        if(!$query or $query->getNumRows() == 0) { return '';}

        return is_null($query->getResultArray()[0]['correo'])?'':$query->getResultArray()[0]['correo'];
    }

    /**
     * Permite actualizar la dirección de correo electrónico de un colaborador
     * @param int $codigo Código del empleado
     * @param string $correo Correo electrónico del empleado
     * @return bool
     * @throws Exception
     */
    public function updateCorreo(int $codigo, string $correo):bool
    {
        $query = $this->db->query("
        UPDATE
            Parametros
        SET
            correo = '$correo'
        WHERE 
            codigo = $codigo
        ");

        if(!$query) { return false; }

        return true;
    }

    /**
     * Permite obtener la extensión de un colaborador
     * @param string $codigo Código del colaborador
     * @return string Código(s) de la extensión
     * @throws Exception
     */
    public function getExtension(string $codigo):string
    {
        $query = $this->db->query("
        SELECT extension
        FROM Parametros
        WHERE codigo = $codigo
        ");

        if(!$query or $query->getNumRows() == 0) {return '';}

        return is_null($query->getResultArray()[0]['extension'])?'':$query->getResultArray()[0]['extension'];
    }

    /**
     * Permite actualizar la extensión de un colaborador
     * @param string $codigo Código del colaborador
     * @param string $extension Extensión del colaborador
     * @return bool
     * @throws Exception
     */
    public function updateExtension(string $codigo, string $extension):bool
    {
        $query = $this->db->query("
        UPDATE Parametros
        SET extension = '$extension'
        WHERE codigo = $codigo
        ");

        if(!$query) {return false;}

        return true;
    }

    /**
     * Permite actualizar la extensión y el correo de un colaborador en la BD de firmas
     * @param array $empleado Arreglo con los datos del empleado
     * @return bool
     * @throws Exception
     */
    public function updateData(array $empleado): bool
    {
        $codigo = $empleado['codigo'];
        $correo = $empleado['correo'];
        $extension = $empleado['extension'];

        $query = $this->db->query("
        UPDATE Parametros
        SET correo = '$correo', extension = '$extension'
        WHERE cod_empleado = $codigo
        ");

        if(!$query) { return false; }

        return true;
    }
}