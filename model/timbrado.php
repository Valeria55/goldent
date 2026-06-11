<?php
class timbrado
{
    private $pdo;
    
    public $id;
    public $timbrado;
    public $fecha_inicio;
    public $fecha_fin;
    public $numero_inicio;
    public $numero_fin;
    public $establecimiento;
    public $punto_expedicion;
    public $estado;

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
            $stm = $this->pdo->prepare("SELECT * FROM timbrados ORDER BY id DESC");
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Obtener($id)
    {
        try {
            $stm = $this->pdo->prepare("SELECT * FROM timbrados WHERE id = ?");
            $stm->execute(array($id));
            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Registrar(timbrado $data)
    {
        try {
            $sql = "INSERT INTO timbrados (timbrado, fecha_inicio, fecha_fin, numero_inicio, numero_fin, establecimiento, punto_expedicion, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $this->pdo->prepare($sql)->execute(
                array(
                    $data->timbrado, 
                    $data->fecha_inicio,
                    $data->fecha_fin,
                    $data->numero_inicio,
                    $data->numero_fin,
                    $data->establecimiento,
                    $data->punto_expedicion,
                    $data->estado ?? 0
                )
            );
            return "Agregado";
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Actualizar(timbrado $data)
    {
        try {
            $sql = "UPDATE timbrados SET 
                        timbrado = ?,
                        fecha_inicio = ?,
                        fecha_fin = ?,
                        numero_inicio = ?,
                        numero_fin = ?,
                        establecimiento = ?,
                        punto_expedicion = ?,
                        estado = ?
                    WHERE id = ?";

            $this->pdo->prepare($sql)->execute(
                array(
                    $data->timbrado,
                    $data->fecha_inicio,
                    $data->fecha_fin,
                    $data->numero_inicio,
                    $data->numero_fin,
                    $data->establecimiento,
                    $data->punto_expedicion,
                    $data->estado,
                    $data->id
                )
            );
            return "Modificado";
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Eliminar($id)
    {
        try {
            $stm = $this->pdo->prepare("DELETE FROM timbrados WHERE id = ?");
            $stm->execute(array($id));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Activar($id)
    {
        try {
            $this->pdo->beginTransaction();
            
            // Desactivar todos
            $this->pdo->exec("UPDATE timbrados SET estado = 0");
            
            // Activar el seleccionado
            $stm = $this->pdo->prepare("UPDATE timbrados SET estado = 1 WHERE id = ?");
            $stm->execute(array($id));
            
            $this->pdo->commit();
            return "Activado";
        } catch (Exception $e) {
            $this->pdo->rollBack();
            die($e->getMessage());
        }
    }
}
