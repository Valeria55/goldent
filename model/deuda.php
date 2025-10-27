<?php
class deuda
{
	private $pdo;
	private $pdo_tienda;

	public $id;
	public $id_cliente;
	public $id_venta;
	public $fecha;
	public $vencimiento;
	public $concepto;
	public $monto;
	public $saldo;
	public $sucursal;
	public $devolucion;

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
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, d.id as id, c.id as id_cliente, v.nro_comprobante 
				FROM deudas d 
				LEFT JOIN clientes c ON d.id_cliente = c.id 
				LEFT JOIN ventas v ON d.id_venta = v.id 
				WHERE d.saldo > 0 
				ORDER BY d.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarSaldados()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, d.id as id, c.id as id_cliente FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE saldo = 0 ORDER BY d.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarAgrupadoCliente()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, d.id as id, c.id as id_cliente, SUM(monto) as monto, SUM(saldo) AS saldo FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE saldo > 0 GROUP BY d.id_cliente ORDER BY d.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function listar_cliente_deuda($id)
	{
		try {
			$stm = $this->pdo->prepare("SELECT *, d.id AS id_deuda  
			FROM deudas d
			LEFT JOIN clientes c ON c.id = d.id_cliente
			WHERE id_cliente = ? AND saldo > 0 AND devolucion = 1");
			$stm->execute(array($id));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDia($fecha)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE Cast(fecha as date) = ?");


			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMes($fecha)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?)");


			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Obtener($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT *, d.id FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE d.id = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Ultimo()
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT MAX(id) as id FROM deudas");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function listar_cliente($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM deudas WHERE id_cliente = ? AND saldo > 0");


			$stm->execute(array($id));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Eliminar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM deudas WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function AnularVenta($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM deudas WHERE id_venta = ?");

			$stm->execute(array($id));
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

			$sql = "SELECT i.concepto, i.fecha, SUM(i.monto) as monto, SUM(i.saldo) as saldo, c.nombre AS nombre 
			FROM deudas i
			LEFT JOIN clientes c ON c.id=i.id_cliente
			WHERE saldo > 0 $rango $anho 
			GROUP BY i.id_cliente ORDER BY i.id DESC";

			$stm = $this->pdo->prepare($sql);
			$stm->execute();
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function SumarSaldo($data)
	{
		try {
			$sql = "UPDATE deudas SET saldo = saldo + ? WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array($data->monto, $data->id)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE deudas SET 
						id_cliente    = ?,
						id_venta      = ?,
						fecha      	  = ?,
						vencimiento	  = ?,
						concepto      = ?, 
						monto         = ?,
						saldo         = ?,
						sucursal      = ?,
						devolucion      = ?
                        
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_cliente,
						$data->id_venta,
						$data->fecha,
						$data->vencimiento,
						$data->concepto,
						$data->monto,
						$data->saldo,
						$data->sucursal,
						$data->devolucion,
						$data->id
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function EditarMonto($id_venta, $monto)
	{
		try {
			$sql = "UPDATE deudas SET 
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

	public function Restar($data)
	{
		try {
			$sql = "UPDATE deudas SET 
					
					saldo = saldo - ?
                        
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->monto,
						$data->id
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Guardar($data)
	{
		try {
			$sql = "INSERT INTO deudas (id_cliente, id_venta, fecha, vencimiento, concepto, monto, saldo, sucursal, devolucion) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_cliente,
						$data->id_venta,
						$data->fecha,
						$data->vencimiento,
						$data->concepto,
						$data->monto,
						$data->saldo,
						$data->sucursal,
						$data->devolucion
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Registrar($data)
	{
		try {
			$sql = "INSERT INTO deudas (id_cliente, id_venta, fecha, vencimiento, concepto, monto, saldo, sucursal, devolucion) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_cliente,
						$data->id_venta,
						$data->fecha,
						$data->vencimiento,
						$data->concepto,
						$data->monto,
						$data->saldo,
						$data->sucursal,
						$data->devolucion
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function RegistrarTaller($data)
	{
		try {
			// Asegúrate de que los datos de entrada sean válidos
			// (Aquí podrías añadir validaciones específicas para cada campo)
			// ...	
			$this->pdo_tienda = Database::StartUp_taller();

			$sql = "INSERT INTO deudas (id_cliente, id_venta, fecha, vencimiento, concepto, monto, saldo, sucursal, devolucion) 
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$stmt = $this->pdo_tienda->prepare($sql);

			// Ejecución de la consulta preparada con manejo de errores
			if ($stmt->execute([
				$data->id_cliente,
				$data->id_venta,
				$data->fecha,
				$data->vencimiento,
				$data->concepto,
				$data->monto,
				$data->saldo,
				$data->sucursal,
				$data->devolucion
			])) {
				// Opcional: Devuelve algún indicador de éxito o el ID de la fila insertada
				return ['success' => true, 'id' => $this->pdo_tienda->lastInsertId()];
			} else {
				// Manejar el caso en que la ejecución falla
				return ['success' => false, 'error' => 'Error al insertar datos.'];
			}
		} catch (PDOException $e) {
			// Manejo de errores específicos de la base de datos
			return ['success' => false, 'error' => $e->getMessage()];
		} finally {
			// Asegurar que la conexión se cierre en cualquier caso
			$this->pdo_tienda = null;
		}
	}

	/**
	 * Obtiene resumen de cuentas por cobrar para un período específico
	 */
	public function ResumenCuentasPorCobrar($desde, $hasta)
	{
		try {
			// SALDO ANTERIOR: TODO el monto de las deudas menos ingresos de cobro de deudas anteriores
			$stm = $this->pdo->prepare("
				SELECT COALESCE(SUM(monto), 0) as total_montos_deudas
				FROM deudas
			");
			$stm->execute();
			$total_montos_deudas = $stm->fetch(PDO::FETCH_OBJ)->total_montos_deudas;

			// Obtener cobros de deudas anteriores a la fecha especificada (ingresos con cobro de deudas)
			$stm = $this->pdo->prepare("
				SELECT COALESCE(SUM(monto * COALESCE(cambio, 1)), 0) as cobros_deudas_anteriores
				FROM ingresos 
				WHERE fecha < ? 
				AND (categoria = 'Cobro de deuda' OR categoria LIKE '%cobro%' OR categoria LIKE '%deuda%')
				AND anulado IS NULL
			");
			$stm->execute(array($desde));
			$cobros_deudas_anteriores = $stm->fetch(PDO::FETCH_OBJ)->cobros_deudas_anteriores;

			$saldo_anterior = $total_montos_deudas - $cobros_deudas_anteriores;

			// VENTAS A CRÉDITO: ventas con estado 'APROBADO' no anuladas desde la fecha especificada
			$stm = $this->pdo->prepare("
				SELECT COALESCE(SUM(v.total), 0) as ventas_credito
				FROM ventas v
				WHERE v.fecha_venta >= ? AND v.fecha_venta <= ?
				AND v.contado = 'Credito'
				AND v.estado = 'APROBADO'
				AND v.anulado = 0
			");
			$stm->execute(array($desde, $hasta));
			$ventas_credito = $stm->fetch(PDO::FETCH_OBJ)->ventas_credito;

			// COBROS RECIBIDOS: cobros hechos a esas ventas a crédito del período
			$stm = $this->pdo->prepare("
				SELECT COALESCE(SUM(i.monto * COALESCE(i.cambio, 1)), 0) as cobros_recibidos
				FROM ingresos i
				INNER JOIN ventas v ON i.id_venta = v.id_venta
				WHERE v.fecha_venta >= ? AND v.fecha_venta <= ?
				AND v.contado = 'Credito'
				AND v.estado = 'APROBADO'
				AND v.anulado IS NULL
				AND i.anulado IS NULL
				AND i.categoria != 'Transferencia'
			");
			$stm->execute(array($desde, $hasta));
			$cobros_recibidos = $stm->fetch(PDO::FETCH_OBJ)->cobros_recibidos;

			// SALDO FINAL: saldo anterior + ventas a crédito - cobros recibidos
			$saldo_final = $saldo_anterior + $ventas_credito - $cobros_recibidos;

			return (object) [
				'saldo_anterior' => $saldo_anterior,
				'ventas_credito' => $ventas_credito,
				'cobros_recibidos' => $cobros_recibidos,
				'saldo_final' => $saldo_final
			];
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function listarClientesConDeudas()
	{
		try {
			$stm = $this->pdo->prepare("
				SELECT 
					c.id as id_cliente,
					c.nombre,
					c.ruc,
					SUM(d.saldo) as total_deuda,
					COUNT(d.id) as cantidad_deudas
				FROM deudas d 
				INNER JOIN clientes c ON d.id_cliente = c.id 
				WHERE d.saldo > 0 
				GROUP BY c.id, c.nombre, c.ruc 
				ORDER BY total_deuda DESC
			");
			$stm->execute();
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function listarDeudasPorCliente($id_cliente)
	{
		try {
			$stm = $this->pdo->prepare("
				SELECT d.*, v.nro_comprobante 
				FROM deudas d
				LEFT JOIN ventas v ON d.id_venta = v.id 
				WHERE d.id_cliente = ? AND d.saldo > 0 
				ORDER BY d.fecha ASC
			");
			$stm->execute(array($id_cliente));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function listarTodasDeudasPorCliente($id_cliente)
	{
		try {
			$stm = $this->pdo->prepare("
				SELECT d.*, v.nro_comprobante 
				FROM deudas d
				LEFT JOIN ventas v ON d.id_venta = v.id 
				WHERE d.id_cliente = ? 
				ORDER BY d.fecha ASC
			");
			$stm->execute(array($id_cliente));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function listarDeudasPorClienteOrdenadas($id_cliente)
	{
		try {
			$stm = $this->pdo->prepare("
				SELECT * FROM deudas 
				WHERE id_cliente = ? AND saldo > 0 
				ORDER BY fecha ASC
			");
			$stm->execute(array($id_cliente));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function registrarPago($id_deuda, $cantidad, $metodo, $moneda = 'GS', $cambio = 1)
	{
		try {
			$this->pdo->beginTransaction();

			// Obtener la deuda actual
			$stm = $this->pdo->prepare("SELECT * FROM deudas WHERE id = ?");
			$stm->execute(array($id_deuda));
			$deuda = $stm->fetch(PDO::FETCH_OBJ);

			if (!$deuda) {
				throw new Exception("Deuda no encontrada");
			}

			$cantidad_guaranies = $cantidad * $cambio;

			if ($cantidad_guaranies > $deuda->saldo) {
				throw new Exception("La cantidad excede el saldo de la deuda");
			}

			// Actualizar el saldo de la deuda
			$nuevo_saldo = $deuda->saldo - $cantidad_guaranies;
			$stm = $this->pdo->prepare("UPDATE deudas SET saldo = ? WHERE id = ?");
			$stm->execute(array($nuevo_saldo, $id_deuda));

			// Determinar id_caja según el método de pago
			$id_caja = ($metodo == 'Efectivo') ? 1 : 3;
			
			// Registrar el ingreso del pago
			$stm = $this->pdo->prepare("
				INSERT INTO ingresos (concepto, monto, fecha, id_deuda, forma_pago, moneda, cambio, id_caja) 
				VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)
			");
			$concepto_ingreso = "Pago de deuda: " . $deuda->concepto;
			$stm->execute(array($concepto_ingreso, $cantidad, $id_deuda, $metodo, $moneda, $cambio, $id_caja));

			$this->pdo->commit();
			return true;

		} catch (Exception $e) {
			$this->pdo->rollBack();
			throw $e;
		}
	}

	public function registrarPagoMultiple($id_deuda, $metodos_pago, $tipos_cambio)
	{
		try {
			// Verificar que el campo nro_recibo existe
			$this->verificarCampoNroRecibo();
			
			$this->pdo->beginTransaction();

			// Generar ID único para este grupo de pagos
			$grupo_pago_id = 'PM_' . date('YmdHis') . '_' . uniqid();

			// Generar número de recibo incremental
			$nro_recibo = $this->obtenerSiguienteNumeroRecibo();

			// Obtener la deuda actual y el cliente
			$stm = $this->pdo->prepare("
				SELECT d.*, c.nombre as cliente_nombre 
				FROM deudas d 
				LEFT JOIN clientes c ON d.id_cliente = c.id 
				WHERE d.id = ?
			");
			$stm->execute(array($id_deuda));
			$deuda = $stm->fetch(PDO::FETCH_OBJ);

			if (!$deuda) {
				throw new Exception("Deuda no encontrada");
			}

			// Calcular el total en guaraníes
			$total_guaranies = 0;
			foreach ($metodos_pago as $metodo_pago) {
				$cantidad_guaranies = $metodo_pago['cantidad'] * $tipos_cambio[$metodo_pago['moneda']];
				$total_guaranies += $cantidad_guaranies;
			}

			if ($total_guaranies > $deuda->saldo) {
				throw new Exception("El total a pagar excede el saldo de la deuda");
			}

			// Actualizar el saldo de la deuda
			$nuevo_saldo = $deuda->saldo - $total_guaranies;
			$stm = $this->pdo->prepare("UPDATE deudas SET saldo = ? WHERE id = ?");
			$stm->execute(array($nuevo_saldo, $id_deuda));

			// Obtener id_usuario de la sesión
			if (!isset($_SESSION)) session_start();
			$id_usuario = $_SESSION['user_id'];

			// Registrar el detalle del pago en la tabla pagos_detalle
			$stm = $this->pdo->prepare("
				INSERT INTO pagos_detalle (grupo_pago_id, id_deuda, id_cliente, monto_aplicado, fecha, id_usuario, nro_recibo) 
				VALUES (?, ?, ?, ?, NOW(), ?, ?)
			");
			$stm->execute(array(
				$grupo_pago_id,
				$id_deuda,
				$deuda->id_cliente,
				$total_guaranies,
				$id_usuario,
				$nro_recibo
			));

			// Registrar cada método de pago como un ingreso separado
			foreach ($metodos_pago as $index => $metodo_pago) {
				$cantidad_guaranies = $metodo_pago['cantidad'] * $tipos_cambio[$metodo_pago['moneda']];
				
				// Obtener id_usuario de la sesión
				if (!isset($_SESSION)) session_start();
				$id_usuario = $_SESSION['user_id'];
				
				// Determinar id_caja según el método de pago
				$id_caja = ($metodo_pago['metodo'] == 'Efectivo') ? 1 : 3;
				
				// Registrar el ingreso con los campos exactos de la tabla
				$stm = $this->pdo->prepare("
					INSERT INTO ingresos (concepto, monto, fecha, id_deuda, id_cliente, id_usuario, categoria, forma_pago, moneda, cambio, pago_deuda, id_caja) 
					VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)
				");
				
				$concepto_ingreso = "Cobro de deuda a " . $deuda->cliente_nombre;
				
				$stm->execute(array(
					$concepto_ingreso,
					$metodo_pago['cantidad'],
					$id_deuda,
					$deuda->id_cliente,
					$id_usuario,
					'Cobro de deuda',
					$metodo_pago['metodo'],
					$metodo_pago['moneda'],
					$tipos_cambio[$metodo_pago['moneda']],
					$grupo_pago_id,
					$id_caja
				));
			}

			$this->pdo->commit();
			return array(
				'success' => true,
				'grupo_pago_id' => $grupo_pago_id,
				'nro_recibo' => $nro_recibo
			);

		} catch (Exception $e) {
			$this->pdo->rollBack();
			error_log("Error en registrarPagoMultiple: " . $e->getMessage());
			throw new Exception("Error al procesar el pago: " . $e->getMessage());
		}
	}

	public function registrarPagoMultipleDeuda($id_deuda, $metodos_pago, $tipos_cambio, $pago_deuda, $total_pago)
	{
		try {
			$this->pdo->beginTransaction();

			// Obtener o generar número de recibo
			$grupo_pago_id = $metodos_pago[0]['grupo_pago_id'];
			$nro_recibo = $this->obtenerNumeroReciboPorGrupo($grupo_pago_id);
			
			if (!$nro_recibo) {
				$nro_recibo = $this->obtenerSiguienteNumeroRecibo();
			}

			// Obtener la deuda actual y el cliente
			$stm = $this->pdo->prepare("
				SELECT d.*, c.nombre as cliente_nombre 
				FROM deudas d 
				LEFT JOIN clientes c ON d.id_cliente = c.id 
				WHERE d.id = ?
			");
			$stm->execute(array($id_deuda));
			$deuda = $stm->fetch(PDO::FETCH_OBJ);

			if (!$deuda) {
				throw new Exception("Deuda no encontrada");
			}

			if ($pago_deuda > $deuda->saldo) {
				throw new Exception("El pago excede el saldo de la deuda");
			}

			// Actualizar el saldo de la deuda
			$nuevo_saldo = $deuda->saldo - $pago_deuda;
			$stm = $this->pdo->prepare("UPDATE deudas SET saldo = ? WHERE id = ?");
			$stm->execute(array($nuevo_saldo, $id_deuda));

			// Obtener id_usuario de la sesión
			if (!isset($_SESSION)) session_start();
			$id_usuario = $_SESSION['user_id'];

			// Registrar el detalle del pago en la tabla pagos_detalle
			$stm = $this->pdo->prepare("
				INSERT INTO pagos_detalle (grupo_pago_id, id_deuda, id_cliente, monto_aplicado, fecha, id_usuario, nro_recibo) 
				VALUES (?, ?, ?, ?, NOW(), ?, ?)
			");
			$stm->execute(array(
				$metodos_pago[0]['grupo_pago_id'], // Usar el grupo_pago_id del primer método
				$id_deuda,
				$deuda->id_cliente,
				$pago_deuda,
				$id_usuario,
				$nro_recibo
			));

			// Verificar si ya se registraron los ingresos para este grupo de pago
			// Solo registrar los ingresos una vez por grupo, no por cada deuda
			$stm = $this->pdo->prepare("SELECT COUNT(*) as count FROM ingresos WHERE pago_deuda = ?");
			$stm->execute(array($grupo_pago_id));
			$existe_ingreso = $stm->fetch(PDO::FETCH_OBJ);

			// Solo registrar los métodos de pago si es la primera deuda del grupo
			if ($existe_ingreso->count == 0) {
				// Registrar cada método de pago completo (no proporcional)
				foreach ($metodos_pago as $index => $metodo_pago) {
					// Obtener id_usuario de la sesión
					if (!isset($_SESSION)) session_start();
					$id_usuario = $_SESSION['user_id'];
					
					// Determinar id_caja según el método de pago
					$id_caja = ($metodo_pago['metodo'] == 'Efectivo') ? 1 : 3;
					
					// Registrar el ingreso con el monto original del método de pago
					$stm = $this->pdo->prepare("
						INSERT INTO ingresos (concepto, monto, fecha, id_deuda, id_cliente, id_usuario, categoria, forma_pago, moneda, cambio, pago_deuda, id_caja) 
						VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)
					");
					
					$concepto_ingreso = "Cobro de deuda a " . $deuda->cliente_nombre . " - Pago múltiple";
					
					$stm->execute(array(
						$concepto_ingreso,
						$metodo_pago['cantidad'], // Usar la cantidad original, no proporcional
						$id_deuda,
						$deuda->id_cliente,
						$id_usuario,
						'Cobro de deuda',
						$metodo_pago['metodo'],
						$metodo_pago['moneda'],
						$tipos_cambio[$metodo_pago['moneda']],
						$metodo_pago['grupo_pago_id'],
						$id_caja
					));
				}
			}

			$this->pdo->commit();
			return true;

		} catch (Exception $e) {
			$this->pdo->rollBack();
			throw $e;
		}
	}

	public function revertirPagoMultiple($grupo_pago_id)
	{
		try {
			$this->pdo->beginTransaction();

			// Obtener todos los ingresos del grupo de pago
			$stm = $this->pdo->prepare("SELECT * FROM ingresos WHERE pago_deuda = ?");
			$stm->execute(array($grupo_pago_id));
			$ingresos = $stm->fetchAll(PDO::FETCH_OBJ);

			if (empty($ingresos)) {
				throw new Exception("No se encontraron pagos para revertir");
			}

			// Agrupar por deuda para revertir los saldos
			$deudas_afectadas = array();
			foreach ($ingresos as $ingreso) {
				if (!isset($deudas_afectadas[$ingreso->id_deuda])) {
					$deudas_afectadas[$ingreso->id_deuda] = 0;
				}
				// Calcular monto en guaraníes usando el tipo de cambio
				$monto_guaranies = $ingreso->monto * ($ingreso->cambio ?? 1);
				$deudas_afectadas[$ingreso->id_deuda] += $monto_guaranies;
			}

			// Revertir los saldos de las deudas
			foreach ($deudas_afectadas as $id_deuda => $monto_total) {
				$stm = $this->pdo->prepare("UPDATE deudas SET saldo = saldo + ? WHERE id = ?");
				$stm->execute(array($monto_total, $id_deuda));
			}

			// Marcar los ingresos como anulados en lugar de eliminarlos
			$stm = $this->pdo->prepare("UPDATE ingresos SET anulado = 1 WHERE pago_deuda = ?");
			$stm->execute(array($grupo_pago_id));

			// Nota: No eliminamos los registros de pagos_detalle para mantener el historial
			// pero no aparecerán en las consultas porque verificamos que los ingresos no estén anulados

			$this->pdo->commit();
			return array(
				'success' => true,
				'deudas_revertidas' => count($deudas_afectadas),
				'ingresos_anulados' => count($ingresos)
			);

		} catch (Exception $e) {
			$this->pdo->rollBack();
			throw $e;
		}
	}

	public function obtenerPagosMultiples($id_deuda = null)
	{
		try {
			$sql = "
				SELECT 
					i.pago_deuda as grupo_id,
					COUNT(*) as cantidad_metodos,
					SUM(i.monto * COALESCE(i.cambio, 1)) as total_monto,
					MIN(i.fecha) as fecha_pago,
					GROUP_CONCAT(DISTINCT i.id_deuda) as deudas_afectadas,
					GROUP_CONCAT(CONCAT(i.forma_pago, ' (', i.moneda, ')') SEPARATOR ' | ') as metodos,
					MIN(pd.nro_recibo) as nro_recibo
				FROM ingresos i
				LEFT JOIN pagos_detalle pd ON i.pago_deuda = pd.grupo_pago_id
				WHERE i.pago_deuda IS NOT NULL AND i.pago_deuda != '' AND COALESCE(i.anulado, 0) = 0
			";
			
			if ($id_deuda) {
				$sql .= " AND i.id_deuda = ?";
				$stm = $this->pdo->prepare($sql . " GROUP BY i.pago_deuda ORDER BY fecha_pago DESC");
				$stm->execute(array($id_deuda));
			} else {
				$stm = $this->pdo->prepare($sql . " GROUP BY i.pago_deuda ORDER BY fecha_pago DESC");
				$stm->execute();
			}
			
			return $stm->fetchAll(PDO::FETCH_OBJ);

		} catch (Exception $e) {
			throw $e;
		}
	}

	public function obtenerPagosMultiplesPorCliente($ids_deudas)
	{
		try {
			if (empty($ids_deudas)) {
				return [];
			}
			
			$placeholders = str_repeat('?,', count($ids_deudas) - 1) . '?';
			
			$sql = "
				SELECT 
					i.pago_deuda as grupo_id,
					COUNT(*) as cantidad_metodos,
					SUM(i.monto * COALESCE(i.cambio, 1)) as total_monto,
					MIN(i.fecha) as fecha_pago,
					GROUP_CONCAT(DISTINCT i.id_deuda) as deudas_afectadas,
					GROUP_CONCAT(CONCAT(i.forma_pago, ' (', i.moneda, ')') SEPARATOR ' | ') as metodos,
					MIN(pd.nro_recibo) as nro_recibo
				FROM ingresos i
				LEFT JOIN pagos_detalle pd ON i.pago_deuda = pd.grupo_pago_id
				WHERE i.pago_deuda IS NOT NULL 
				AND i.pago_deuda != ''
				AND COALESCE(i.anulado, 0) = 0
				AND i.id_deuda IN ($placeholders)
				GROUP BY i.pago_deuda 
				ORDER BY fecha_pago DESC
			";
			
			$stm = $this->pdo->prepare($sql);
			$stm->execute($ids_deudas);
			
			return $stm->fetchAll(PDO::FETCH_OBJ);

		} catch (Exception $e) {
			throw $e;
		}
	}

	public function obtenerTiposCambioActuales($id_usuario)
	{
		try {
			$stm = $this->pdo->prepare("
				SELECT cot_dolar, cot_real 
				FROM cierres 
				WHERE id_usuario = ? 
				ORDER BY fecha_apertura DESC, id DESC 
				LIMIT 1
			");
			$stm->execute(array($id_usuario));
			$cierre = $stm->fetch(PDO::FETCH_OBJ);
			
			// Valores por defecto si no hay cierre
			if (!$cierre) {
				return [
					'USD' => 7500,
					'RS' => 1400,
					'GS' => 1
				];
			}
			
			return [
				'USD' => $cierre->cot_dolar ? floatval($cierre->cot_dolar) : 7500,
				'RS' => $cierre->cot_real ? floatval($cierre->cot_real) : 1400,
				'GS' => 1
			];
			
		} catch (Exception $e) {
			// Valores por defecto en caso de error
			return [
				'USD' => 7500,
				'RS' => 1400,
				'GS' => 1
			];
		}
	}

	public function obtenerDetalleRecibo($grupo_pago_id, $permitir_anulado = false)
	{
		try {
			error_log("DEBUG: obtenerDetalleRecibo - grupo_pago_id: $grupo_pago_id, permitir_anulado: " . ($permitir_anulado ? 'SI' : 'NO'));
			
			// Solo verificar si no está anulado cuando no se permite explícitamente ver anulados
			if (!$permitir_anulado) {
				$stm = $this->pdo->prepare("
					SELECT COUNT(*) as count_activos 
					FROM ingresos 
					WHERE pago_deuda = ? AND COALESCE(anulado, 0) = 0
				");
				$stm->execute(array($grupo_pago_id));
				$activos = $stm->fetch(PDO::FETCH_OBJ);
				
				if ($activos->count_activos == 0) {
					throw new Exception("Este recibo ha sido anulado");
				}
			}

			// Obtener información del recibo
			$stm = $this->pdo->prepare("
				SELECT 
					pd.*,
					d.concepto as deuda_concepto,
					d.monto as deuda_monto_original,
					d.fecha as deuda_fecha,
					c.nombre as cliente_nombre,
					c.ruc as cliente_documento,
					u.user as usuario_nombre,
					v.nro_comprobante
				FROM pagos_detalle pd
				LEFT JOIN deudas d ON pd.id_deuda = d.id
				LEFT JOIN clientes c ON pd.id_cliente = c.id
				LEFT JOIN usuario u ON pd.id_usuario = u.id
				LEFT JOIN ventas v ON d.id_venta = v.id
				WHERE pd.grupo_pago_id = ?
				ORDER BY d.fecha ASC
			");
			$stm->execute(array($grupo_pago_id));
			$detalle_deudas = $stm->fetchAll(PDO::FETCH_OBJ);
			
			error_log("DEBUG: detalle_deudas encontradas: " . count($detalle_deudas));

			// Obtener métodos de pago utilizados
			if ($permitir_anulado) {
				$sql_metodos = "
					SELECT 
						forma_pago,
						moneda,
						SUM(monto) as total_metodo,
						cambio
					FROM ingresos 
					WHERE pago_deuda = ?
					GROUP BY forma_pago, moneda, cambio
				";
			} else {
				$sql_metodos = "
					SELECT 
						forma_pago,
						moneda,
						SUM(monto) as total_metodo,
						cambio
					FROM ingresos 
					WHERE pago_deuda = ? AND COALESCE(anulado, 0) = 0
					GROUP BY forma_pago, moneda, cambio
				";
			}
			
			$stm = $this->pdo->prepare($sql_metodos);
			$stm->execute(array($grupo_pago_id));
			$metodos_pago = $stm->fetchAll(PDO::FETCH_OBJ);
			
			error_log("DEBUG: métodos de pago encontrados: " . count($metodos_pago));

			return array(
				'detalle_deudas' => $detalle_deudas,
				'metodos_pago' => $metodos_pago,
				'grupo_pago_id' => $grupo_pago_id
			);

		} catch (Exception $e) {
			throw $e;
		}
	}

	public function obtenerRecibosCliente($id_cliente)
	{
		try {
			// Debug: contar todos los registros en pagos_detalle
			$stm_debug = $this->pdo->prepare("SELECT COUNT(*) as total FROM pagos_detalle");
			$stm_debug->execute();
			$count_total = $stm_debug->fetch(PDO::FETCH_OBJ);
			error_log("DEBUG: Total registros en pagos_detalle: " . $count_total->total);
			
			// Debug: contar registros para este cliente
			$stm_debug2 = $this->pdo->prepare("SELECT COUNT(*) as total FROM pagos_detalle WHERE id_cliente = ?");
			$stm_debug2->execute(array($id_cliente));
			$count_cliente = $stm_debug2->fetch(PDO::FETCH_OBJ);
			error_log("DEBUG: Registros en pagos_detalle para cliente $id_cliente: " . $count_cliente->total);
			
			$stm = $this->pdo->prepare("
				SELECT 
					pd.grupo_pago_id,
					MIN(pd.fecha) as fecha_recibo,
					SUM(pd.monto_aplicado) as total_recibo,
					COUNT(*) as cantidad_deudas,
					u.user as usuario_nombre
				FROM pagos_detalle pd
				LEFT JOIN usuario u ON pd.id_usuario = u.id
				WHERE pd.id_cliente = ?
				AND pd.grupo_pago_id IN (
					SELECT DISTINCT pago_deuda 
					FROM ingresos 
					WHERE pago_deuda IS NOT NULL 
					AND COALESCE(anulado, 0) = 0
				)
				GROUP BY pd.grupo_pago_id, u.user
				ORDER BY MIN(pd.fecha) DESC
			");
			$stm->execute(array($id_cliente));
			$result = $stm->fetchAll(PDO::FETCH_OBJ);
			
			error_log("DEBUG: Recibos encontrados: " . count($result));
			return $result;

		} catch (Exception $e) {
			error_log("DEBUG: Error en obtenerRecibosCliente: " . $e->getMessage());
			throw $e;
		}
	}

	public function obtenerRecibosAnulados($id_cliente)
	{
		try {
			$stm = $this->pdo->prepare("
				SELECT 
					pd.grupo_pago_id,
					MIN(pd.fecha) as fecha_recibo,
					SUM(pd.monto_aplicado) as total_recibo,
					COUNT(*) as cantidad_deudas,
					u.user as usuario_nombre,
					MIN(pd.nro_recibo) as nro_recibo
				FROM pagos_detalle pd
				LEFT JOIN usuario u ON pd.id_usuario = u.id
				WHERE pd.id_cliente = ?
				AND pd.grupo_pago_id IN (
					SELECT DISTINCT pago_deuda 
					FROM ingresos 
					WHERE pago_deuda IS NOT NULL 
					AND COALESCE(anulado, 0) = 1
				)
				GROUP BY pd.grupo_pago_id, u.user
				ORDER BY MIN(pd.fecha) DESC
			");
			$stm->execute(array($id_cliente));
			$result = $stm->fetchAll(PDO::FETCH_OBJ);
			
			return $result;

		} catch (Exception $e) {
			error_log("DEBUG: Error en obtenerRecibosAnulados: " . $e->getMessage());
			throw $e;
		}
	}

	public function obtenerDeudasClienteOrdenadas($id_cliente)
	{
		try {
			$stm = $this->pdo->prepare("
				SELECT id, id_cliente, monto, saldo, fecha, concepto
				FROM deudas 
				WHERE id_cliente = ? 
				ORDER BY fecha DESC, id DESC
			");
			$stm->execute(array($id_cliente));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			throw $e;
		}
	}

	public function restaurarSaldoEspecifico($id_deuda, $monto)
	{
		try {
			$stm = $this->pdo->prepare("
				UPDATE deudas 
				SET saldo = saldo + ? 
				WHERE id = ?
			");
			$stm->execute(array($monto, $id_deuda));
			return true;
		} catch (Exception $e) {
			throw $e;
		}
	}

	// Función para obtener el siguiente número de recibo incremental
	public function obtenerSiguienteNumeroRecibo()
	{
		try {
			// Configuración: número inicial deseado
			$numero_inicial = 216;
			
			// Verificar si ya existen recibos
			$stm = $this->pdo->prepare("
				SELECT MAX(CAST(SUBSTRING_INDEX(nro_recibo, '-', -1) AS UNSIGNED)) as ultimo_numero,
				       COUNT(*) as total_recibos
				FROM pagos_detalle 
				WHERE nro_recibo IS NOT NULL 
				AND nro_recibo LIKE '001-002-%'
			");
			$stm->execute();
			$resultado = $stm->fetch(PDO::FETCH_OBJ);
			
			if ($resultado->total_recibos > 0 && $resultado->ultimo_numero) {
				// Si ya hay recibos, usar el siguiente al último número
				$nuevo_numero = $resultado->ultimo_numero + 1;
			} else {
				// Si no hay recibos, empezar desde el número inicial configurado
				$nuevo_numero = $numero_inicial;
			}
			
			// Formatear el número como 001-002-0000XXX
			return $this->formatearNumeroRecibo($nuevo_numero);
			
		} catch (Exception $e) {
			// Si hay cualquier error, usar número inicial + timestamp como fallback
			$numero_fallback = 216 + (time() % 1000);
			return $this->formatearNumeroRecibo($numero_fallback);
		}
	}

	// Función para formatear el número de recibo
	private function formatearNumeroRecibo($numero)
	{
		// Formato: 001-002-0000XXX (donde XXX es el número incremental)
		$parte1 = "001";
		$parte2 = "002";
		$parte3 = str_pad($numero, 7, "0", STR_PAD_LEFT);
		
		return $parte1 . "-" . $parte2 . "-" . $parte3;
	}

	// Función para obtener el número de recibo por grupo de pago
	public function obtenerNumeroReciboPorGrupo($grupo_pago_id)
	{
		try {
			$stm = $this->pdo->prepare("
				SELECT nro_recibo 
				FROM pagos_detalle 
				WHERE grupo_pago_id = ? 
				AND nro_recibo IS NOT NULL 
				LIMIT 1
			");
			$stm->execute(array($grupo_pago_id));
			$resultado = $stm->fetch(PDO::FETCH_OBJ);
			
			return $resultado ? $resultado->nro_recibo : null;
		} catch (Exception $e) {
			return null;
		}
	}

	// Función para verificar si el campo nro_recibo existe
	public function verificarCampoNroRecibo()
	{
		try {
			$stm = $this->pdo->prepare("DESCRIBE pagos_detalle");
			$stm->execute();
			$campos = $stm->fetchAll(PDO::FETCH_ASSOC);
			
			foreach ($campos as $campo) {
				if ($campo['Field'] === 'nro_recibo') {
					return true;
				}
			}
			
			// Si no existe, intentar agregarlo
			$this->pdo->exec("ALTER TABLE pagos_detalle ADD COLUMN nro_recibo VARCHAR(20) NULL");
			return true;
			
		} catch (Exception $e) {
			return false;
		}
	}

	// Función para configurar el número inicial de recibos
	public function configurarNumeroInicialRecibo($numero_inicial)
	{
		try {
			// Verificar si ya existen recibos
			$stm = $this->pdo->prepare("
				SELECT COUNT(*) as total_recibos 
				FROM pagos_detalle 
				WHERE nro_recibo IS NOT NULL
			");
			$stm->execute();
			$resultado = $stm->fetch(PDO::FETCH_OBJ);
			
			if ($resultado->total_recibos == 0) {
				// Si no hay recibos, podemos configurar el número inicial
				return array(
					'success' => true,
					'message' => "Se configurará el próximo recibo para iniciar en: 001-002-" . str_pad($numero_inicial, 7, "0", STR_PAD_LEFT)
				);
			} else {
				// Si ya hay recibos, mostrar el estado actual
				$stm = $this->pdo->prepare("
					SELECT MAX(CAST(SUBSTRING_INDEX(nro_recibo, '-', -1) AS UNSIGNED)) as ultimo_numero
					FROM pagos_detalle 
					WHERE nro_recibo IS NOT NULL 
					AND nro_recibo LIKE '001-002-%'
				");
				$stm->execute();
				$ultimo = $stm->fetch(PDO::FETCH_OBJ);
				
				return array(
					'success' => false,
					'message' => "Ya existen " . $resultado->total_recibos . " recibos. El último número usado es: " . ($ultimo->ultimo_numero ?? 'N/A'),
					'proximo_numero' => ($ultimo->ultimo_numero ?? 0) + 1
				);
			}
			
		} catch (Exception $e) {
			return array(
				'success' => false,
				'message' => "Error al verificar recibos: " . $e->getMessage()
			);
		}
	}

	// Función para obtener el estado actual de la numeración
	public function obtenerEstadoNumeracion()
	{
		try {
			$stm = $this->pdo->prepare("
				SELECT 
					COUNT(*) as total_recibos,
					MIN(CAST(SUBSTRING_INDEX(nro_recibo, '-', -1) AS UNSIGNED)) as primer_numero,
					MAX(CAST(SUBSTRING_INDEX(nro_recibo, '-', -1) AS UNSIGNED)) as ultimo_numero
				FROM pagos_detalle 
				WHERE nro_recibo IS NOT NULL 
				AND nro_recibo LIKE '001-002-%'
			");
			$stm->execute();
			$resultado = $stm->fetch(PDO::FETCH_OBJ);
			
			return array(
				'total_recibos' => $resultado->total_recibos ?? 0,
				'primer_numero' => $resultado->primer_numero ?? null,
				'ultimo_numero' => $resultado->ultimo_numero ?? null,
				'proximo_numero' => ($resultado->ultimo_numero ?? 215) + 1 // 215 + 1 = 216 por defecto
			);
			
		} catch (Exception $e) {
			return array(
				'error' => $e->getMessage()
			);
		}
	}
}
