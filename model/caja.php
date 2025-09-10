<?php
class caja
{
	private $pdo;

	public $id;
	public $id_usuario;
	public $fecha;
	public $caja;
	public $monto;
	public $usd_monto;
	public $rs_monto;
	public $tipo;
	public $cotizacion_dolar;
	public $cotizacion_real;
	public $observaciones;
	public $anulado;

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
			$id_user = $_SESSION["user_id"];
			if (!isset($_SESSION)) session_start();
			if ($_SESSION["nivel"] == 1) {
				$stm = $this->pdo->prepare("SELECT *,
    (SELECT user FROM usuario u WHERE u.id = c.id_usuario) AS usuario,
    
    -- Subconsulta para obtener cotizaciones del último cierre
    (
        SELECT 
            (
                (COALESCE(c.monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' THEN i.monto ELSE 0 END), 0) 
                 FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date)>= c.fecha)) + 
                (COALESCE(c.usd_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) 
                 FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date)>= c.fecha)) * ci.cot_dolar + 
                (COALESCE(c.rs_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) 
                 FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date)>= c.fecha)) * ci.cot_real
            )
        FROM cierres ci 
        ORDER BY ci.id DESC 
        LIMIT 1
    ) AS ingresos,

    (
        SELECT 
            (
                (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' THEN e.monto ELSE 0 END), 0) 
                 FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha) + 
                (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) 
                 FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha) * ci.cot_dolar + 
                (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) 
                 FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha) * ci.cot_real
            )
        FROM cierres ci 
        ORDER BY ci.id DESC 
        LIMIT 1
    ) AS egresos,

    -- Detalles por moneda incluyendo monto inicial de caja
    (COALESCE(c.monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha) - (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' THEN e.monto ELSE 0 END), 0) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha as date) >= c.fecha)) AS saldo_gs,
    (COALESCE(c.usd_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha) - (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha)) AS saldo_usd,
    (COALESCE(c.rs_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha) - (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha)) AS saldo_rs,

    -- Detalles separados para cálculos
    (COALESCE(c.monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha)) AS ingresos_gs,
    (COALESCE(c.usd_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha)) AS ingresos_usd,
    (COALESCE(c.rs_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha)) AS ingresos_rs,

    (SELECT SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' THEN e.monto ELSE 0 END) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha) AS egresos_gs,
    (SELECT SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha) AS egresos_usd,
    (SELECT SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha) AS egresos_rs

FROM cajas c
ORDER BY c.id DESC;
");
				$stm->execute();
			} else if ($_SESSION["nivel"] == 4) {
				$stm = $this->pdo->prepare("SELECT *, 
    (SELECT user FROM usuario u WHERE u.id = c.id_usuario) AS usuario, 
    
    (
        SELECT 
            (
                (COALESCE(c.monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' 
                    THEN i.monto ELSE 0 END), 0) 
                    FROM ingresos i 
                    WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, i.id_usuario = ?))) + 

                (COALESCE(c.usd_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) 
                    FROM ingresos i 
                    WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, i.id_usuario = ?))) * ci.cot_dolar + 

                (COALESCE(c.rs_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) 
                    FROM ingresos i 
                    WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, i.id_usuario = ?))) * ci.cot_real 
            )
        FROM cierres ci 
        ORDER BY ci.id DESC 
        LIMIT 1
    ) AS ingresos,

    (
        SELECT 
            (
                (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' 
                    THEN e.monto ELSE 0 END), 0) 
                    FROM egresos e 
                    WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, e.id_usuario = ?)) + 

                (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) 
                    FROM egresos e 
                    WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, e.id_usuario = ?)) * ci.cot_dolar  + 

                (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) 
                    FROM egresos e 
                    WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, e.id_usuario = ?)) * ci.cot_real
            )
        FROM cierres ci 
        ORDER BY ci.id DESC 
        LIMIT 1
    ) AS egresos,

    -- Saldos totales incluyendo monto inicial de caja
    (COALESCE(c.monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' THEN i.monto ELSE 0 END), 0)
     FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, i.id_usuario = ?)) - 
     (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' THEN e.monto ELSE 0 END), 0) 
     FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, e.id_usuario = ?))) AS saldo_gs,

    (COALESCE(c.usd_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) 
     FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, i.id_usuario = ?)) - 
     (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) 
     FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, e.id_usuario = ?))) AS saldo_usd,

    (COALESCE(c.rs_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) 
     FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, i.id_usuario = ?)) - 
     (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) 
     FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, e.id_usuario = ?))) AS saldo_rs,

    -- Detalles separados para cálculos
    (COALESCE(c.monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' THEN i.monto ELSE 0 END), 0) 
     FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, i.id_usuario = ?))) AS ingresos_gs,

    (COALESCE(c.usd_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) 
     FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, i.id_usuario = ?))) AS ingresos_usd,

    (COALESCE(c.rs_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) 
     FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, i.id_usuario = ?))) AS ingresos_rs,

    (SELECT SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' THEN e.monto ELSE 0 END) 
     FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, e.id_usuario = ?)) AS egresos_gs,

    (SELECT SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END) 
     FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, e.id_usuario = ?)) AS egresos_usd,

    (SELECT SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END) 
     FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND IF(c.id = 3, 1, e.id_usuario = ?)) AS egresos_rs

