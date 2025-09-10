<?php
class ingreso
{
	private $pdo;

	public $id;
	public $id_cliente;
	public $id_usuario;
	public $id_caja;
	public $id_venta;
	public $id_deuda;
	public $fecha;
	public $categoria;
	public $concepto;
	public $comprobante;
	public $monto;
	public $forma_pago;
	public $sucursal;
	public $anulado;
	public $id_gift;
	public $id_usuario_transferencia = null;
	public $id_compra;
	public $moneda;
	public $cambio;
	public $id_transferencia;

	public function __CONSTRUCT()
	{
		try {
			$this->pdo = Database::StartUp();
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Listar($fecha)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, i.id as id 
				FROM ingresos i 
				LEFT JOIN clientes c ON i.id_cliente = c.id 
				WHERE i.categoria <> 'Transferencia' AND cast(i.fecha as date) = ? ORDER BY i.fecha DESC");
			$stm->execute(array($fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function Listarr()
	{
		try {
			if (!isset($_SESSION)) session_start();
			$id_usuario = $_SESSION['user_id'];
			if ($_SESSION['nivel'] == 1) {

				$result = array();

				$stm = $this->pdo->prepare("SELECT *, i.id as id FROM ingresos i 
				  LEFT JOIN clientes c ON i.id_cliente = c.id 
				  WHERE cast(i.fecha as date) = ? 
				  ORDER BY i.fecha DESC");
				$stm->execute();
			} else {

				$result = array();

				$stm = $this->pdo->prepare("SELECT *, i.id as id FROM ingresos i 
				 LEFT JOIN clientes c ON i.id_cliente = c.id 
				 WHERE i.id_usuario=$id_usuario
				 ORDER BY i.fecha DESC");
				$stm->execute();
			}


			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function consultarVenta($id_venta)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT SUM(monto) AS total FROM ingresos WHERE id_venta = ?");
			$stm->execute(array($id_venta));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function EliminarVenta($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM ingresos WHERE id_venta = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarVenta($id)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, (SELECT v.nro_comprobante FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as nro_comprobante, (SELECT v.banco FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as banco, i.id as id FROM ingresos i LEFT JOIN clientes c ON i.id_cliente = c.id WHERE i.id = ? ORDER BY i.id DESC");
			$stm->execute(array($id));

			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerCobro($id_venta)
	{
		try {
			$stm = $this->pdo->prepare("SELECT forma_pago, 'Guaranies' as moneda, monto FROM ingresos WHERE id_venta = ? AND anulado IS NULL ORDER BY id ASC");
			$stm->execute(array($id_venta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}



	public function MiLista()
	{
		try {
			if (!isset($_SESSION)) session_start();

			$stm = $this->pdo->prepare("SELECT *, i.id as id FROM ingresos i LEFT JOIN clientes c ON i.id_cliente = c.id
			    WHERE id_usuario = ? AND anulado IS NULL
			    ORDER BY i.id DESC");
			$stm->execute(array($_SESSION['user_id']));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function MiListaAnulados()
	{
		try {
			if (!isset($_SESSION)) session_start();

			$stm = $this->pdo->prepare("SELECT *, i.id as id FROM ingresos i LEFT JOIN clientes c ON i.id_cliente = c.id
			    WHERE id_usuario = ? AND anulado = 1
			    ORDER BY i.id DESC");
			$stm->execute(array($_SESSION['user_id']));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarSinAnular()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, i.id as id FROM ingresos i LEFT JOIN clientes c ON i.id_cliente = c.id WHERE anulado IS NULL ORDER BY i.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function ListarDeuda($id_deuda)
	{
		try {
			$result = array();
			$stm = $this->pdo->prepare("SELECT *, i.id as id FROM ingresos i LEFT JOIN clientes c ON i.id_cliente = c.id WHERE i.id_deuda = ? AND anulado IS NULL ORDER BY i.id DESC");
			$stm->execute(array($id_deuda));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMes($fecha)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM ingresos WHERE MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?) AND categoria <> 'Venta' ");


			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function AgrupadoMes($desde, $hasta, $anho)
	{
		try {
			$result = array();
			$mes = date('Y-m') . '-01';
			$fecha = date('Y-m-d');

			if ($anho > 0) {
				$anho = "AND YEAR(i.fecha) = $anho";
				$rango = "";
			} else {
				$anho = "";

				if ($desde != '') {
					if ($hasta != '') {
						$rango = " AND CAST(i.fecha as date) >= '$desde' AND CAST(i.fecha as date) <= '$hasta'";
					} else {
						$rango = " AND CAST(i.fecha as date) >= '$desde' AND CAST(i.fecha as date) <= '$fecha'";
					}
				} else {
					if ($hasta != '') {
						$rango = " AND CAST(i.fecha as date) >= '$mes' AND CAST(i.fecha as date) <= '$hasta'";
					} else {
						$rango = " AND CAST(i.fecha as date) >= '$mes' AND CAST(i.fecha as date) <= '$fecha'";
					}
				}
			}


			// die("SELECT i.categoria, i.fecha, SUM(i.monto) as monto FROM ingresos i WHERE anulado IS NULL $rango $anho AND i.categoria <> 'Transferencia' AND i.categoria <> 'Venta por gift card' GROUP BY i.categoria ORDER BY i.id DESC");

			$sql = "SELECT i.categoria, i.forma_pago, i.fecha, SUM(i.monto * i.cambio) as monto, moneda, cambio 
					FROM ingresos i 
					WHERE anulado IS NULL $rango $anho AND i.categoria <> 'Transferencia' AND i.categoria <> 'Venta por gift card' 
					GROUP BY i.categoria, i.forma_pago ORDER BY i.id DESC";
			$stm = $this->pdo->prepare($sql);
			$stm->execute();
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Listar_rango($desde, $hasta)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, i.id as id FROM ingresos i 
				LEFT JOIN clientes c ON i.id_cliente = c.id 
				WHERE i.categoria <> 'Transferencia' AND cast(fecha as date) >= ? AND cast(fecha as date) <= ? ORDER BY i.id DESC");
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarRangoSesion($desde, $hasta, $id_usuario)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, i.id as id FROM ingresos i LEFT JOIN clientes c ON i.id_cliente = c.id WHERE fecha >= ? AND fecha <= ? AND id_usuario = ? AND id_venta = 0 AND anulado IS NULL ORDER BY i.id DESC");
			$stm->execute(array($desde, $hasta, $id_usuario));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarSinVenta($fecha)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, (monto*cambio) as monto_guaranies FROM ingresos WHERE categoria <> 'Venta' AND categoria <> 'Transferencia' AND Cast(fecha as date) = ? AND anulado IS NULL ORDER BY id DESC");
			$stm->execute(array($fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMovimientoCajaDia($fecha)
	{
		try {
			$sql = $this->pdo->prepare("SELECT *, i.id as id, u.user as usuario FROM ingresos i 
				LEFT JOIN usuario u ON i.id_usuario = u.id 
				WHERE i.categoria = 'Transferencia' AND i.anulado IS NULL AND CAST(i.fecha as DATE) = ?;");
			$sql->execute([$fecha]);
			return $sql->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMovimiento($desde, $hasta)
	{
		$str = $desde != '' ? "AND CAST(i.fecha AS DATE) >= '$desde' AND CAST(i.fecha AS DATE) <= '$hasta'" : 'AND MONTH(i.fecha) = MONTH(CURRENT_DATE()) AND YEAR(i.fecha) = YEAR(CURRENT_DATE())';

		try {
			$sql = $this->pdo->prepare("SELECT *, i.id as id, u.user as usuario FROM ingresos i
				LEFT JOIN usuario u ON i.id_usuario = u.id 
				WHERE i.categoria = 'Transferencia' AND i.anulado IS NULL $str");
			$sql->execute();
			return $sql->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarSesion($id_usuario)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM ingresos WHERE categoria <> 'Venta' AND fecha >= (SELECT fecha_apertura FROM cierres WHERE id_usuario = ? AND fecha_cierre IS NULL) ORDER BY id DESC");
			$stm->execute(array($id_usuario));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarSinCompraMes($desde, $hasta)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, (SELECT ( ( (SUM(precio_venta*cantidad)-SUM(precio_costo*cantidad)) * 100 )/ SUM(precio_venta*cantidad)) AS ganancia FROM ventas v WHERE v.id_venta = i.id_venta GROUP BY id_venta) AS margen_ganancia, (i.monto * i.cambio) as monto_guaranies FROM ingresos i WHERE id_deuda IS NOT NULL AND CAST(fecha AS date) >= ? AND CAST(fecha AS date) <= ? AND anulado IS NULL ORDER BY id ASC");
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function EditarMonto($id_venta, $monto)
	{
		try {
			$sql = "UPDATE ingresos SET 
						monto    = ?
				    WHERE id_venta = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$monto,
						$id_venta
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Agrupado_ingreso($mes)
	{
		try {
			$result = array();
			if ($mes != '0') {
				$stm = $this->pdo->prepare("SELECT concepto, categoria, sum(monto) as monto, fecha  FROM ingresos WHERE MONTH(fecha) = $mes AND anulado IS NULL AND categoria <> 'Transferencia' GROUP BY categoria ORDER BY id DESC");
			} else {
				$stm = $this->pdo->prepare("SELECT concepto, categoria, sum(monto) as monto FROM ingresos WHERE anulado IS NULL AND categoria <> 'Transferencia' GROUP BY categoria ORDER BY id DESC");
			}
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerIngreso($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM ingresos WHERE id_gift = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerGift($id_venta)
	{
		try {
			$stm = $this->pdo->prepare("SELECT monto FROM ingresos WHERE id_venta = ? AND forma_pago = 'Gift Card'");


			$stm->execute(array($id_venta));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Obtener($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM ingresos WHERE id = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function UltimoID()
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT id FROM ingresos ORDER BY id desc LIMIT 1");


			$stm->execute(array());
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Eliminar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE ingresos SET anulado = 1 WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function AnularVenta($id)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE ingresos SET anulado = 1 WHERE id_venta = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function AnularGift($id)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE ingresos SET anulado = 1 WHERE id_gift = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE ingresos SET 
			            id_cliente     = ?,
			            id_venta       = ?,
			            fecha      	   = ?,
						categoria      = ?,
						concepto       = ?,
						comprobante    = ?, 
						monto          = ?, 
						forma_pago     = ?,
                        sucursal       = ?,
                        id_gift        = ?,
						id_compra      = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_cliente,
						$data->id_venta,
						$data->fecha,
						$data->categoria,
						$data->concepto,
						$data->comprobante,
						$data->monto,
						$data->forma_pago,
						$data->sucursal,
						$data->id_gift,
						$data->id_compra,
						$data->id
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Registrar($data)
	{
		try {
			if (!isset($_SESSION)) session_start();
			if ($data->categoria == "Transferencia") {
				$id_usuario = $data->id_usuario;
			} else {
				$id_usuario = $_SESSION['user_id'];
			}
			$sql = "INSERT INTO ingresos (id_cliente, id_usuario, id_caja, id_venta, id_deuda, fecha, categoria, concepto, comprobante, monto, moneda, cambio, forma_pago, sucursal, id_gift, id_usuario_transferencia, id_compra, id_transferencia) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_cliente,
						$id_usuario,
						$data->id_caja,
						$data->id_venta,
						$data->id_deuda,
						$data->fecha,
						$data->categoria,
						$data->concepto,
						$data->comprobante,
						$data->monto,
						$data->moneda,
						$data->cambio,
						$data->forma_pago,
						$data->sucursal,
						$data->id_gift,
						$data->id_usuario_transferencia,
						$data->id_compra,
						$data->id_transferencia
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarOtrosIngresos($desde, $hasta, $id_usuario)
	{
		try {
			$result = array();
			$fecha = date('Y-m-d');
			$where = [];
			$params = [];

			// Date range
			if ($desde != '') {
				if ($hasta != '') {
					$where[] = "i.fecha >= ?";
					$params[] = $desde;
					$where[] = "i.fecha <= ?";
					$params[] = $hasta;
				} else {
					$where[] = "i.fecha >= ?";
					$params[] = $desde;
					$where[] = "i.fecha <= ?";
					$params[] = $fecha;
				}
			}

			// User filter - only apply if id_usuario is not null/empty
			if ($id_usuario != '' && $id_usuario !== null) {
				$where[] = "i.id_usuario = ?";
				$params[] = $id_usuario;
			}

			// Include records that are not sales (id_venta = 0) OR debt collections
			$where[] = "(i.id_venta = 0 OR i.categoria = 'Cobro de deuda')";
			$where[] = "i.anulado IS NULL";
			$where[] = "i.categoria <> 'Transferencia'";


			$where_clause = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';
			$sql = "SELECT *, i.id as id FROM ingresos i LEFT JOIN clientes c ON i.id_cliente = c.id $where_clause ORDER BY i.id DESC";

			$stm = $this->pdo->prepare($sql);
			$stm->execute($params);
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerIngresosPorMoneda($id_venta)
	{
		try {
			$stm = $this->pdo->prepare("SELECT 
            i.moneda,
            SUM(i.monto) as total_monto
            FROM ingresos i
            WHERE i.id_venta = ? AND i.anulado IS NULL 
            GROUP BY i.moneda
            ORDER BY i.moneda");
			$stm->execute(array($id_venta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerCotizacionesUsadas($id_venta)
	{
		try {
			$stm = $this->pdo->prepare("SELECT DISTINCT 
            v.cot_usd,
            v.cot_rs
            FROM ventas v
            WHERE v.id_venta = ?
            AND (v.cot_usd IS NOT NULL OR v.cot_rs IS NOT NULL)
            LIMIT 1");
			$stm->execute(array($id_venta));

			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarBalance($desde, $hasta, $anho)
	{
		try {
			$result = array();
			$mes = date('Y-m') . '-01';
			$fecha = date('Y-m-d');

			if ($anho > 0) {
				$anho_condition = "AND YEAR(i.fecha) = $anho";
				$rango = "";
			} else {
				$anho_condition = "";

				if ($desde != '') {
					if ($hasta != '') {
						$rango = " AND CAST(i.fecha as date) >= '$desde' AND CAST(i.fecha as date) <= '$hasta'";
					} else {
						$rango = " AND CAST(i.fecha as date) >= '$desde' AND CAST(i.fecha as date) <= '$fecha'";
					}
				} else {
					if ($hasta != '') {
						$rango = " AND CAST(i.fecha as date) >= '$mes' AND CAST(i.fecha as date) <= '$hasta'";
					} else {
						$rango = " AND CAST(i.fecha as date) >= '$mes' AND CAST(i.fecha as date) <= '$fecha'";
					}
				}
			}

			$sql = "SELECT i.categoria, i.forma_pago, i.fecha, i.monto, i.moneda, i.cambio 
					FROM ingresos i 
					WHERE anulado IS NULL $rango $anho_condition 
					AND i.categoria <> 'Transferencia' 
					AND i.categoria <> 'Venta por gift card' 
					ORDER BY i.id DESC";
			$stm = $this->pdo->prepare($sql);
			$stm->execute();
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
