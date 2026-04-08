<?php
class adelanto
{
    private $pdo;

    public $id;
    public $id_cliente;
    public $monto;
    public $descripcion;
    public $forma_pago;
    public $comprobante;
    public $fecha;
    public $id_usuario;
    public $anulado;
    public $id_usuario_anulo;
    public $estado;
    public $activo;

    public function __CONSTRUCT()
    {
        try {
            $this->pdo = Database::StartUp();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Listar()
    {
        try {
            $stm = $this->pdo->prepare("SELECT a.*, c.nombre as cliente_nombre, u.user as usuario_creador, ua.user as usuario_anulador 
                                        FROM adelantos a 
                                        JOIN clientes c ON a.id_cliente = c.id 
                                        LEFT JOIN usuario u ON a.id_usuario = u.id 
                                        LEFT JOIN usuario ua ON a.id_usuario_anulo = ua.id 
                                        WHERE a.activo = 'SI' 
                                        ORDER BY a.id DESC");
            $stm->execute();

            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ListarPendientesPorCliente($id_cliente)
    {
        try {
            $stm = $this->pdo->prepare("SELECT * FROM adelantos 
                                        WHERE id_cliente = ? AND estado = 'PENDIENTE' AND anulado = 0 AND activo = 'SI' 
                                        ORDER BY id DESC");
            $stm->execute(array($id_cliente));

            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Obtener($id)
    {
        try {
            $stm = $this->pdo->prepare("SELECT * FROM adelantos WHERE id = ?");
            $stm->execute(array($id));

            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Registrar(adelanto $data)
    {
        try {
            $sql = "INSERT INTO adelantos (id_cliente, monto, descripcion, forma_pago, comprobante, fecha, id_usuario, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $this->pdo->prepare($sql)
                ->execute(
                    array(
                        $data->id_cliente,
                        $data->monto,
                        $data->descripcion,
                        $data->forma_pago,
                        $data->comprobante,
                        $data->fecha,
                        $data->id_usuario,
                        'PENDIENTE'
                    )
                );

            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Actualizar($data)
    {
        try {
            $sql = "UPDATE adelantos SET 
                        id_cliente = ?, 
                        monto = ?, 
                        descripcion = ?, 
                        forma_pago = ?,
                        comprobante = ?,
                        fecha = ? 
                    WHERE id = ?";

            $this->pdo->prepare($sql)
                ->execute(
                    array(
                        $data->id_cliente,
                        $data->monto,
                        $data->descripcion,
                        $data->forma_pago,
                        $data->comprobante,
                        $data->fecha,
                        $data->id
                    )
                );
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Anular($id, $id_usuario_anulo)
    {
        try {
            $stm = $this->pdo->prepare("UPDATE adelantos SET anulado = 1, id_usuario_anulo = ?, estado = 'ANULADO' WHERE id = ?");
            $stm->execute(array($id_usuario_anulo, $id));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function CambiarEstado($id, $estado)
    {
        try {
            $stm = $this->pdo->prepare("UPDATE adelantos SET estado = ? WHERE id = ?");
            $stm->execute(array($estado, $id));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Eliminar($id)
    {
        try {
            $stm = $this->pdo->prepare("UPDATE adelantos SET activo = 'NO' WHERE id = ?");
            $stm->execute(array($id));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