FROM cajas c 
ORDER BY c.id DESC;
");
				$stm->execute(array($_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"]));
			} else {
				// mostrar solo los del usuario actual si no es de nivel 1
				$stm = $this->pdo->prepare("SELECT *, 
    (SELECT user FROM usuario u WHERE u.id = c.id_usuario) AS usuario, 

    (
        SELECT 
            (
                (COALESCE(c.monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' 
                    THEN i.monto ELSE 0 END), 0) 
                    FROM ingresos i 
                    WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND i.id_usuario = ?)) + 

                (COALESCE(c.usd_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) 
                    FROM ingresos i 
                    WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND i.id_usuario = ?)) * ci.cot_dolar  + 

                (COALESCE(c.rs_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) 
                    FROM ingresos i 
                    WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND i.id_usuario = ?)) * ci.cot_real
            )
        FROM cierres ci 
        ORDER BY ci.id DESC 
        LIMIT 1
    ) AS ingresos,

    (
        SELECT 
            (
                (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' 
                    THEN e.monto ELSE 0 END), 0) 
                    FROM egresos e 
                    WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND e.id_usuario = ?) + 

                (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) 
                    FROM egresos e 
                    WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND e.id_usuario = ?) * ci.cot_dolar + 

                (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) 
                    FROM egresos e 
                    WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND e.id_usuario = ?) * ci.cot_real 
            )
        FROM cierres ci 
        ORDER BY ci.id DESC 
        LIMIT 1
    ) AS egresos,

    (COALESCE(c.monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' 
        THEN i.monto ELSE 0 END), 0) 
        FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND i.id_usuario = ?)) AS ingresos_gs,

    (COALESCE(c.usd_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) 
        FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND i.id_usuario = ?)) AS ingresos_usd,

    (COALESCE(c.rs_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) 
        FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND i.id_usuario = ?)) AS ingresos_rs,

    (SELECT SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' 
        THEN e.monto ELSE 0 END) 
        FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND e.id_usuario = ?) AS egresos_gs,

    (SELECT SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END) 
        FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND e.id_usuario = ?) AS egresos_usd,

    (SELECT SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END) 
        FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND e.id_usuario = ?) AS egresos_rs,

    -- Saldos totales incluyendo monto inicial de caja  
    (COALESCE(c.monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' THEN i.monto ELSE 0 END), 0) 
     FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND i.id_usuario = ?) - 
     (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' THEN e.monto ELSE 0 END), 0) 
     FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND e.id_usuario = ?)) AS saldo_gs,

    (COALESCE(c.usd_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) 
     FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND i.id_usuario = ?) - 
     (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) 
     FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND e.id_usuario = ?)) AS saldo_usd,

    (COALESCE(c.rs_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) 
     FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND i.id_usuario = ?) - 
     (SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) 
     FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND e.id_usuario = ?)) AS saldo_rs

