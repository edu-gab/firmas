<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;
use Exception;

class UsuarioModel extends Model
{
    protected $table = "Usuario";

    protected $primaryKey = "IdUsuario";

    protected $allowedFields = [
        'UserName',
        'Nombre',
        'Correo'
    ];

    public function __construct() {
        parent::__construct();
        $this->db = Database::connect();
        $this->builder = $this->db->table($this->table);
    }

    /**
     * Permite verificar si el usuario tiene permisos para acceder al sistema
     * @param string $username
     * @return bool
     * @throws Exception
     */
    public function verifyUser(string $username):bool
    {
        $query = $this->db->query("SELECT IdUsuario FROM Usuario WHERE UserName = '$username'");

        if( !$query ) { throw new Exception("Ocurrió un error al consultar la base de datos."); }

        return $query->getNumRows() == 1;
    }

    /**
     * Permite obtener los datos del usuario de la BD
     * @param string $username
     * @return array|null
     * @throws Exception
     */
    public function getUserData(string $username):array|null
    {
        $query = $this->db->query("SELECT IdUsuario, UserName, Nombre, Correo, FechaCreacion FROM Usuario WHERE UserName = '$username' and Estado = 1");

        if( !$query ) { throw new Exception("Ocurrió un error al consultar la base de datos."); }

        if($query->getNumRows() == 0) { return null; }

        $result = $query->getResultArray()[0];

        return [
            'id'                =>          $result['IdUsuario'],
            'username'          =>          $result['UserName'],
            'nombre'            =>          $result['Nombre'],
            'correo'            =>          $result['Correo'],
            'fecha_creacion'    =>          $result['FechaCreacion']
        ];
    }
}