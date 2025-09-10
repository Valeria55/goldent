<?php
class pago_tmp
{
	private $pdo;

	public $id;
	public $id_usuario;
	public $pago;
	public $monto;
	public $moneda;
	public $cambio;
	public $id_deuda;

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
			if (!isset($_SESSION)) session_start();
			$id_usuario = $_SESSION["user_id"];

			$stm = $this->pdo->prepare("SELECT * FROM pagos_tmp WHERE id_usuario = ? ORDER BY id DESC");
			$stm->execute(array($id_usuario));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarPagos()
	{
		try {
			$result = array();
			if (!isset($_SESSION)) session_start();
			$id_usuario = $_SESSION["user_id"];

			$stm = $this->pdo->prepare("SELECT p.*,
			CASE 
			  WHEN p.pago REGEXP '^[0-9]+$' THEN d.concepto
			  ELSE p.pago
			END AS pago_info
		  	FROM pagos_tmp p
		  	LEFT JOIN deudas d ON d.id = p.pago
		  	WHERE p.id_usuario = ? 
		  	ORDER BY p.id DESC");
			$stm->execute(array($id_usuario));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Obtener()
	{
		try {
			if (!isset($_SESSION)) session_start();
			$id_usuario = $_SESSION["user_id"];

			$stm = $this->pdo->prepare("SELECT SUM(monto) as monto FROM pagos_tmp WHERE id_usuario = ?");
			$stm->execute(array($id_usuario));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerTodo()
	{
		try {
			if (!isset($_SESSION)) session_start();
			$id_usuario = $_SESSION["user_id"];

			$stm = $this->pdo->prepare("SELECT * FROM pagos_tmp WHERE id_usuario = ?");
			$stm->execute(array($id_usuario));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Eliminar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM pagos_tmp WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Vaciar()
	{
		try {
			if (!isset($_SESSION)) session_start();
			$id_usuario = $_SESSION["user_id"];
			$stm = $this->pdo->prepare("DELETE FROM pagos_tmp WHERE id_usuario = ?");

			$stm->execute(array($id_usuario));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE pago_tmpes SET 
						id_producto        = ?,
						pago_tmp      		= ?,
						id_deuda      		= ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_producto,
						$data->pago_tmp,
						$data->id
					)
				);
			return "Modificado";
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Registrar(pago_tmp $data)
	{
		try {
			// Asegurar que id_usuario esté definido
			if (!isset($data->id_usuario) || $data->id_usuario == null) {
				if (!isset($_SESSION)) session_start();
				$data->id_usuario = $_SESSION['user_id'];
			}
			
			$sql = "INSERT INTO pagos_tmp (id_usuario, pago, monto, moneda, cambio, id_deuda) 
                VALUES (?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_usuario,
						$data->pago,
						$data->monto,
						$data->moneda,
						$data->cambio,
						$data->id_deuda
					)
				);
			return "Agregado";
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function CargarEgresosDeCompra($id_compra)
	{
		try {
			// Primero vaciar pagos temporales del usuario actual
			$this->Vaciar();
			
			// Obtener egresos de la compra
			$egreso = new egreso();
			$egresos = $egreso->ObtenerPorCompra($id_compra);
			
			// Asegurar que la sesión esté iniciada
			if (!isset($_SESSION)) session_start();
			$id_usuario = $_SESSION['user_id'];
			
			// Convertir cada egreso a pago temporal
			foreach ($egresos as $egr) {
				// Validar que el egreso tenga datos válidos
				if (!$egr->forma_pago || !$egr->monto) {
					continue; // Saltar egresos sin datos válidos
				}
				
				$pago_tmp = new pago_tmp();
				$pago_tmp->id_usuario = $id_usuario;
				$pago_tmp->pago = $egr->forma_pago;
				$pago_tmp->monto = $egr->monto;
				$pago_tmp->moneda = $egr->moneda ? $egr->moneda : 'GS';
				$pago_tmp->cambio = $egr->cambio ? $egr->cambio : 1;
				$pago_tmp->id_deuda = 0; // Los egresos normales no tienen deuda asociada
				
				$this->Registrar($pago_tmp);
			}
			
			return true;
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	
	public function ObtenerConvertido()
	{
		try {
			if (!isset($_SESSION)) session_start();
			$id_usuario = $_SESSION["user_id"];

			// Obtener las cotizaciones actuales
			require_once 'model/cierre.php';
			$cierre = new cierre();
			$cambio = $cierre->Ultimo();

			$stm = $this->pdo->prepare("SELECT *, 
            CASE 
                WHEN moneda = 'USD' THEN monto * ?
                WHEN moneda = 'RS' THEN monto * ?
                ELSE monto 
            END as monto_gs
            FROM pagos_tmp WHERE id_usuario = ?");

			$stm->execute(array($cambio->cot_dolar, $cambio->cot_real, $id_usuario));
			$pagos = $stm->fetchAll(PDO::FETCH_OBJ);

			$total_gs = 0;
			foreach ($pagos as $pago) {
				$total_gs += $pago->monto_gs;
			}

			return (object)['monto' => $total_gs];
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
