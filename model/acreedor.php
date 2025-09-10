<?php
class acreedor
{
	private $pdo;

	public $id;
	public $id_cliente;
	public $id_compra;
	public $fecha;
	public $concepto;
	public $monto;
	public $saldo;
	public $sucursal;

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

			$stm = $this->pdo->prepare("SELECT *, a.id as id, c.id as id_cliente, 
			(SELECT co.nro_comprobante FROM compras co WHERE co.id_compra=a.id_compra LIMIT 1) AS nro_comprobante
			FROM acreedores a 
			LEFT JOIN clientes c ON a.id_cliente = c.id 
			WHERE saldo > 0 AND (a.anulado IS NULL OR a.anulado = 0) ORDER BY a.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function ListarDia($fecha)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM acreedores a LEFT JOIN clientes c ON a.id_cliente = c.id WHERE Cast(fecha as date) = ? AND (a.anulado IS NULL OR a.anulado = 0)");


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
				->prepare("SELECT * FROM acreedores a LEFT JOIN clientes c ON a.id_cliente = c.id WHERE MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?) AND (a.anulado IS NULL OR a.anulado = 0)");


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
				->prepare("SELECT *, a.id FROM acreedores a LEFT JOIN clientes c ON a.id_cliente = c.id WHERE a.id = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function listar_cliente($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM acreedores WHERE id_cliente = ? AND saldo > 0");


			$stm->execute(array($id));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function UltimoID()
	{
		try {
			$stm = $this->pdo->prepare("SELECT id FROM acreedores ORDER BY id DESC LIMIT 1");


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
				->prepare("DELETE FROM acreedores WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Anularcompra($id)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE acreedores SET anulado = 1 WHERE id_compra = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function SumarSaldo($data)
	{
		try {
			$sql = "UPDATE acreedores SET saldo = saldo + ? WHERE id = ?";

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
			$sql = "UPDATE acreedores SET 
						id_cliente    = ?,
						id_compra      = ?,
						fecha      	  = ?,
						concepto      = ?, 
						monto         = ?,
						saldo         = ?,
						sucursal      = ?
                        
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_cliente,
						$data->id_compra,
						$data->fecha,
						$data->concepto,
						$data->monto,
						$data->saldo,
						$data->sucursal,
						$data->id
					)
				);
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
				$anho = "AND YEAR(a.fecha) = $anho";
				$rango = "";
			} else {
				$anho = "";

				if ($desde != '') {
					if ($hasta != '') {
						$rango = " AND CAST(a.fecha as date) >= '$desde' AND CAST(a.fecha as date) <= '$hasta'";
					} else {
						$rango = " AND CAST(a.fecha as date) >= '$desde' AND CAST(a.fecha as date) <= '$fecha'";
					}
				} else {
					if ($hasta != '') {
						$rango = " AND CAST(a.fecha as date) >= '$mes' AND CAST(a.fecha as date) <= '$hasta'";
					} else {
						$rango = " AND CAST(a.fecha as date) >= '$mes' AND CAST(a.fecha as date) <= '$fecha'";
					}
				}
			}

			$sql = "SELECT a.concepto, a.fecha, SUM(a.monto) as monto, SUM(a.saldo) as saldo, c.nombre AS nombre
			FROM acreedores a
			LEFT JOIN clientes c ON a.id_cliente=c.id
			WHERE saldo > 0 $rango $anho 
			GROUP BY a.id_cliente 
			ORDER BY a.id DESC";

			$stm = $this->pdo->prepare($sql);
			$stm->execute();
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function EditarMonto($id_compra, $monto)
	{
		try {
			$sql = "UPDATE acreedores SET 
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

	public function Restar($data)
	{
		try {
			$sql = "UPDATE acreedores SET 
					
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

	public function Registrar($data)
	{
		try {
			$sql = "INSERT INTO acreedores (id_cliente, id_compra, fecha, concepto, monto, saldo, sucursal) 
		        VALUES (?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_cliente,
						$data->id_compra,
						$data->fecha,
						$data->concepto,
						$data->monto,
						$data->saldo,
						$data->sucursal

					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/**
	 * Obtiene resumen de cuentas por pagar para un período específico
	 */
	public function ResumenCuentasPorPagar($desde, $hasta)
	{
		try {
			// SALDO ANTERIOR: TODO el monto de los acreedores menos egresos con id_acreedor anteriores
			$stm = $this->pdo->prepare("
				SELECT COALESCE(SUM(monto), 0) as total_montos_acreedores
				FROM acreedores
			");
			$stm->execute();
			$total_montos_acreedores = $stm->fetch(PDO::FETCH_OBJ)->total_montos_acreedores;

			// Obtener pagos a acreedores anteriores a la fecha especificada (egresos con id_acreedor)
			$stm = $this->pdo->prepare("
				SELECT COALESCE(SUM(monto * COALESCE(cambio, 1)), 0) as pagos_acreedores_anteriores
				FROM egresos 
				WHERE fecha < ? 
				AND id_acreedor > 0
				AND anulado IS NULL
			");
			$stm->execute(array($desde));
			$pagos_acreedores_anteriores = $stm->fetch(PDO::FETCH_OBJ)->pagos_acreedores_anteriores;

			$saldo_anterior = $total_montos_acreedores - $pagos_acreedores_anteriores;

			// COMPRAS A CRÉDITO: compras del período especificado
			$stm = $this->pdo->prepare("
				SELECT COALESCE(SUM(c.total), 0) as compras_credito
				FROM compras c
				WHERE DATE(c.fecha_compra) >= ? AND DATE(c.fecha_compra) <= ?
				AND c.contado = 'Credito'
				AND c.anulado = 0
			");
			$stm->execute(array($desde, $hasta));
			$compras_credito = $stm->fetch(PDO::FETCH_OBJ)->compras_credito;

			// PAGOS REALIZADOS: pagos hechos a compras a crédito del período
			$stm = $this->pdo->prepare("
				SELECT COALESCE(SUM(e.monto * COALESCE(e.cambio, 1)), 0) as pagos_realizados
				FROM egresos e
				WHERE e.fecha >= ? AND e.fecha <= ?
				AND e.id_acreedor > 0
				AND e.anulado IS NULL
			");
			$stm->execute(array($desde, $hasta));
			$pagos_realizados = $stm->fetch(PDO::FETCH_OBJ)->pagos_realizados;

			// SALDO FINAL: saldo anterior + compras a crédito - pagos realizados
			$saldo_final = $saldo_anterior + $compras_credito - $pagos_realizados;

			return (object) [
				'saldo_anterior' => $saldo_anterior,
				'compras_credito' => $compras_credito,
				'pagos_realizados' => $pagos_realizados,
				'saldo_final' => $saldo_final
			];
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/**
	 * Método temporal para debuggear las compras a crédito
	 */
	public function DebugComprasCredito($desde, $hasta)
	{
		try {
			// 1. Ver todas las compras del período sin filtros
			$stm1 = $this->pdo->prepare("
				SELECT COUNT(*) as total_compras, SUM(c.total) as suma_total
				FROM compras c
				WHERE DATE(c.fecha_compra) >= ? AND DATE(c.fecha_compra) <= ?
			");
			$stm1->execute(array($desde, $hasta));
			$todas = $stm1->fetch(PDO::FETCH_OBJ);

			// 2. Ver compras con contado = 'Credito'
			$stm2 = $this->pdo->prepare("
				SELECT COUNT(*) as total_credito, SUM(c.total) as suma_credito
				FROM compras c
				WHERE DATE(c.fecha_compra) >= ? AND DATE(c.fecha_compra) <= ?
				AND c.contado = 'Credito'
			");
			$stm2->execute(array($desde, $hasta));
			$credito = $stm2->fetch(PDO::FETCH_OBJ);

			// 3. Ver compras con anulado = 0
			$stm3 = $this->pdo->prepare("
				SELECT COUNT(*) as total_no_anulado, SUM(c.total) as suma_no_anulado
				FROM compras c
				WHERE DATE(c.fecha_compra) >= ? AND DATE(c.fecha_compra) <= ?
				AND c.anulado = 0
			");
			$stm3->execute(array($desde, $hasta));
			$no_anulado = $stm3->fetch(PDO::FETCH_OBJ);

			// 4. Ver compras con ambos filtros
			$stm4 = $this->pdo->prepare("
				SELECT COUNT(*) as total_final, SUM(c.total) as suma_final
				FROM compras c
				WHERE DATE(c.fecha_compra) >= ? AND DATE(c.fecha_compra) <= ?
				AND c.contado = 'Credito'
				AND c.anulado = 0
			");
			$stm4->execute(array($desde, $hasta));
			$final = $stm4->fetch(PDO::FETCH_OBJ);

			// 5. Ver valores únicos de contado y anulado
			$stm5 = $this->pdo->prepare("
				SELECT DISTINCT c.contado, c.anulado, COUNT(*) as cantidad
				FROM compras c
				WHERE DATE(c.fecha_compra) >= ? AND DATE(c.fecha_compra) <= ?
				GROUP BY c.contado, c.anulado
			");
			$stm5->execute(array($desde, $hasta));
			$valores = $stm5->fetchAll(PDO::FETCH_OBJ);

			return (object) [
				'todas_compras' => $todas,
				'solo_credito' => $credito,
				'solo_no_anulado' => $no_anulado,
				'credito_y_no_anulado' => $final,
				'valores_distintos' => $valores
			];
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
