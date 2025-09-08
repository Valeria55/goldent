<?php
class factura
{
    private $pdo;

    public $id;
    public $id_factura;
    public $id_cliente;
    public $id_vendedor;
    public $id_producto;
    public $precio_venta;
    public $cantidad;
    public $fecha_factura;
    public $anulado;
    public $descuento;
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
            session_start();
            $userId = $_SESSION['user_id'];
            if (($_SESSION['nivel'] < 3)) {
                $vendedor = "";
            } else {
                $vendedor = "AND v.id_vendedor = '$userId'";
            }

            $result = array();

            $stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_venta)-(v.descuento)) AS total, SUM(v.cantidad) AS cant_items
			FROM facturas v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE 1=1 $vendedor GROUP BY v.id_factura ORDER BY v.id DESC");
            $stm->execute(array());

            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ListarFactura($id_factura)
	{
		try
		{
			
				$stm = $this->pdo->prepare("SELECT  v.descuento AS descuentov, v.id_factura AS id_venta , v.id, p.producto,v.anulado,  p.codigo, SUM(v.cantidad) AS cantidad, v.precio_venta, SUM(v.cantidad*v.precio_venta) AS subtotal, SUM(descuento) AS descuento, SUM(v.cantidad*v.precio_venta) AS total, fecha_factura, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, p.iva,
                (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, v.id_cliente, v.cot_dolar
                FROM facturas v 
                LEFT JOIN productos p ON v.id_producto = p.id 
                LEFT JOIN clientes c ON v.id_cliente = c.id 
                WHERE v.id_factura = ?
                GROUP BY v.id_producto");
				$stm->execute(array($id_factura));
			

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
    public function ListarDetalle($id_factura)
    {
        try {

            $result = array();

            $stm = $this->pdo->prepare("SELECT *, v.id as id, p.codigo
			FROM facturas v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE id_factura = ? ORDER BY v.id DESC");
            $stm->execute(array($id_factura));

            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
//     public function Listarfactura($id_factura)
//     {
//         try {

//             $result = array();

//             $stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_venta)-(((v.cantidad*v.precio_venta)*v.descuento))/100) AS total
// 			FROM facturas v 
// 			LEFT JOIN productos p ON v.id_producto = p.id
// 			LEFT JOIN usuario u ON v.id_vendedor = u.id
// 			LEFT JOIN clientes c ON v.id_cliente = c.id
// 			WHERE id_factura = ? ORDER BY v.id DESC");
//             $stm->execute(array($id_factura));

//             return $stm->fetchAll(PDO::FETCH_OBJ);
//         } catch (Exception $e) {
//             die($e->getMessage());
//         }
//     }
    public function ObtenerId_factura($id)
    {
        try {
            $stm = $this->pdo->prepare("SELECT * FROM facturas WHERE id_factura = ?");


            $stm->execute(array($id));
            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Obtener()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                session_start();
            }
            $user_id = $_SESSION['user_id'];
            $stm = $this->pdo
                ->prepare("SELECT * FROM facturas  WHERE id_vendedor = '$user_id' GROUP BY id_factura");


            $stm->execute();
            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Ultimo()
    {
        try {
            $stm = $this->pdo
                ->prepare("SELECT MAX(id_factura) as id_factura FROM facturas");
            $stm->execute();
            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ObtenerMoneda()
    {
        try {
            $stm = $this->pdo
                ->prepare("SELECT * FROM monedas WHERE id = 1");


            $stm->execute();
            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Eliminar($id)
    {
        try {
            $stm = $this->pdo
                ->prepare("DELETE FROM facturas WHERE id = ?");

            $stm->execute(array($id));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Vaciar()
    {
        try {
            session_start();
            $id_vendedor = $_SESSION['user_id'];
            $stm = $this->pdo
                ->prepare("DELETE FROM facturas WHERE id_vendedor = ? ");
            $stm->execute(array($id_vendedor));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CambiarEstado($data)
    {
        try {
            $stm = $this->pdo
                ->prepare("UPDATE facturas SET estado='Vendido' WHERE id_factura = ? ");
            $stm->execute(array($data->id_factura));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Actualizar($data)
    {
        try {
            $sql = "UPDATE facturas SET
						id_factura     = ?,
						id_vendedor     = ?,
						id_producto     = ?,
						precio_venta   = ?,
                        cantidad      = ?, 
						fecha_factura      = ?
						
				    WHERE id = ?";

            $this->pdo->prepare($sql)
                ->execute(
                    array(
                        $data->id_factura,
                        $data->id_vendedor,
                        $data->id_producto,
                        $data->precio_venta,
                        $data->cantidad,
                        $data->fecha_factura,
                        $data->id
                    )
                );
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Moneda($data)
    {
        try {
            $sql = "UPDATE monedas SET
						reales     = ?,
						dolares     = ?,
						monto_inicial = ?
						";

            $this->pdo->prepare($sql)
                ->execute(
                    array(
                        $data->reales,
                        $data->dolares,
                        $data->monto_inicial
                    )
                );
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Registrar($data)
    {
        try {
            $sql = "INSERT INTO facturas (id_factura, id_cliente, id_vendedor, id_producto, precio_venta, cantidad, fecha_factura, descuento, cot_dolar, nro_comprobante) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $this->pdo->prepare($sql)
                ->execute(
                    array(
                        $data->id_factura,
                        $data->id_cliente,
                        $data->id_vendedor,
                        $data->id_producto,
                        $data->precio_venta,
                        $data->cantidad,
                        $data->fecha_factura,
                        $data->descuento,
                        // $data->estado,
                        $data->cot_dolar,
                        $data->nro_comprobante


                    )
                );
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
