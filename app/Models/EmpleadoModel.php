<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;
use Couchbase\QueryErrorException;
use Exception;

class EmpleadoModel extends Model
{
    protected $table = "area";

    protected $primaryKey = "are_codigo";

    protected $allowedFields = [
        'are_descripcion',
        'are_estado',
        'are_usuarioi',
        'are_fechai'
    ];

    public function __construct() {
        parent::__construct();
        $this->db = Database::connect("evolution");
    }

    /**
     * Permite obtener la información de todos los empleados
     * @return array
     * @throws Exception
     */
    public function getData():array
    {
        $query = $this->db->query("
        SELECT distinct
            a.codigo as codigo,
            b.apellido as apellido,
            b.nombre as nombre,
            c.descripcion as cargo,
            empresa.descripcion as empresa,
            localidad.DESCRIPCION as localidad,
            e.descripcion as dpto,
            DIRECCION_E_MAIL as correo,
            tel.NUMERO as extension
        FROM
            hr_1 as a
                left join hr_a_26 as tel ON
                        a.codigo = tel.codigo and
                        tel.cod_tipo_telef = 4 and
                        TEL.NUMERO IS NOT NULL,
            HR_5 as b,
            HR_P_20 as c,
            EMPRESAS as empresa,
            AD_3 as e,
            LOCALIDADES as localidad
        WHERE
                empresa.cod_empresa IN ('0007', '00008', '00300') AND
                a.codigo = b.codigo and
                a.status = 'A' and
                c.COD_PUESTO = a.cod_puesto and
                a.cod_empresa = empresa.cod_empresa and
                e.cod_cost = a.cod_cost and
                a.COD_GRUPO_EMPLEADO IN('00002', '00008', '00010') AND
                localidad.COD_LOC = a.COD_LOC
        
        UNION ALL
        
        SELECT distinct
            a.codigo as codigo,
            b.apellido as apellido,
            b.nombre as nombre,
            c.descripcion as cargo,
            empresa.descripcion as empresa,
            localidad.DESCRIPCION as localidad,
            e.descripcion as dpto,
            DIRECCION_E_MAIL as correo,
            tel.NUMERO as extension
        FROM
            hr_1 as a
                left join hr_a_26 as tel ON
                        a.codigo = tel.codigo and
                        tel.cod_tipo_telef = 4 and
                        TEL.NUMERO IS NOT NULL,
            HR_5 as b,
            HR_P_20 as c,
            EMPRESAS as empresa,
            AD_3 as e,
            LOCALIDADES as localidad
        WHERE
                DIRECCION_E_MAIL LIKE '%@grupoberlin.com' AND
                empresa.cod_empresa IN ('0007', '00008', '00300') AND
                a.codigo = b.codigo and
                a.status = 'A' and
                c.COD_PUESTO = a.cod_puesto and
                a.cod_empresa = empresa.cod_empresa and
                e.cod_cost = a.cod_cost and
                a.COD_GRUPO_EMPLEADO IN('00001') AND
                localidad.COD_LOC = a.COD_LOC
        ");

        return $query->getResultArray();
    }

    /**
     * Permite obtener la información de Evolution de un empleado
     * @param string $codigo Código del empleado
     * @return array
     * @throws Exception
     */
    public function getEmpleadoData(string $codigo):array
    {
        $query = $this->db->query("
        SELECT distinct
            b.apellido as apellido,
            b.nombre as nombre,
            c.descripcion as cargo,
            empresa.descripcion as empresa,
            localidad.DESCRIPCION as localidad,
            e.descripcion as dpto,
            DIRECCION_E_MAIL as correo,
            tel.NUMERO as extension
        FROM
            hr_1 as a
            left outer join hr_a_26 as tel ON
                a.codigo = tel.codigo and
                tel.cod_tipo_telef = 4 and
                TEL.NUMERO IS NOT NULL,
            HR_5 as b,
            HR_P_20 as c,
            EMPRESAS as empresa,
            AD_3 as e,
            LOCALIDADES as localidad
        WHERE
            empresa.cod_empresa IN ('0007', '00008', '00300') AND
            a.codigo = b.codigo and
            a.status = 'A' and
            c.COD_PUESTO = a.cod_puesto and
            a.cod_empresa = empresa.cod_empresa and
            e.cod_cost = a.cod_cost and
            a.COD_GRUPO_EMPLEADO IN('00002', '00008', '00010', '00001') AND
            localidad.COD_LOC = a.COD_LOC AND 
            a.codigo = $codigo
        order by empresa.descripcion, b.apellido
        ");

        if(!$query) { throw new Exception("Error en la consulta"); }

        return $query->getNumRows() != 0?$query->getResultArray()[0]:[];
    }

    /**
     * Permite obtener el código de evolution del empleado
     * @param string $nombre Nombres del empleado
     * @return string Código del empleado
     * @throws Exception
     * */
    public function getCodigo(string $nombre):string
    {
        $query = $this->db->query("
        SELECT distinct
            b.COD_ALTERNO as codigo
        FROM
            hr_1 as a,
            HR_5 as b
        WHERE
            a.cod_empresa IN ('0007', '00008', '00300') AND
            a.codigo = b.codigo and
            a.status = 'A' and
            a.COD_GRUPO_EMPLEADO IN('00002', '00008', '00010', '00001') AND
            CONCAT(b.NOMBRE,' ', b.APELLIDO) = upper('$nombre')
        order by b.COD_ALTERNO
        ");

        if(!$query) { throw new QueryErrorException('Error en la consulta'); }

        if($query->getNumRows() == 0) { return ''; }

        return $query->getResultArray()[0]['codigo'];
    }

    /**
     * Permite traer los datos necesarios para la actualización de la Base de Datos de las Firmas
     * @return array
     * @throws Exception
     */
    public function getDataToUpdate():array
    {
        $query = $this->db->query("
        SELECT distinct
            a.codigo as codigo,
            empresa.COD_EMPRESA as empresa,
            localidad.COD_LOC as localidad,
            DIRECCION_E_MAIL as correo,
            tel.NUMERO as extension
        FROM
            hr_1 as a
                left join hr_a_26 as tel ON
                        a.codigo = tel.codigo and
                        tel.cod_tipo_telef = 4 and
                        TEL.NUMERO IS NOT NULL,
            HR_5 as b,
            HR_P_20 as c,
            EMPRESAS as empresa,
            AD_3 as e,
            LOCALIDADES as localidad
        WHERE
            empresa.cod_empresa IN ('0007', '00008', '00300') AND
            a.codigo = b.codigo and
            a.status = 'A' and
            c.COD_PUESTO = a.cod_puesto and
            a.cod_empresa = empresa.cod_empresa and
            e.cod_cost = a.cod_cost and
            a.COD_GRUPO_EMPLEADO IN('00002', '00008', '00010') AND
            localidad.COD_LOC = a.COD_LOC
        
        UNION ALL
        
        SELECT distinct
            a.codigo as codigo,
            empresa.COD_EMPRESA as empresa,
            localidad.COD_LOC as localidad,
            DIRECCION_E_MAIL as correo,
            tel.NUMERO as extension
        FROM
            hr_1 as a
                left join hr_a_26 as tel ON
                        a.codigo = tel.codigo and
                        tel.cod_tipo_telef = 4 and
                        TEL.NUMERO IS NOT NULL,
            HR_5 as b,
            HR_P_20 as c,
            EMPRESAS as empresa,
            AD_3 as e,
            LOCALIDADES as localidad
        WHERE
            DIRECCION_E_MAIL LIKE '%@grupoberlin.com' AND
            empresa.cod_empresa IN ('0007', '00008', '00300') AND
            a.codigo = b.codigo and
            a.status = 'A' and
            c.COD_PUESTO = a.cod_puesto and
            a.cod_empresa = empresa.cod_empresa and
            e.cod_cost = a.cod_cost and
            a.COD_GRUPO_EMPLEADO IN('00001') AND
            localidad.COD_LOC = a.COD_LOC
        ");

        return $query->getResultArray();
    }

    /**
     * Permite obtener el número de celular de un colaborador
     * @param string $codigo Código del colaborador
     * @return string Número de celular
     * @throws Exception
     */
    public function getCelular(string $codigo):string
    {
        $query = $this->db->query("
        SELECT distinct 
            tel.NUMERO as cel
        FROM
            hr_1 as a
            left outer join hr_a_26 as tel ON
                a.codigo = tel.codigo and
                tel.cod_tipo_telef = 3 and
                TEL.NUMERO IS NOT NULL
        WHERE
            NUMERO IS NOT NULL AND
            a.status = 'A' and
            a.COD_GRUPO_EMPLEADO IN('00002', '00008', '00010') and
            a.CODIGO in('$codigo')
        ");

        if($query->getNumRows() == 0) {return '';}

        return $query->getResultArray()[0]['cel'];
    }

    /**
     * Permite obtener el código de un colaborador
     * @param string $codigo Código del colaborador
     * @return array Correo y nombres del colaborador
     * @throws Exception
     * */
    public function getCorreoData(string $codigo):array
    {
        $query = $this->db->query("
        SELECT 
            DIRECCION_E_MAIL as correo,
            CONCAT(APELLIDO, ' ', NOMBRE) as colaborador
        FROM 
            EMPLEADOS
        WHERE 
            STATUS_EMPLEADO = 'A' AND
            CODIGO = '$codigo'
        ");

        if(!$query){ throw new Exception('Query Exception');}

        $correo = $query->getResultArray()[0]['correo'];
        $colaborador = ucwords(strtolower($query->getResultArray()[0]['colaborador']));

        // Verificamos si el campo es nulo
        if(is_null($correo)) { $correo = ''; }

        return array('correo'=>$correo, 'colaborador'=>$colaborador);
    }
}