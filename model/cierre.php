<?php
class cierre
{
	private $pdo;
	public $id;
	public $fecha_apertura;
	public $fecha_cierre;
	public $id_usuario;
	public $id_caja;
	public $monto_apertura;
	public $monto_cierre;
	public $monto_cierre_rs;
	public $monto_cierre_usd;
	public $apertura_rs;
	public $apertura_usd;
	public $cot_dolar;
	public $cot_real;

	public function __CONSTRUCT()
	{
		if (!isset($_SESSION)) session_start();
		try {
			$this->pdo = Database::StartUp();
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Listar($desde, $hasta)
	{
		try {
			$result = array();
			$rango = ($desde == 0) ? "" : "AND c.fecha_cierre >= '$desde' AND c.fecha_cierre <= '$hasta'";
			$stm = $this->pdo->prepare("SELECT *, c.id as id, 
        (SELECT SUM(i.monto * i.cambio) FROM ingresos i WHERE i.fecha>=c.fecha_apertura AND i.fecha<=c.fecha_cierre AND i.anulado IS NULL AND i.id_usuario = c.id_usuario AND i.id_caja = 1) AS monto_sistema,
        (SELECT SUM(e.monto * e.cambio) FROM egresos e WHERE  e.fecha >=c.fecha_apertura AND  e.fecha <=c.fecha_cierre AND e.anulado IS NULL AND e.id_usuario = c.id_usuario AND e.id_caja = 1 ) AS monto_egreso,
        (c.monto_apertura + (COALESCE(c.apertura_rs, 0) * COALESCE(c.cot_real, 0)) + (COALESCE(c.apertura_usd, 0) * COALESCE(c.cot_dolar, 0))) AS apertura_total_convertido,
        (c.monto_cierre + (COALESCE(c.monto_cierre_rs, 0) * COALESCE(c.cot_real, 0)) + (COALESCE(c.monto_cierre_usd, 0) * COALESCE(c.cot_dolar, 0))) AS cierre_total_convertido
        FROM cierres c 
        LEFT JOIN usuario u ON c.id_usuario = u.id 
        WHERE c.fecha_cierre IS NOT NULL $rango 
        ORDER BY c.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Listar30Dias($desde, $hasta)
	{
		try {
			$result = array();
			if ($desde == 0) {
				$hasta = date('Y-m-d H:i:s');
				$desde = date('Y-m-d 00:00:00', strtotime('-29 days')); // Incluye hoy y 29 días atrás
			}
			$rango = "AND c.fecha_cierre >= '$desde' AND c.fecha_cierre <= '$hasta'";
			$stm = $this->pdo->prepare("SELECT *, c.id as id, 
            (SELECT SUM(i.monto * i.cambio) FROM ingresos i WHERE i.fecha>=c.fecha_apertura AND i.fecha<=c.fecha_cierre AND i.anulado IS NULL AND i.id_usuario = c.id_usuario AND i.id_caja = 1) AS monto_sistema,
            (SELECT SUM(e.monto * e.cambio) FROM egresos e WHERE  e.fecha >=c.fecha_apertura AND  e.fecha <=c.fecha_cierre AND e.anulado IS NULL AND e.id_usuario = c.id_usuario AND e.id_caja = 1 ) AS monto_egreso,
            (c.monto_apertura + (COALESCE(c.apertura_rs, 0) * COALESCE(c.cot_real, 0)) + (COALESCE(c.apertura_usd, 0) * COALESCE(c.cot_dolar, 0))) AS apertura_total_convertido,
            (c.monto_cierre + (COALESCE(c.monto_cierre_rs, 0) * COALESCE(c.cot_real, 0)) + (COALESCE(c.monto_cierre_usd, 0) * COALESCE(c.cot_dolar, 0))) AS cierre_total_convertido
            FROM cierres c 
            LEFT JOIN usuario u ON c.id_usuario = u.id 
            WHERE c.fecha_cierre IS NOT NULL $rango 
            ORDER BY c.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarActivas()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, c.id as id FROM cierres c LEFT JOIN usuario u ON c.id_usuario = u.id WHERE fecha_cierre IS NULL ORDER BY c.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMovimientosSesion($id_usuario)
	{
		try {
			$result = array();
			/*$query = "
				SELECT i.fecha, (SELECT c.caja FROM cajas c WHERE c.id = i.id_caja) AS caja, id_caja, i.categoria, i.concepto, i.comprobante, (i.monto * 1) as monto, i.forma_pago, i.anulado, (SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento FROM ingresos i WHERE i.fecha >= (SELECT c.fecha_apertura FROM cierres c WHERE c.id_usuario = ? AND c.fecha_cierre IS NULL) AND i.id_usuario = ?
				UNION ALL 
				SELECT fecha, (SELECT c.caja FROM cajas c WHERE c.id = egresos.id_caja) AS caja, id_caja, categoria, concepto, comprobante, (monto * -1) as monto, forma_pago, anulado, (SELECT v.descuento FROM ventas v WHERE v.id_venta = id_compra LIMIT 1) as descuento FROM egresos WHERE fecha >= (SELECT fecha_apertura FROM cierres WHERE id_usuario = ? AND fecha_cierre IS NULL) AND id_usuario = ? ORDER BY fecha";*/
			// $query = "SELECT i.fecha, i.id_venta AS id_venta, (SELECT c.caja FROM cajas c WHERE c.id = i.id_caja) AS caja, id_caja, i.categoria, i.concepto, i.comprobante, (i.monto * 1) as monto, i.forma_pago, i.anulado, (SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento FROM ingresos i WHERE i.fecha >= (SELECT c.fecha_apertura FROM cierres c WHERE c.id_usuario = ? AND c.fecha_cierre IS NULL) AND i.id_usuario = ? ORDER BY i.id DESC";
			$query = "	SELECT 	
							i.fecha, 
							i.id_venta AS id_venta,
							(SELECT c.caja FROM cajas c WHERE c.id = i.id_caja) AS caja, 
							i.id_caja, 
							i.categoria, 
							i.concepto, 
							i.comprobante, 
							(i.monto * 1) as monto, 
							i.forma_pago, 
							i.anulado, 
							(SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento
						FROM ingresos i 
						WHERE i.fecha >= (SELECT c.fecha_apertura FROM cierres c WHERE c.id_usuario = ? AND c.fecha_cierre IS NULL) 
						AND i.id_usuario = ? AND i.anulado IS NULL
								
						UNION ALL   
				
						SELECT 
							e.fecha,
							e.id_compra as id_venta, 
							(SELECT c.caja FROM cajas c WHERE c.id = e.id_caja) AS caja, 
							e.id_caja,
							e.categoria, 
							e.concepto, 
							e.comprobante, 
							(e.monto * -1) as monto, 
							e.forma_pago, 
							e.anulado, 
							(SELECT c.descuento FROM compras c WHERE c.id_compra = e.id_compra LIMIT 1) as descuento 
							FROM egresos e WHERE fecha >= (SELECT c.fecha_apertura FROM cierres c WHERE c.id_usuario = ? AND c.fecha_cierre IS NULL) 
						AND e.id_usuario = ? AND e.anulado IS NULL ";


			$stm = $this->pdo->prepare($query);
			$stm->execute(array($id_usuario, $id_usuario, $id_usuario, $id_usuario));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarMovimientosSesionCerrada($id_usuario, $apertura, $cierre)
	{
		try {
			$result = array();
			$stm = $this->pdo->prepare("SELECT *, (monto * cambio) as monto_convertido FROM ingresos WHERE fecha >= ? AND fecha <= ? AND id_usuario = ? AND anulado IS NULL
        UNION ALL
        SELECT *, -(monto * cambio) as monto_convertido FROM egresos WHERE fecha >= ? AND fecha <= ? AND id_usuario = ? AND anulado IS NULL
        ORDER BY fecha ASC");
			$stm->execute(array($apertura, $cierre, $id_usuario, $apertura, $cierre, $id_usuario));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarMetodos($desde, $hasta, $id_usuario)
	{
		try {

			$query = "SELECT i.fecha, i.categoria, i.concepto, i.comprobante, (i.monto ) as monto, i.forma_pago, i.anulado,
				(SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento 
				FROM ingresos i 
				WHERE i.anulado IS NULL AND  i.fecha>= ? AND i.fecha<= ?  AND i.id_usuario = ? ORDER BY i.id DESC";
			$stm = $this->pdo->prepare($query);
			$stm->execute(array($desde, $hasta, $id_usuario));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarMovimientosDia($fecha)
	{
		try {

			$query = "SELECT i.fecha, i.categoria, i.concepto, i.comprobante, (i.monto * i.cambio) as monto, i.forma_pago, i.anulado, (SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento FROM ingresos i WHERE 
				i.categoria <> 'Transferencia' AND Cast(i.fecha as date) = ? ORDER BY i.id DESC";
			$stm = $this->pdo->prepare($query);
			$stm->execute(array($fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function Ultimo()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, c.id as id FROM cierres c LEFT JOIN usuario u ON c.id_usuario = u.id ORDER BY c.id DESC LIMIT 1");
			$stm->execute();

			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ConsultarCierre($id, $fecha)
	{
		try {

			$stm = $this->pdo
				->prepare("SELECT * FROM cierres WHERE id_usuario = ? AND fecha_cierre IS NULL AND CAST(fecha_apertura AS date) < ?");

			$stm->execute(array($id, $fecha));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Consultar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM cierres WHERE id_usuario = ? AND fecha_cierre IS NULL");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ConsultarUsuario($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM cierres WHERE id_usuario = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function ListarAcreedor($id_acreedor)
	{
		try {
			$result = array();
			$stm = $this->pdo->prepare("SELECT *, e.id as id FROM egresos e LEFT JOIN clientes c ON e.id_cliente = c.id WHERE e.id_acreedor = ? ORDER BY e.id DESC");
			$stm->execute(array($id_acreedor));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMes($fecha)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM egresos WHERE MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?) AND categoria <> 'compra' ");


			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Listar_rango($desde, $hasta)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, e.id as id FROM egresos e LEFT JOIN clientes c ON e.id_cliente = c.id WHERE cast(fecha as date) >= ? AND cast(fecha as date) <= ? ORDER BY e.id DESC");
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarSincompra($fecha)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM egresos WHERE categoria <> 'compra' AND Cast(fecha as date) = ? ORDER BY id DESC");
			$stm->execute(array($fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarSincompraMes($fecha)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM egresos WHERE categoria <> 'compra' AND MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?) ORDER BY id DESC");
			$stm->execute(array($fecha, $fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function EditarMonto($id_compra, $monto)
	{
		try {
			$sql = "UPDATE egresos SET 
						monto    = ?
				    WHERE id_compra = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$monto,
						$id_compra
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Agrupado_egreso($mes)
	{
		try {
			$result = array();
			if ($mes != '0') {
				$stm = $this->pdo->prepare("SELECT concepto, categoria, sum(monto) as monto, fecha  FROM egresos WHERE MONTH(fecha) = $mes GROUP BY categoria ORDER BY id DESC");
			} else {
				$stm = $this->pdo->prepare("SELECT concepto, categoria, sum(monto) as monto FROM egresos GROUP BY categoria ORDER BY id DESC");
			}
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Obtener($id)
	{
		try {
			$stm = $this->pdo->prepare("SELECT *, 
        (monto_apertura + (COALESCE(apertura_rs, 0) * COALESCE(cot_real, 0)) + (COALESCE(apertura_usd, 0) * COALESCE(cot_dolar, 0))) AS apertura_total_convertido,
        (monto_cierre + (COALESCE(monto_cierre_rs, 0) * COALESCE(cot_real, 0)) + (COALESCE(monto_cierre_usd, 0) * COALESCE(cot_dolar, 0))) AS cierre_total_convertido
        FROM cierres WHERE id = ?");
			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Eliminar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM egresos WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Anularcompra($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM egresos WHERE id_compra = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE egresos SET 
			            id_cliente     = ?,
			            id_compra     = ?,
			            fecha      	  = ?,
						categoria     = ?,
						concepto      = ?,
						comprobante      = ?, 
						monto         = ?, 
						forma_pago         = ?,
                        sucursal      = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_cliente,
						$data->id_compra,
						$data->fecha,
						$data->categoria,
						$data->concepto,
						$data->comprobante,
						$data->monto,
						$data->forma_pago,
						$data->sucursal,
						$data->id
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function Cierre($data)
	{
		try {
			$sql = "UPDATE cierres SET 

			            fecha_cierre     = ?,
						monto_cierre     = ?,
						monto_cierre_rs  = ?,
						monto_cierre_usd = ?
						
				    WHERE id_usuario = ? 
				    AND fecha_cierre IS NULL";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->fecha_cierre,
						$data->monto_cierre,
						$data->monto_cierre_rs,
						$data->monto_cierre_usd,
						$data->id_usuario
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Registrar($data)
	{
		try {
			$sql = "INSERT INTO cierres (fecha_apertura, fecha_cierre, id_usuario, id_caja, monto_apertura, monto_cierre, apertura_rs, apertura_usd, cot_dolar, cot_real) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->fecha_apertura,
						$data->fecha_cierre,
						$data->id_usuario,
						$data->id_caja,
						$data->monto_apertura,
						$data->monto_cierre,
						$data->apertura_rs,
						$data->apertura_usd,
						$data->cot_dolar,
						$data->cot_real
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarCierreUsuario($fecha, $id_usuario)
	{
		try {
			$result = array();


			$stm = $this->pdo->prepare("SELECT *, c.id as id 
			FROM cierres c 
			WHERE CAST(fecha_apertura AS date) = ?  AND id_usuario=?
			ORDER BY c.id DESC");
			$stm->execute(array($fecha, $id_usuario));

			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMetodosCierre($fecha_apertura, $fecha_cierre, $id_usuario = null)
	{
		try {
			$where = "fecha >= ? AND fecha <= ? AND categoria <> 'transferencia'";
			$params = [$fecha_apertura, $fecha_cierre];
			if (!empty($id_usuario)) {
				$where .= " AND id_usuario = ?";
				$params[] = $id_usuario;
			}
			$stm = $this->pdo->prepare("SELECT * FROM ingresos WHERE $where");
			$stm->execute($params);
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMovimientosCajaPrincipal($id_cierre)
	{
		try {
			// Primero obtenemos los datos del cierre
			$cierre_query = "SELECT id, monto_apertura, monto_cierre, fecha_apertura, fecha_cierre, id_usuario FROM cierres WHERE id = ?";
			$stm = $this->pdo->prepare($cierre_query);
			$stm->execute([$id_cierre]);
			$cierre_data = $stm->fetch(PDO::FETCH_OBJ);

			if (!$cierre_data) {
				return [];
			}

			$movimientos = [];

			// Consulta para ingresos de efectivo en caja 1
			$query_ingresos = "
            SELECT 
                i.fecha, 
                i.concepto, 
                i.monto AS ingreso,
				i.moneda,
                0 as egreso,
                'Caja Principal' AS nombre_caja
            FROM ingresos i
            WHERE 
                i.anulado IS NULL 
                AND i.forma_pago = 'efectivo'
                AND i.id_caja = 1
                AND i.fecha >= ?
                AND i.fecha <= ?
                AND i.id_usuario = ?
            ORDER BY i.fecha ASC";

			$stm = $this->pdo->prepare($query_ingresos);
			$stm->execute([
				$cierre_data->fecha_apertura,
				$cierre_data->fecha_cierre,
				$cierre_data->id_usuario
			]);

			$ingresos = $stm->fetchAll(PDO::FETCH_OBJ);

			// Consulta para egresos de efectivo en caja 1
			$query_egresos = "
            SELECT 
                e.fecha, 
                e.concepto, 
                0 as ingreso, 
                e.monto AS egreso,
				e.moneda,
                'Caja Principal' AS nombre_caja
            FROM egresos e
            WHERE 
                e.anulado IS NULL 
                AND e.forma_pago = 'efectivo'
                AND e.id_caja = 1
                AND e.fecha >= ?
                AND e.fecha <= ?
                AND e.id_usuario = ?
            ORDER BY e.fecha ASC";

			$stm = $this->pdo->prepare($query_egresos);
			$stm->execute([
				$cierre_data->fecha_apertura,
				$cierre_data->fecha_cierre,
				$cierre_data->id_usuario
			]);

			$egresos = $stm->fetchAll(PDO::FETCH_OBJ);

			// Combinar y ordenar resultados
			$todos_movimientos = array_merge($ingresos, $egresos);

			// Agregar datos del cierre a cada movimiento
			foreach ($todos_movimientos as $mov) {
				$mov->monto_apertura = $cierre_data->monto_apertura;
				$mov->monto_cierre = $cierre_data->monto_cierre;
				$mov->fecha_apertura = $cierre_data->fecha_apertura;
			}

			// Ordenar por fecha
			usort($todos_movimientos, function ($a, $b) {
				return strtotime($a->fecha) - strtotime($b->fecha);
			});

			return $todos_movimientos;
		} catch (Exception $e) {
			error_log("Error en ListarMovimientosCajaPrincipal: " . $e->getMessage());
			return [];
		}
	}

	public function ListarMovimientosMetodo($id_cierre, $metodo)
	{
		try {
			// Primero obtenemos los datos del cierre
			$cierre_query = "SELECT id, monto_apertura, monto_cierre, apertura_rs, apertura_usd, fecha_apertura, fecha_cierre, id_usuario, cot_real, cot_dolar FROM cierres WHERE id = ?";
			$stm = $this->pdo->prepare($cierre_query);
			$stm->execute([$id_cierre]);
			$cierre_data = $stm->fetch(PDO::FETCH_OBJ);

			if (!$cierre_data) {
				return [];
			}

			$movimientos = [];

			// Si es efectivo, necesitamos convertir todos los montos a guaraníes
			if (strtolower($metodo) === 'efectivo') {
				// Consulta para ingresos de efectivo con conversión
				$query_ingresos = "
					SELECT 
						i.fecha, 
						i.id_venta,
						i.concepto, 
						CASE 
							WHEN i.moneda = 'RS' OR i.moneda = 'Reales' OR i.moneda = 'R$' THEN i.monto * i.cambio
							WHEN i.moneda = 'USD' OR i.moneda = 'Dolares' OR i.moneda = 'US$' THEN i.monto * i.cambio
							ELSE i.monto
						END AS ingreso,
						i.moneda,
						i.cambio,
						0 as egreso,
						c.caja AS nombre_caja
					FROM ingresos i
					LEFT JOIN cajas c ON i.id_caja = c.id
					WHERE 
						i.anulado IS NULL 
						AND i.forma_pago = ?
						AND i.fecha >= ?
						AND i.fecha <= ?
						AND i.id_usuario = ?
					ORDER BY i.fecha ASC";

				$stm = $this->pdo->prepare($query_ingresos);
				$stm->execute([
					$metodo,
					$cierre_data->fecha_apertura,
					$cierre_data->fecha_cierre,
					$cierre_data->id_usuario
				]);

				$ingresos = $stm->fetchAll(PDO::FETCH_OBJ);

				// Consulta para egresos de efectivo con conversión
				$query_egresos = "
					SELECT 
						e.fecha, 
						e.id_compra,
						e.concepto, 
						0 as ingreso,
						CASE 
							WHEN e.moneda = 'RS' OR e.moneda = 'Reales' OR e.moneda = 'R$' THEN e.monto * e.cambio
							WHEN e.moneda = 'USD' OR e.moneda = 'Dolares' OR e.moneda = 'US$' THEN e.monto * e.cambio
							ELSE e.monto
						END AS egreso,
						e.moneda,
						e.cambio,
						c.caja AS nombre_caja
					FROM egresos e
					LEFT JOIN cajas c ON e.id_caja = c.id
					WHERE 
						e.anulado IS NULL 
						AND e.forma_pago = ?
						AND e.fecha >= ?
						AND e.fecha <= ?
						AND e.id_usuario = ?
					ORDER BY e.fecha ASC";

				$stm = $this->pdo->prepare($query_egresos);
				$stm->execute([
					$metodo,
					$cierre_data->fecha_apertura,
					$cierre_data->fecha_cierre,
					$cierre_data->id_usuario
				]);

				$egresos = $stm->fetchAll(PDO::FETCH_OBJ);

				// Calcular el saldo inicial total convertido a guaraníes
				$apertura_gs = $cierre_data->monto_apertura ?? 0;
				$apertura_rs_convertida = ($cierre_data->apertura_rs ?? 0) * ($cierre_data->cot_real ?? 1);
				$apertura_usd_convertida = ($cierre_data->apertura_usd ?? 0) * ($cierre_data->cot_dolar ?? 1);

				$apertura_total = $apertura_gs + $apertura_rs_convertida + $apertura_usd_convertida;

				// Calcular el cierre total convertido a guaraníes
				$cierre_gs = $cierre_data->monto_cierre ?? 0;
				$cierre_rs_convertida = ($cierre_data->monto_cierre_rs ?? 0) * ($cierre_data->cot_real ?? 1);
				$cierre_usd_convertida = ($cierre_data->monto_cierre_usd ?? 0) * ($cierre_data->cot_dolar ?? 1);

				$cierre_total = $cierre_gs + $cierre_rs_convertida + $cierre_usd_convertida;
			} else {
				// Para otros métodos de pago, usar la consulta original
				$query_ingresos = "
					SELECT 
						i.fecha, 
						i.id_venta,
						i.concepto, 
						i.monto AS ingreso, 
						0 as egreso,
						c.caja AS nombre_caja
					FROM ingresos i
					LEFT JOIN cajas c ON i.id_caja = c.id
					WHERE 
						i.anulado IS NULL 
						AND i.forma_pago = ?
						AND i.fecha >= ?
						AND i.fecha <= ?
						AND i.id_usuario = ?
					ORDER BY i.fecha ASC";

				$stm = $this->pdo->prepare($query_ingresos);
				$stm->execute([
					$metodo,
					$cierre_data->fecha_apertura,
					$cierre_data->fecha_cierre,
					$cierre_data->id_usuario
				]);

				$ingresos = $stm->fetchAll(PDO::FETCH_OBJ);

				$query_egresos = "
					SELECT 
						e.fecha, 
						e.id_compra,
						e.concepto, 
						0 as ingreso, 
						e.monto AS egreso,
						c.caja AS nombre_caja
					FROM egresos e
					LEFT JOIN cajas c ON e.id_caja = c.id
					WHERE 
						e.anulado IS NULL 
						AND e.forma_pago = ?
						AND e.fecha >= ?
						AND e.fecha <= ?
						AND e.id_usuario = ?
					ORDER BY e.fecha ASC";

				$stm = $this->pdo->prepare($query_egresos);
				$stm->execute([
					$metodo,
					$cierre_data->fecha_apertura,
					$cierre_data->fecha_cierre,
					$cierre_data->id_usuario
				]);

				$egresos = $stm->fetchAll(PDO::FETCH_OBJ);

				// Para otros métodos, usar solo el monto de apertura en guaraníes
				$apertura_total = $cierre_data->monto_apertura ?? 0;
				$cierre_total = $cierre_data->monto_cierre ?? 0;
			}

			// Combinar y ordenar resultados
			$todos_movimientos = array_merge($ingresos, $egresos);

			// Agregar datos del cierre a cada movimiento
			foreach ($todos_movimientos as $mov) {
				if (strtolower($metodo) === 'efectivo') {
					$mov->monto_apertura = $apertura_total;
					$mov->monto_cierre = $cierre_total;
				} else {
					$mov->monto_apertura = $cierre_data->monto_apertura;
					$mov->monto_cierre = $cierre_data->monto_cierre;
				}
				$mov->fecha_apertura = $cierre_data->fecha_apertura;
			}

			// Ordenar por fecha
			usort($todos_movimientos, function ($a, $b) {
				return strtotime($a->fecha) - strtotime($b->fecha);
			});

			return $todos_movimientos;
		} catch (Exception $e) {
			error_log("Error en ListarMovimientosMetodo: " . $e->getMessage());
			return [];
		}
	}

	public function ListarMovimientosCajaPrincipalPorMoneda($id_cierre, $moneda)
	{
		try {
			// Normalizar el nombre de la moneda
			$moneda_normalizada = $this->normalizarMoneda($moneda);

			// Primero obtenemos los datos del cierre
			$cierre_query = "SELECT id, monto_apertura, monto_cierre, apertura_rs, apertura_usd, monto_cierre_rs, monto_cierre_usd, fecha_apertura, fecha_cierre, id_usuario FROM cierres WHERE id = ?";
			$stm = $this->pdo->prepare($cierre_query);
			$stm->execute([$id_cierre]);
			$cierre_data = $stm->fetch(PDO::FETCH_OBJ);

			if (!$cierre_data) {
				return [];
			}

			$movimientos = [];			// Consulta para ingresos de efectivo en caja 1 por moneda específica
			$query_ingresos = "
            SELECT 
                i.fecha, 
                i.id_venta,
                i.concepto, 
                i.monto AS ingreso,
				i.moneda,
                0 as egreso,
                'Caja Principal' AS nombre_caja
            FROM ingresos i
            WHERE 
                i.anulado IS NULL 
                AND i.forma_pago = 'efectivo'
                AND i.id_caja = 1
                AND (
                    i.moneda = ? 
                    OR i.moneda = ? 
                    OR i.moneda = ?
                    OR (i.moneda IS NULL AND ? = 'GS')
                    OR (i.moneda = '' AND ? = 'GS')
                )
                AND i.fecha >= ?
                AND i.fecha <= ?
                AND i.id_usuario = ?
            ORDER BY i.fecha ASC";

			$stm = $this->pdo->prepare($query_ingresos);
			$stm->execute([
				$moneda,
				$moneda_normalizada,
				$this->getAlternativeMonedaName($moneda),
				$moneda,
				$moneda,
				$cierre_data->fecha_apertura,
				$cierre_data->fecha_cierre,
				$cierre_data->id_usuario
			]);

			$ingresos = $stm->fetchAll(PDO::FETCH_OBJ);

			// Consulta para egresos de efectivo en caja 1 por moneda específica
			$query_egresos = "
            SELECT 
                e.fecha, 
                e.id_compra,
                e.concepto, 
                0 as ingreso, 
                e.monto AS egreso,
				e.moneda,
                'Caja Principal' AS nombre_caja
            FROM egresos e
            WHERE 
                e.anulado IS NULL 
                AND e.forma_pago = 'efectivo'
                AND e.id_caja = 1
                AND (
                    e.moneda = ? 
                    OR e.moneda = ? 
                    OR e.moneda = ?
                    OR (e.moneda IS NULL AND ? = 'GS')
                    OR (e.moneda = '' AND ? = 'GS')
                )
                AND e.fecha >= ?
                AND e.fecha <= ?
                AND e.id_usuario = ?
            ORDER BY e.fecha ASC";

			$stm = $this->pdo->prepare($query_egresos);
			$stm->execute([
				$moneda,
				$moneda_normalizada,
				$this->getAlternativeMonedaName($moneda),
				$moneda,
				$moneda,
				$cierre_data->fecha_apertura,
				$cierre_data->fecha_cierre,
				$cierre_data->id_usuario
			]);

			$egresos = $stm->fetchAll(PDO::FETCH_OBJ);

			// Combinar y ordenar resultados
			$todos_movimientos = array_merge($ingresos, $egresos);

			// Agregar datos del cierre a cada movimiento según la moneda
			foreach ($todos_movimientos as $mov) {
				switch ($moneda) {
					case 'GS':
					case 'Guaranies':
						$mov->monto_apertura = $cierre_data->monto_apertura;
						$mov->monto_cierre = $cierre_data->monto_cierre;
						break;
					case 'RS':
					case 'Reales':
						$mov->monto_apertura = $cierre_data->apertura_rs ?? 0;
						$mov->monto_cierre = $cierre_data->monto_cierre_rs ?? 0;
						break;
					case 'USD':
					case 'Dolares':
						$mov->monto_apertura = $cierre_data->apertura_usd ?? 0;
						$mov->monto_cierre = $cierre_data->monto_cierre_usd ?? 0;
						break;
					default:
						$mov->monto_apertura = 0;
						$mov->monto_cierre = 0;
				}
				$mov->fecha_apertura = $cierre_data->fecha_apertura;
				$mov->moneda = $moneda;
			}

			// Ordenar por fecha
			usort($todos_movimientos, function ($a, $b) {
				return strtotime($a->fecha) - strtotime($b->fecha);
			});

			return $todos_movimientos;
		} catch (Exception $e) {
			error_log("Error en ListarMovimientosCajaPrincipalPorMoneda: " . $e->getMessage());
			return [];
		}
	}

	private function normalizarMoneda($moneda)
	{
		switch (strtoupper($moneda)) {
			case 'GS':
				return 'Guaranies';
			case 'RS':
				return 'Reales';
			case 'USD':
				return 'Dolares';
			case 'GUARANIES':
				return 'Guaranies';
			case 'REALES':
				return 'Reales';
			case 'DOLARES':
				return 'Dolares';
			default:
				return $moneda;
		}
	}

	private function getAlternativeMonedaName($moneda)
	{
		switch (strtoupper($moneda)) {
			case 'GS':
				return 'Gs';
			case 'RS':
				return 'R$';
			case 'USD':
				return 'US$';
			case 'GUARANIES':
				return 'GS';
			case 'REALES':
				return 'RS';
			case 'DOLARES':
				return 'USD';
			default:
				return $moneda;
		}
	}

	public function ActualizarMontos($data)
	{
		try {
			$sql = "UPDATE cierres SET 
			            monto_apertura = ?,
			            monto_cierre = ?,
						apertura_rs = ?,
						monto_cierre_rs = ?,
						apertura_usd = ?,
						monto_cierre_usd = ?
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->monto_apertura,
						$data->monto_cierre,
						$data->apertura_rs,
						$data->monto_cierre_rs,
						$data->apertura_usd,
						$data->monto_cierre_usd,
						$data->id
					)
				);
			return true;
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