FROM cajas c 
ORDER BY c.id DESC;
");
				$stm->execute(array($_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"], $_SESSION["user_id"]));
				// $stm->execute();
			}


			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarTodo()
	{
		try {

			$stm = $this->pdo->prepare("
			SELECT *, 
				(SELECT user FROM usuario u WHERE u.id = c.id_usuario) as usuario, 
				(SELECT SUM(i.monto) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL GROUP BY i.id_caja) as ingresos,
				(SELECT SUM(e.monto) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL GROUP BY e.id_caja) as egresos  
			FROM cajas c ORDER BY c.id DESC");
			$stm->execute();


			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarUsuario($id_usuario)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, (SELECT user FROM usuario u WHERE u.id = c.id_usuario) as usuario FROM cajas c WHERE id_usuario = ? AND anulado <> 1 ORDER BY c.id DESC");
			$stm->execute(array($id_usuario));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

		public function ListarMovimientosCajaNew($id_caja, $desde = null, $hasta = null)
	{
		try {
			$rangodesdei = $desde ? "AND CAST(i.fecha AS date) >= '$desde'" : "";
			$rangohastai = $hasta ? " AND CAST(i.fecha AS date) <= '$hasta'" : "";

			$rangodesdee = $desde ? "AND CAST(e.fecha AS date) >= '$desde'" : "";
			$rangohastae = $hasta ? "AND CAST(e.fecha AS date) <= '$hasta'" : "";

			$result = array();
			$query = "
				SELECT 
					i.comprobante,
					i.fecha, 
					i.categoria, 
					i.concepto, 
					i.monto AS monto_moneda,
					i.moneda AS moneda,
					i.comprobante, 
					(CASE 
						WHEN i.moneda = 'USD' THEN i.monto * (SELECT cot_dolar FROM cierres ORDER BY id DESC LIMIT 1)
						WHEN i.moneda = 'RS' THEN i.monto * (SELECT cot_real FROM cierres ORDER BY id DESC LIMIT 1)
						ELSE i.monto
					END) as monto, 
					i.forma_pago, 
					i.anulado, 
					(SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento,
					(SELECT cl.nombre FROM clientes cl WHERE i.id_cliente = cl.id LIMIT 1) as nombre ,
					(SELECT u.user FROM usuario u WHERE i.id_usuario = u.id LIMIT 1) as usuario,
					(SELECT u.user FROM usuario u WHERE i.id_usuario = u.id LIMIT 1) as nombre_usuario
				FROM ingresos i 
				WHERE id_caja = ? 
					AND (i.fecha) >= (SELECT fecha FROM cajas WHERE id = i.id_caja)
					$rangodesdei $rangohastai
		
				UNION ALL 
				SELECT 
					e.comprobante,
					e.fecha, 
					e.categoria,
					e.concepto,
					e.monto AS monto_moneda, 
					e.moneda AS moneda,
					e.comprobante, 
					(CASE 
						WHEN e.moneda = 'USD' THEN (e.monto * (SELECT cot_dolar FROM cierres ORDER BY id DESC LIMIT 1)) * -1
						WHEN e.moneda = 'RS' THEN (e.monto * (SELECT cot_real FROM cierres ORDER BY id DESC LIMIT 1)) * -1
						ELSE e.monto * -1
					END) as monto, 
					e.forma_pago, 
					e.anulado, 
					(SELECT v.descuento FROM compras v WHERE v.id_compra = e.id_compra LIMIT 1) as descuento,
					(SELECT cl.nombre FROM clientes cl WHERE e.id_cliente = cl.id LIMIT 1) as nombre,
					(SELECT u.user FROM usuario u WHERE e.id_usuario = u.id LIMIT 1) as usuario,
					(SELECT u.user FROM usuario u WHERE e.id_usuario = u.id LIMIT 1) as nombre_usuario
				FROM egresos e 
				WHERE e.id_caja = ? 
					AND (e.fecha) >= (SELECT fecha FROM cajas WHERE id = e.id_caja)
					$rangodesdee $rangohastae
				    ORDER BY fecha";
			$stm = $this->pdo->prepare($query);
			$stm->execute(array($id_caja, $id_caja));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarMovimientosCaja($id_caja, $desde, $hasta)
	{
		try {
			$fecha = date('Y-m-d');
			$rango = '';

			if ($desde != 0) {
				$rango .= " AND CAST(fecha as date) >= '$desde'";
			}

			if ($hasta != 0) {
				$rango .= " AND CAST(fecha as date) <= '$hasta'";
			} else {
				$rango .= " AND CAST(fecha as date) <= '$fecha'";
			}

			$query = "
            SELECT 
                i.fecha, 
                i.id_usuario_transferencia, 
                i.id_usuario,
                (SELECT user FROM usuario us WHERE us.id = i.id_usuario) AS usuario, 
                i.categoria, 
                i.concepto, 
                i.comprobante, 
                (i.monto * 1) as monto, 
                i.forma_pago, 
                i.anulado, 
				i.cambio,
                (SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento 
            FROM ingresos i 
            WHERE id_caja = ? AND anulado IS NULL $rango
                
            UNION ALL 
                
            SELECT 
                e.fecha, 
                '' AS id_usuario_transferencia, 
                '' AS id_usuario, 
                (SELECT user FROM usuario us WHERE us.id = e.id_usuario) AS usuario, 
                e.categoria, 
                e.concepto, 
                e.comprobante, 
                (e.monto * -1) as monto, 
                e.forma_pago, 
                e.anulado, 
				e.cambio,
                (SELECT v.descuento FROM compras v WHERE v.id_compra = e.id_compra LIMIT 1) as descuento 
            FROM egresos e 
            WHERE e.id_caja = ? AND anulado IS NULL $rango
            ORDER BY fecha";

			$stm = $this->pdo->prepare($query);
			$stm->execute([$id_caja, $id_caja]);

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function ListarMovimientosCajaUsuario($id_caja, $desde, $hasta) // todos los movimientos ese usuario en la caja
	{

		try {
			$user_id = $_SESSION['user_id'];
			$result = array();
			$fecha = date('Y-m-d');
			if ($desde != 0) {
				if ($hasta != 0) {
					$rango = " AND CAST(fecha as date) >= '$desde' AND CAST(fecha as date) <= '$hasta'";
				} else {
					$rango = " AND CAST(fecha as date) >= '$desde' AND CAST(fecha as date) <= '$fecha'";
				}
			} else {
				if ($hasta != 0) {
					$rango = " ";
				} else {
					$rango = "";
				}
			}
			$query = "
				SELECT 
						i.fecha, 	
						i.id_usuario_transferencia, 
						i.id_usuario, 
						(SELECT user FROM usuario u WHERE u.id = i.id_usuario) as usuario, 
						i.categoria, 
						i.concepto, 
						i.comprobante, 
						(i.monto * 1) as monto, 
						i.forma_pago, 
						i.anulado, 
						i.cambio,
						(SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento 
					FROM ingresos i 
					WHERE 
						id_caja = ? 
						AND i.id_usuario = $user_id
						AND anulado IS NULL $rango
				
					UNION ALL 
				
				SELECT 	
						e.fecha, 
						'' AS id_usuario_transferencia, 
						e.id_usuario as id_usuario, 
						(SELECT user FROM usuario u WHERE u.id = e.id_usuario) as usuario,
						e.categoria, 
						e.concepto, 
						e.comprobante, 
						(e.monto * -1) as monto, 
						e.forma_pago, 
						e.anulado, 
						e.cambio,
						(SELECT v.descuento FROM compras v WHERE v.id_compra = e.id_compra LIMIT 1) as descuento 
				
				FROM egresos e 
				WHERE 
					e.id_caja = ?
					AND e.id_usuario = $user_id
					AND anulado IS NULL $rango
				ORDER BY fecha";
			$stm = $this->pdo->prepare($query);
			$stm->execute(array($id_caja, $id_caja));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarAgrupado()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM cajas GROUP BY caja ORDER BY id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerSaldoAnterior($id_caja, $fecha_desde)
	{
		try {
			if (!isset($_SESSION)) session_start();
			$user_id = $_SESSION['user_id'];

			if ($_SESSION["nivel"] == 1) { // admin ve todos los movimientos
				$query = "
					SELECT 
						(
							(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' 
								THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i 
								WHERE i.id_caja = ? AND i.anulado IS NULL 
								AND CAST(i.fecha as date) < ?) + 

							(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i 
								WHERE i.id_caja = ? AND i.anulado IS NULL 
								AND CAST(i.fecha as date) < ?) * ci.cot_dolar + 

							(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i 
								WHERE i.id_caja = ? AND i.anulado IS NULL 
								AND CAST(i.fecha as date) < ?) * ci.cot_real 
						) - 
						(
							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' 
								THEN e.monto ELSE 0 END), 0) 
								FROM egresos e 
								WHERE e.id_caja = ? AND e.anulado IS NULL 
								AND CAST(e.fecha as date) < ?) + 

							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) 
								FROM egresos e 
								WHERE e.id_caja = ? AND e.anulado IS NULL 
								AND CAST(e.fecha as date) < ?) * ci.cot_dolar + 

							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) 
								FROM egresos e 
								WHERE e.id_caja = ? AND e.anulado IS NULL 
								AND CAST(e.fecha as date) < ?) * ci.cot_real
						) AS saldo_anterior
					FROM cierres ci 
					ORDER BY ci.id DESC 
					LIMIT 1";
				$stm = $this->pdo->prepare($query);
				$stm->execute(array($id_caja, $fecha_desde, $id_caja, $fecha_desde, $id_caja, $fecha_desde, $id_caja, $fecha_desde, $id_caja, $fecha_desde, $id_caja, $fecha_desde));
			} else if ($_SESSION["nivel"] == 4) { // gerente
				$query = "
					SELECT 
						(
							(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' 
								THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i 
								WHERE i.id_caja = ? AND i.anulado IS NULL 
								AND CAST(i.fecha as date) < ?
								AND IF(? = 3, 1, i.id_usuario = ?)) + 

							(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i 
								WHERE i.id_caja = ? AND i.anulado IS NULL 
								AND CAST(i.fecha as date) < ?
								AND IF(? = 3, 1, i.id_usuario = ?)) * ci.cot_dolar + 

							(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i 
								WHERE i.id_caja = ? AND i.anulado IS NULL 
								AND CAST(i.fecha as date) < ?
								AND IF(? = 3, 1, i.id_usuario = ?)) * ci.cot_real 
						) - 
						(
							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' 
								THEN e.monto ELSE 0 END), 0) 
								FROM egresos e 
								WHERE e.id_caja = ? AND e.anulado IS NULL 
								AND CAST(e.fecha as date) < ?
								AND IF(? = 3, 1, e.id_usuario = ?)) + 

							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) 
								FROM egresos e 
								WHERE e.id_caja = ? AND e.anulado IS NULL 
								AND CAST(e.fecha as date) < ?
								AND IF(? = 3, 1, e.id_usuario = ?)) * ci.cot_dolar + 

							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) 
								FROM egresos e 
								WHERE e.id_caja = ? AND e.anulado IS NULL 
								AND CAST(e.fecha as date) < ?
								AND IF(? = 3, 1, e.id_usuario = ?)) * ci.cot_real
						) AS saldo_anterior
					FROM cierres ci 
					ORDER BY ci.id DESC 
					LIMIT 1";
				$stm = $this->pdo->prepare($query);
				$stm->execute(array($id_caja, $fecha_desde, $id_caja, $user_id, $id_caja, $fecha_desde, $id_caja, $user_id, $id_caja, $fecha_desde, $id_caja, $user_id, $id_caja, $fecha_desde, $id_caja, $user_id, $id_caja, $fecha_desde, $id_caja, $user_id, $id_caja, $fecha_desde, $id_caja, $user_id));
			} else { // otros usuarios solo ven sus movimientos
				$query = "
					SELECT 
						(
							(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' 
								THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i 
								WHERE i.id_caja = ? AND i.anulado IS NULL 
								AND CAST(i.fecha as date) < ?
								AND i.id_usuario = ?) + 

							(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i 
								WHERE i.id_caja = ? AND i.anulado IS NULL 
								AND CAST(i.fecha as date) < ?
								AND i.id_usuario = ?) * ci.cot_dolar + 

							(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i 
								WHERE i.id_caja = ? AND i.anulado IS NULL 
								AND CAST(i.fecha as date) < ?
								AND i.id_usuario = ?) * ci.cot_real 
						) - 
						(
							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' 
								THEN e.monto ELSE 0 END), 0) 
								FROM egresos e 
								WHERE e.id_caja = ? AND e.anulado IS NULL 
								AND CAST(e.fecha as date) < ?
								AND e.id_usuario = ?) + 

							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) 
								FROM egresos e 
								WHERE e.id_caja = ? AND e.anulado IS NULL 
								AND CAST(e.fecha as date) < ?
								AND e.id_usuario = ?) * ci.cot_dolar + 

							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) 
								FROM egresos e 
								WHERE e.id_caja = ? AND e.anulado IS NULL 
								AND CAST(e.fecha as date) < ?
								AND e.id_usuario = ?) * ci.cot_real
						) AS saldo_anterior
					FROM cierres ci 
					ORDER BY ci.id DESC 
					LIMIT 1";
				$stm = $this->pdo->prepare($query);
				$stm->execute(array($id_caja, $fecha_desde, $user_id, $id_caja, $fecha_desde, $user_id, $id_caja, $fecha_desde, $user_id, $id_caja, $fecha_desde, $user_id, $id_caja, $fecha_desde, $user_id, $id_caja, $fecha_desde, $user_id));
			}

			$resultado = $stm->fetch(PDO::FETCH_OBJ);
			return $resultado ? $resultado->saldo_anterior : 0;
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function DebugSaldo($id_caja, $fecha_desde = null)
	{
		try {
			if (!isset($_SESSION)) session_start();

			// Obtener cotizaciones actuales
			$query_cotizaciones = "SELECT cot_dolar, cot_real FROM cierres ORDER BY id DESC LIMIT 1";
			$stm_cot = $this->pdo->prepare($query_cotizaciones);
			$stm_cot->execute();
			$cotizaciones = $stm_cot->fetch(PDO::FETCH_OBJ);

			$cot_dolar = $cotizaciones->cot_dolar ?? 7500;
			$cot_real = $cotizaciones->cot_real ?? 1500;

			// Obtener totales históricos (sin filtro de fecha)
			$query_total = "
				SELECT 
					(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' 
						THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = ? AND i.anulado IS NULL) AS ingresos_gs_total,
					(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = ? AND i.anulado IS NULL) AS ingresos_usd_total,
					(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = ? AND i.anulado IS NULL) AS ingresos_rs_total,
					(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' 
						THEN e.monto ELSE 0 END), 0) FROM egresos e WHERE e.id_caja = ? AND e.anulado IS NULL) AS egresos_gs_total,
					(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) FROM egresos e WHERE e.id_caja = ? AND e.anulado IS NULL) AS egresos_usd_total,
					(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) FROM egresos e WHERE e.id_caja = ? AND e.anulado IS NULL) AS egresos_rs_total";

			if ($fecha_desde) {
				$query_anterior = $query_total . ",
					(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' 
						THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = ? AND i.anulado IS NULL AND CAST(i.fecha as date) < ?) AS ingresos_gs_anterior,
					(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = ? AND i.anulado IS NULL AND CAST(i.fecha as date) < ?) AS ingresos_usd_anterior,
					(SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = ? AND i.anulado IS NULL AND CAST(i.fecha as date) < ?) AS ingresos_rs_anterior,
					(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' 
						THEN e.monto ELSE 0 END), 0) FROM egresos e WHERE e.id_caja = ? AND e.anulado IS NULL AND CAST(e.fecha as date) < ?) AS egresos_gs_anterior,
					(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) FROM egresos e WHERE e.id_caja = ? AND e.anulado IS NULL AND CAST(e.fecha as date) < ?) AS egresos_usd_anterior,
					(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) FROM egresos e WHERE e.id_caja = ? AND e.anulado IS NULL AND CAST(e.fecha as date) < ?) AS egresos_rs_anterior";

				$stm = $this->pdo->prepare($query_anterior);
				$stm->execute(array($id_caja, $id_caja, $id_caja, $id_caja, $id_caja, $id_caja, $id_caja, $fecha_desde, $id_caja, $fecha_desde, $id_caja, $fecha_desde, $id_caja, $fecha_desde, $id_caja, $fecha_desde, $id_caja, $fecha_desde));
			} else {
				$stm = $this->pdo->prepare($query_total);
				$stm->execute(array($id_caja, $id_caja, $id_caja, $id_caja, $id_caja, $id_caja));
			}

			$resultado = $stm->fetch(PDO::FETCH_OBJ);

			// Calcular totales convertidos
			$total_historico = ($resultado->ingresos_gs_total + ($resultado->ingresos_usd_total * $cot_dolar) + ($resultado->ingresos_rs_total * $cot_real)) -
				($resultado->egresos_gs_total + ($resultado->egresos_usd_total * $cot_dolar) + ($resultado->egresos_rs_total * $cot_real));

			$debug_info = array(
				'cotizaciones' => array('dolar' => $cot_dolar, 'real' => $cot_real),
				'totales_historicos' => $resultado,
				'saldo_total_calculado' => $total_historico
			);

			if ($fecha_desde && isset($resultado->ingresos_gs_anterior)) {
				$saldo_anterior = ($resultado->ingresos_gs_anterior + ($resultado->ingresos_usd_anterior * $cot_dolar) + ($resultado->ingresos_rs_anterior * $cot_real)) -
					($resultado->egresos_gs_anterior + ($resultado->egresos_usd_anterior * $cot_dolar) + ($resultado->egresos_rs_anterior * $cot_real));
				$debug_info['saldo_anterior_calculado'] = $saldo_anterior;
			}

			return $debug_info;
		} catch (Exception $e) {
			return array('error' => $e->getMessage());
		}
	}

	public function Obtener($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT 
					ca.*,
					ca.fecha,
					(ca.monto + (ca.rs_monto * (SELECT cot_real FROM cierres ORDER BY id DESC LIMIT 1)) + (ca.usd_monto * (SELECT cot_dolar FROM cierres ORDER BY id DESC LIMIT 1))) as monto
				FROM cajas ca
				WHERE ca.id = ?");
			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerMontosOriginales($id)
	{
		try {
			$stm = $this->pdo->prepare("SELECT monto, usd_monto, rs_monto FROM cajas WHERE id = ?");
			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ObtenerBalance($id)
	{
		try {
			if (!isset($_SESSION)) session_start();
			if ($_SESSION["nivel"] == 1) { // usuario admin
				$stm = $this->pdo->prepare("
				SELECT 
					tab.*, 
					(tab.ingresos - tab.egresos) AS disponible
				FROM (SELECT *, 
							(SELECT user FROM usuario u WHERE u.id = c.id_usuario) as usuario, 

							-- Total ingresos incluyendo monto inicial de caja más movimientos desde fecha de caja
							(COALESCE(c.monto, 0) + COALESCE(c.usd_monto, 0) * (SELECT COALESCE(cot_dolar, 7500) FROM cierres ORDER BY id DESC LIMIT 1) + COALESCE(c.rs_monto, 0) * (SELECT COALESCE(cot_real, 1500) FROM cierres ORDER BY id DESC LIMIT 1) +
								(SELECT COALESCE(SUM(i.monto * COALESCE(i.cambio, 1)), 0)
								FROM ingresos i 
								WHERE i.id_caja = c.id 
									AND i.anulado IS NULL 
									AND CAST(i.fecha AS date) >= c.fecha)) as ingresos,
							
							(SELECT COALESCE(SUM(e.monto * COALESCE(e.cambio, 1)), 0)
								FROM egresos e 
								WHERE e.id_caja = c.id 
									AND e.anulado IS NULL 
									AND CAST(e.fecha AS date) >= c.fecha) as egresos,
							
							-- Desglose por moneda - Ingresos (incluyendo monto inicial)
							(COALESCE(c.monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha)) as ingresos_gs,
							(COALESCE(c.usd_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha)) as ingresos_usd,
							(COALESCE(c.rs_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha)) as ingresos_rs,
							
							-- Desglose por moneda - Egresos
							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' THEN e.monto ELSE 0 END), 0) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha) as egresos_gs,
							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha) as egresos_usd,
							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha) as egresos_rs
							  
						FROM cajas c 
						WHERE id = ?) tab");
				$stm->execute(array($id));
			} elseif ($_SESSION['nivel'] == 4) { // mostrar solo los del usuario actual si no es de nivel 1
				//muestra solo los movimientos propios excepto cuando es tesoreria, ahi no se tiene en cuenta id_usuario y se suma todo

				$user_id = $_SESSION['user_id'];
				$stm = $this->pdo->prepare("
				SELECT 
					tab.*, 
					(IF(tab.ingresos IS NULL, 0 , tab.ingresos ) - IF(tab.egresos IS NULL, 0 , tab.egresos )) AS disponible 
				FROM (SELECT *, 
							(SELECT user FROM usuario u WHERE u.id = c.id_usuario) as usuario, 

							-- Total ingresos incluyendo monto inicial de caja más movimientos desde fecha de caja
							(COALESCE(c.monto, 0) + COALESCE(c.usd_monto, 0) * (SELECT COALESCE(cot_dolar, 7500) FROM cierres ORDER BY id DESC LIMIT 1) + COALESCE(c.rs_monto, 0) * (SELECT COALESCE(cot_real, 1500) FROM cierres ORDER BY id DESC LIMIT 1) +
								(SELECT COALESCE(SUM(i.monto * COALESCE(i.cambio, 1)), 0)
								FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL 
									AND CAST(i.fecha AS date) >= c.fecha
									AND IF(i.id_caja <> 3, i.id_usuario = $user_id , 1 ))) as ingresos,
							(SELECT COALESCE(SUM(e.monto * COALESCE(e.cambio, 1)), 0)	
								FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL 
									AND CAST(e.fecha AS date) >= c.fecha
									AND IF(e.id_caja <> 3, e.id_usuario = $user_id , 1 )) as egresos,
							
							-- Desglose por moneda - Ingresos (incluyendo monto inicial)
							(COALESCE(c.monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND IF(i.id_caja <> 3, i.id_usuario = $user_id , 1 ))) as ingresos_gs,
							(COALESCE(c.usd_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND IF(i.id_caja <> 3, i.id_usuario = $user_id , 1 ))) as ingresos_usd,
							(COALESCE(c.rs_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND IF(i.id_caja <> 3, i.id_usuario = $user_id , 1 ))) as ingresos_rs,
							
							-- Desglose por moneda - Egresos
							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' THEN e.monto ELSE 0 END), 0) 
								FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND IF(e.id_caja <> 3, e.id_usuario = $user_id , 1 )) as egresos_gs,
							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) 
								FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND IF(e.id_caja <> 3, e.id_usuario = $user_id , 1 )) as egresos_usd,
							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) 
								FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND IF(e.id_caja <> 3, e.id_usuario = $user_id , 1 )) as egresos_rs

						FROM cajas c 
						WHERE id = ?) tab");
				$stm->execute(array($id));
			} else { // mostrar siempre solo los del usuario actual si no es nivel 1 ni 4 
				$user_id = $_SESSION['user_id'];
				$stm = $this->pdo->prepare("
				SELECT 
					tab.*, 
					(IF(tab.ingresos IS NULL, 0 , tab.ingresos ) - IF(tab.egresos IS NULL, 0 , tab.egresos )) AS disponible 
				FROM (SELECT *, 
							(SELECT user FROM usuario u WHERE u.id = c.id_usuario) as usuario, 

							-- Total ingresos incluyendo monto inicial de caja más movimientos desde fecha de caja
							(COALESCE(c.monto, 0) + COALESCE(c.usd_monto, 0) * (SELECT COALESCE(cot_dolar, 7500) FROM cierres ORDER BY id DESC LIMIT 1) + COALESCE(c.rs_monto, 0) * (SELECT COALESCE(cot_real, 1500) FROM cierres ORDER BY id DESC LIMIT 1) +
								(SELECT COALESCE(SUM(i.monto * COALESCE(i.cambio, 1)), 0)
								FROM ingresos i 
								WHERE i.id_caja = c.id 
									AND i.id_usuario = $user_id 
									AND i.anulado IS NULL 
									AND CAST(i.fecha AS date) >= c.fecha)) as ingresos,
							(SELECT COALESCE(SUM(e.monto * COALESCE(e.cambio, 1)), 0)
								FROM egresos e 
									WHERE e.id_caja = c.id 
										AND e.id_usuario = $user_id 
										AND e.anulado IS NULL 
										AND CAST(e.fecha AS date) >= c.fecha) as egresos,
							
							-- Desglose por moneda - Ingresos (incluyendo monto inicial)
							(COALESCE(c.monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'GS' OR i.moneda IS NULL OR i.moneda = '' THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND i.id_usuario = $user_id)) as ingresos_gs,
							(COALESCE(c.usd_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'USD' THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND i.id_usuario = $user_id)) as ingresos_usd,
							(COALESCE(c.rs_monto, 0) + (SELECT COALESCE(SUM(CASE WHEN i.moneda = 'RS' THEN i.monto ELSE 0 END), 0) 
								FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL AND CAST(i.fecha AS date) >= c.fecha AND i.id_usuario = $user_id)) as ingresos_rs,
							
							-- Desglose por moneda - Egresos
							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'GS' OR e.moneda IS NULL OR e.moneda = '' THEN e.monto ELSE 0 END), 0) 
								FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND e.id_usuario = $user_id) as egresos_gs,
							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'USD' THEN e.monto ELSE 0 END), 0) 
								FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND e.id_usuario = $user_id) as egresos_usd,
							(SELECT COALESCE(SUM(CASE WHEN e.moneda = 'RS' THEN e.monto ELSE 0 END), 0) 
								FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL AND CAST(e.fecha AS date) >= c.fecha AND e.id_usuario = $user_id) as egresos_rs
							  
						FROM cajas c 
						WHERE id = ?) tab");
				$stm->execute(array($id));
			}



			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Eliminar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM cajas WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Anular($id)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE cajas SET anulado = 1 WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE cajas SET 
						id_usuario	  = ?,
						fecha	      = ?,
						caja          = ?, 
						monto         = ?,
						comprobante   = ?,
						anulado       = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_usuario,
						$data->fecha,
						$data->caja,
						$data->monto,
						$data->comprobante,
						$data->anulado,
						$data->id
					)
				);
			return "Modificado";
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Registrar(caja $data)
	{
		try {
			$sql = "INSERT INTO cajas (id_usuario, fecha, caja, monto, comprobante, anulado) 
		        VALUES (?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_usuario,
						$data->fecha,
						$data->caja,
						$data->monto,
						$data->comprobante,
						$data->anulado
					)
				);
			return "Agregado";
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerCotizacionesActuales()
	{
		try {
			if (!isset($_SESSION)) session_start();

			// Obtener las cotizaciones del último cierre abierto del usuario actual
			$stm = $this->pdo->prepare("
				SELECT cot_dolar, cot_real 
				FROM cierres 
				WHERE id_usuario = ? AND fecha_cierre IS NULL 
				ORDER BY fecha_apertura DESC 
				LIMIT 1
			");
			$stm->execute(array($_SESSION['user_id']));
			$result = $stm->fetch(PDO::FETCH_OBJ);

			if ($result) {
				return $result;
			} else {
				// Si no hay cierre abierto, obtener las cotizaciones del último cierre cerrado
				$stm = $this->pdo->prepare("
					SELECT cot_dolar, cot_real 
					FROM cierres 
					WHERE fecha_cierre IS NOT NULL 
					ORDER BY fecha_cierre DESC 
					LIMIT 1
				");
				$stm->execute();
				$lastClosed = $stm->fetch(PDO::FETCH_OBJ);

				if ($lastClosed) {
					return $lastClosed;
				} else {
					// Si no hay ningún cierre, usar valores por defecto
					$default = new stdClass();
					$default->cot_dolar = 7500;
					$default->cot_real = 1500;
					return $default;
				}
			}
		} catch (Exception $e) {
			// En caso de error, intentar obtener el último cierre
			try {
				$stm = $this->pdo->prepare("
					SELECT cot_dolar, cot_real 
					FROM cierres 
					WHERE fecha_cierre IS NOT NULL 
					ORDER BY fecha_cierre DESC 
					LIMIT 1
				");
				$stm->execute();
				$lastClosed = $stm->fetch(PDO::FETCH_OBJ);

				if ($lastClosed) {
					return $lastClosed;
				}
			} catch (Exception $e2) {
				// Si todo falla, usar valores por defecto
			}

			$default = new stdClass();
			$default->cot_dolar = 7500;
			$default->cot_real = 1500;
			return $default;
		}
	}

	public function ListarMovimientosCronologicos($desde = '', $hasta = '', $id_caja = '', $moneda = '')
	{
		try {
			// Construir las condiciones WHERE para ingresos
			$whereConditionsIngresos = [];
			$whereConditionsEgresos = [];
			$paramsIngresos = [];
			$paramsEgresos = [];

			// Filtro por caja - Ingresos
			if (!empty($id_caja)) {
				$whereConditionsIngresos[] = "i.id_caja = ?";
				$paramsIngresos[] = $id_caja;
				$whereConditionsEgresos[] = "e.id_caja = ?";
				$paramsEgresos[] = $id_caja;
			}

			// Filtro por fechas - Ingresos
			if (!empty($desde) && !empty($hasta)) {
				$whereConditionsIngresos[] = "DATE(i.fecha) BETWEEN ? AND ?";
				$paramsIngresos[] = $desde;
				$paramsIngresos[] = $hasta;
				$whereConditionsEgresos[] = "DATE(e.fecha) BETWEEN ? AND ?";
				$paramsEgresos[] = $desde;
				$paramsEgresos[] = $hasta;
			} elseif (!empty($desde) && empty($hasta)) {
				$whereConditionsIngresos[] = "DATE(i.fecha) >= ?";
				$paramsIngresos[] = $desde;
				$whereConditionsEgresos[] = "DATE(e.fecha) >= ?";
				$paramsEgresos[] = $desde;
			} elseif (empty($desde) && !empty($hasta)) {
				$whereConditionsIngresos[] = "DATE(i.fecha) <= ?";
				$paramsIngresos[] = $hasta;
				$whereConditionsEgresos[] = "DATE(e.fecha) <= ?";
				$paramsEgresos[] = $hasta;
			}

			// Filtro por moneda
			if (!empty($moneda)) {
				$whereConditionsIngresos[] = "i.moneda = ?";
				$paramsIngresos[] = $moneda;
				$whereConditionsEgresos[] = "e.moneda = ?";
				$paramsEgresos[] = $moneda;
			}

			// Agregar filtro por categoría "Transferencia"
			if (!empty($whereConditionsIngresos)) {
				$whereConditionsIngresos[] = "i.categoria = 'Transferencia'";
			} else {
				$whereConditionsIngresos[] = "i.categoria = 'Transferencia'";
			}
			
			if (!empty($whereConditionsEgresos)) {
				$whereConditionsEgresos[] = "e.categoria = 'Transferencia'";
			} else {
				$whereConditionsEgresos[] = "e.categoria = 'Transferencia'";
			}

			// Reconstruir los WHERE clauses con el filtro de categoría
			$whereClauseIngresos = 'WHERE ' . implode(' AND ', $whereConditionsIngresos);
			$whereClauseEgresos = 'WHERE ' . implode(' AND ', $whereConditionsEgresos);

			// Consulta UNION para combinar ingresos y egresos
			$sql = "
				(SELECT 
					i.id,
					i.fecha,
					i.concepto,
					i.comprobante,
					i.monto,
					i.forma_pago,
					i.moneda,
					i.cambio,
					(i.monto * i.cambio) as monto_moneda,
					c.caja,
					'ingreso' as tipo_transaccion,
					i.anulado
				FROM ingresos i
				LEFT JOIN cajas c ON i.id_caja = c.id
				$whereClauseIngresos)
				
				UNION ALL
				
				(SELECT 
					e.id,
					e.fecha,
					e.concepto,
					e.comprobante,
					(e.monto * -1) as monto,
					e.forma_pago,
					e.moneda,
					e.cambio,
					(e.monto * e.cambio) as monto_moneda,
					c.caja,
					'egreso' as tipo_transaccion,
					e.anulado
				FROM egresos e
				LEFT JOIN cajas c ON e.id_caja = c.id
				$whereClauseEgresos)
				
				ORDER BY fecha DESC, id DESC
			";

			// Combinar parámetros para ambas partes del UNION
			$allParams = array_merge($paramsIngresos, $paramsEgresos);

			$stm = $this->pdo->prepare($sql);
			$stm->execute($allParams);
			return $stm->fetchAll(PDO::FETCH_OBJ);

		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
