<?php
class factura_tmp
{
	private $pdo;
    
    public $id;
    public $id_factura;
    public $id_vendedor;
    public $id_producto;
    public $precio_venta;
    public $cantidad;
    public $descuento;
    public $fecha_factura;  
    
	public function __CONSTRUCT()
	{
		try
		{
			$this->pdo = Database::StartUp();     
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function Listar()
	{
		try
		{
			if(!isset($_SESSION['user_id'])){
				session_start();
			}
			$userId= $_SESSION['user_id'];
			$result = array();

			$stm = $this->pdo->prepare("SELECT v.id, v.id_producto,v.descuento, p.codigo, v.id_vendedor, v.precio_venta, p.producto, p.precio_costo, v.cantidad, v.id_factura
			FROM facturas_tmp v 
			LEFT JOIN productos p ON v.id_producto = p.id
			WHERE id_vendedor = ? ORDER BY v.id DESC");
			$stm->execute(array($userId));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function Obtener()
	{
		try 
		{
		    if(!isset($_SESSION['user_id'])){
				session_start();
			}
		    $user_id = $_SESSION['user_id'];
			$stm = $this->pdo
			          ->prepare("SELECT *, SUM((precio_venta*cantidad)-descuento) as monto FROM facturas_tmp  WHERE id_vendedor = '$user_id' GROUP BY id_factura");
			          

			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function Ultimo()
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT MAX(id_factura) as id_factura FROM facturas_tmp");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerMoneda()
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM monedas WHERE id = 1");
			          

			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Eliminar($id)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("DELETE FROM facturas_tmp WHERE id = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Vaciar()
	{
		try 
		{
		    session_start();
		    $id_vendedor = $_SESSION['user_id'];
			$stm = $this->pdo
			            ->prepare("DELETE FROM facturas_tmp WHERE id_vendedor = ? ");
			$stm->execute(array($id_vendedor));			          

		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try 
		{
			$sql = "UPDATE facturas_tmp SET
						id_factura     = ?,
						id_vendedor     = ?,
						id_producto     = ?,
						precio_venta   = ?,
                        cantidad      = ?, 
						margen_ganancia     = ?,
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
                        $data->margen_ganancia, 
                        $data->fecha_factura,
                        $data->id
					)
				);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Moneda($data)
	{
		try 
		{
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
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar($data)
	{
		try 
		{
		$sql = "INSERT INTO facturas_tmp (id_factura, id_vendedor, id_producto, precio_venta, cantidad, descuento, fecha_factura) 
		        VALUES (?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
                    $data->id_factura,
                    $data->id_vendedor,
                    $data->id_producto,                 
                    $data->precio_venta,
                    $data->cantidad,
                    $data->descuento, 
                    $data->fecha_factura 
                   
                )
			);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}