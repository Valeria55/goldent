<?php
class cliente
{
    private $pdo;

    public $id;
    public $ruc;
    public $nombre;
    public $nick;
    public $correo;
    public $pass;
    public $telefono;
    public $cumple;
    public $direccion;
    public $fecha_registro;
    public $foto_perfil;
    public $sucursal;
    public $puntos;
    public $gastado;
    public $mayorista;
    public $cliente;
    public $proveedor;

    public function __CONSTRUCT()
    {
        try
        {
            $this->pdo = Database::StartUp();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Listar()
    {
        try
        {
            $result = array();

            $stm = $this->pdo->prepare("SELECT * FROM clientes WHERE anulado IS NULL ORDER BY id DESC");
            $stm->execute();

            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ListarCumple($dia, $mes)
    {
        try
        {
            $result = array();

            $stm = $this->pdo->prepare("SELECT * FROM clientes WHERE DAY(cumple) = ? AND MONTH(cumple) = ? ORDER BY id DESC");
            $stm->execute(array($dia, $mes));

            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ListarClientes()
    {
        try
        {
            $result = array();

            $stm = $this->pdo->prepare("SELECT * FROM clientes WHERE cliente = 1 ORDER BY id DESC");
            $stm->execute();

            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ConsultaCde($ruc)
    {
        try
        {
            $result = array();

            $stm = $this->pdo->prepare("SELECT *, CONCAT(nombre,' ',apellido) AS persona FROM consultacde WHERE cedula=?");
            $stm->execute(array($ruc));

            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ListarMayorista()
    {
        try
        {
            $result = array();

            $stm = $this->pdo->prepare("SELECT * FROM clientes WHERE mayorista = 'SI' ORDER BY id DESC");
            $stm->execute();

            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ListarProveedores()
    {
        try
        {
            $result = array();

            $stm = $this->pdo->prepare("SELECT * FROM clientes WHERE proveedor = 1 ORDER BY id DESC");
            $stm->execute();

            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ListarFuncionarios()
    {
        try
        {
            $result = array();

            $stm = $this->pdo->prepare("SELECT * FROM clientes ORDER BY id DESC");
            $stm->execute();

            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function BuscarRuc($ruc)
    {
        try
        {
            $stm = $this->pdo->prepare("SELECT * FROM clientes WHERE ruc = ?");
            $stm->execute(array($ruc));

            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Obtener($id)
    {
        try
        {
            $stm = $this->pdo->prepare("SELECT * FROM clientes WHERE id = ?");
            $stm->execute(array($id));

            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    

    // public function Eliminar($id)
    // {
    //     try
    //     {
    //         $stm = $this->pdo
    //             ->prepare("DELETE FROM clientes WHERE id = ?");

    //         $stm->execute(array($id));
    //     } catch (Exception $e) {
    //         die($e->getMessage());
    //     }
    // }

    
    public function Anular($id)
    {
        try
        {
            $stm = $this->pdo
                ->prepare("UPDATE clientes SET anulado = 1 WHERE id = ?");

            $stm->execute(array($id));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function SumarPuntos($puntos, $id_cliente)
    {
        try
        {
            $stm = $this->pdo
                ->prepare("UPDATE clientes SET puntos = puntos + ? WHERE id = ?");

            $stm->execute(array($puntos, $id_cliente));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function SumarGastos($gastos, $id_cliente)
    {
        try
        {
            $stm = $this->pdo
                ->prepare("UPDATE clientes SET gastado = gastado + ? WHERE id = ?");

            $stm->execute(array($gastos, $id_cliente));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Actualizar($data)
    {
        try
        {
            if ($data->foto_perfil!='') {
                $sql = "UPDATE clientes SET
                        ruc              = ?,
                        nombre           = ?,
                        nick             = ?,
                        correo           = ?,
                        pass             = ?,
                        telefono         = ?,
                        cumple           = ?,
                        direccion        = ?,
                        foto_perfil      = ?,
                        sucursal         = ?,
                        puntos           = ?,
                        gastado          = ?,
                        mayorista        = ?

                    WHERE id = ?";

                    $this->pdo->prepare($sql)
                    ->execute(
                        array(
                            $data->ruc,
                            $data->nombre,
                            $data->nick,
                            $data->correo,
                            $data->pass,
                            $data->telefono,
                            $data->cumple,
                            $data->direccion,
                            $data->foto_perfil,
                            $data->sucursal,
                            $data->puntos,
                            $data->gastado,
                            $data->mayorista,
                            $data->id
                        )
                    );
            }else{
                $sql = "UPDATE clientes SET
                        ruc              = ?,
                        nombre           = ?,
                        nick             = ?,
                        correo           = ?,
                        pass             = ?,
                        telefono         = ?,
                        cumple           = ?,
                        direccion        = ?,
                        sucursal         = ?,
                        cliente          = ?,
                        proveedor        = ?,
                        puntos           = ?,
                        gastado          = ?,
                        mayorista        = ?

                    WHERE id = ?";

                    $this->pdo->prepare($sql)
                    ->execute(
                        array(
                            $data->ruc,
                            $data->nombre,
                            $data->nick,
                            $data->correo,
                            $data->pass,
                            $data->telefono,
                            $data->cumple,
                            $data->direccion,
                            $data->sucursal,
                            $data->cliente,
                            $data->proveedor,
                            $data->puntos,
                            $data->gastado,
                            $data->mayorista,
                            $data->id
                        )
                    );
            }
            
        } catch (Exception $e) {
            if(($e->getCode() == 23000)){
                die('EL RUC YA EXISTE');
            }else{
                die($e->getMessage());
            }
        }
    }

    public function Registrar(cliente $data)
    {
        try
        {   
            if ($data->foto_perfil!='') {
                $sql = "INSERT INTO clientes (ruc, nombre, nick, correo, pass, telefono, cumple, direccion, fecha_registro, foto_perfil, sucursal,cliente, proveedor, puntos, gastado, mayorista)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,  ?, ?, ?, ?, ?)";

                $this->pdo->prepare($sql)
                    ->execute(
                        array(
                            $data->ruc,
                            $data->nombre,
                            $data->nick,
                            $data->correo,
                            $data->pass,
                            $data->telefono,
                            $data->cumple,
                            $data->direccion,
                            $data->fecha_registro,
                            $data->foto_perfil,
                            $data->sucursal,
                            $data->cliente,
                            $data->proveedor,
                            $data->puntos,
                            $data->gastado,
                            $data->mayorista
                        )
                    );
            }else{
                $sql = "INSERT INTO clientes (ruc, nombre, nick, correo, pass, telefono, cumple, direccion, fecha_registro, sucursal, cliente, proveedor, puntos, gastado, mayorista)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $this->pdo->prepare($sql)
                    ->execute(
                        array(
                            $data->ruc,
                            $data->nombre,
                            $data->nick,
                            $data->correo,
                            $data->pass,
                            $data->telefono,
                            $data->cumple,
                            $data->direccion,
                            $data->fecha_registro,
                            $data->sucursal,
                            $data->cliente,
                            $data->proveedor,
                            $data->puntos,
                            $data->gastado,
                            $data->mayorista
                        )
                    );
            }
            header('Location:' . getenv('HTTP_REFERER'));
            
        } catch (Exception $e) {
            if(($e->getCode() == 23000)){
                die('<script>
                        alert("EL RUC YA EXISTE");
                        window.history.back ();
                    </script>');
            }else{
                die($e->getMessage());
            }
        }
    }

}
