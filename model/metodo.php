<?php
class metodo
{
	private $pdo;
    
    public $id;
    public $metodo;
    public $anulado;
    public $saldo_inicial;
    public $porcentaje;
    public $fecha_inicio;

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

	public function Listar2()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("
            SELECT *, 
                (
                    SELECT SUM(
                        CASE 
                            WHEN i.tarjeta = 'CREDITO' THEN i.monto - (i.monto * 0.052)
                            WHEN i.tarjeta = 'DEBITO' THEN i.monto - (i.monto * 0.033)
                            ELSE i.monto
                        END
                    ) 
                    FROM ingresos i 
                    WHERE i.forma_pago = m.metodo 
                        AND i.anulado IS NULL 
                        AND i.fecha  >= (SELECT m.fecha_inicio  FROM metodos m WHERE i.forma_pago = m.metodo)
                ) AS ingresos,
                (
                    SELECT SUM(e.monto) 
                    FROM egresos e 
                    WHERE e.forma_pago = m.metodo 
                        AND e.fecha  >= (SELECT m.fecha_inicio  FROM metodos m WHERE e.forma_pago =  m.metodo ) 
                        AND e.anulado IS NULL 
                ) AS egresos
            FROM metodos m 
            WHERE m.anulado = 0
        ");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Listar()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, 
			(SELECT SUM(i.monto) FROM ingresos i WHERE i.categoria<>'SALDO INICIAL' AND i.forma_pago = m.metodo AND i.anulado IS NULL  AND i.fecha >= m.fecha_inicio) AS ingresos, 
			
			(SELECT SUM(e.monto) FROM egresos e WHERE e.forma_pago = m.metodo AND e.anulado IS NULL  AND e.id_compra IS NULL AND e.fecha >=m.fecha_inicio AND id_compra IS NULL ) AS egresos
			
			FROM metodos m WHERE m.anulado = 0;");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

		public function ListarMovimientos($metodo,$desde,$hasta)
	{
		try
		   
		{
		     $rango_ingreso =$desde != ''? " AND CAST(i.fecha as date) >= '$desde' AND CAST(i.fecha as date) <= '$hasta'":'';
		     $rango_egreso = $desde != ''?" AND CAST(e.fecha as date) >= '$desde' AND CAST(e.fecha as date) <= '$hasta'":'';
		   // AND CAST(fecha AS date)='2023-04-27'
			$result = array();
			$query = "SELECT 
			i.fecha, 
			i.categoria,
			i.concepto,
			i.comprobante,
			i.nro_comprobante,
			(i.monto) as monto,
			i.moneda, 
			i.forma_pago,
			i.anulado, 
			i.tarjeta,
			(SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento,
			(SELECT nombre FROM clientes c WHERE c.id = i.id_cliente ) AS persona
			FROM ingresos i 
			WHERE forma_pago = ? AND anulado IS NULL AND i.fecha  >= (SELECT m.fecha_inicio  FROM metodos m WHERE m.metodo = ? $rango_ingreso ) 
				
				UNION ALL 
			SELECT 
			e.fecha,
			e.categoria,
			e.concepto, 
			e.comprobante,
			e.nro_comprobante,
			(e.monto * -1) as monto, 
			e.moneda, e.forma_pago,
			e.anulado, 
			e.tarjeta,
			(SELECT v.descuento FROM compras v WHERE v.id_compra = e.id_compra LIMIT 1) as descuento,
			(SELECT nombre FROM clientes c WHERE c.id = e.id_cliente ) AS persona
			FROM egresos e 
			WHERE  e.forma_pago = ? AND anulado IS NULL  AND e.fecha  >= (SELECT m.fecha_inicio  FROM metodos m WHERE m.metodo = ? $rango_egreso) 
			ORDER BY fecha";
			$stm = $this->pdo->prepare($query);
			$stm->execute(array($metodo ,$metodo, $metodo, $metodo));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function SaldoAnterior($metodo,$desde)
	{
		try
		   
		{
			$query = "SELECT m.*,
			(SELECT SUM(i.monto) FROM ingresos i WHERE i.forma_pago = m.metodo AND i.anulado IS NULL AND i.fecha  >= (SELECT m.fecha_inicio)  AND (CAST( i.fecha AS date) < ?)) AS ingresos,
            
            (SELECT SUM(i.monto*0.052) FROM ingresos i WHERE i.tarjeta='CREDITO' AND  i.forma_pago = m.metodo AND i.anulado IS NULL AND i.fecha  >= (SELECT m.fecha_inicio)  AND (CAST( i.fecha AS date) < ?)) AS credito,
            
            (SELECT SUM(i.monto*0.033) FROM ingresos i WHERE i.tarjeta='DEBITO' AND  i.forma_pago = m.metodo AND i.anulado IS NULL AND i.fecha  >= (SELECT m.fecha_inicio)  AND (CAST( i.fecha AS date) < ?)) AS debito,
            
			(SELECT SUM(e.monto) FROM egresos e WHERE e.forma_pago = m.metodo AND e.anulado IS NULL AND e.fecha  >= (SELECT m.fecha_inicio)  AND (CAST( e.fecha AS date) < ?)) AS egresos
			 FROM metodos m WHERE m.metodo= ? ";
			$stm = $this->pdo->prepare($query);
			$stm->execute(array( $desde, $desde, $desde, $desde, $metodo));

			return $stm->fetchAll(PDO::FETCH_OBJ);
			
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function SaldoAnteriorBalance($desde)
	{
		try
		   
		{
			$result = array();
			// var_dump($desde);
			// die();

			$query = "SELECT m.*,
			(SELECT SUM(i.monto) FROM ingresos i WHERE i.forma_pago = m.metodo AND i.anulado IS NULL AND i.fecha  >= (SELECT m.fecha_inicio)  AND (CAST( i.fecha AS date) < '$desde')) AS ingresos,
			(SELECT SUM(e.monto) FROM egresos e WHERE e.forma_pago = m.metodo AND e.anulado IS NULL AND e.fecha  >= (SELECT m.fecha_inicio)  AND (CAST( e.fecha AS date) < '$desde')) AS egresos
			 FROM metodos m 
			 WHERE  m.anulado=0";
			$stm = $this->pdo->prepare($query);
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
			
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
		public function SaldoActualBalance($hasta)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("
            SELECT *, 
                (
                    SELECT SUM(
                        CASE 
                            WHEN i.tarjeta = 'CREDITO' THEN i.monto - (i.monto * 0.052)
                            WHEN i.tarjeta = 'DEBITO' THEN i.monto - (i.monto * 0.033)
                            ELSE i.monto
                        END
                    ) 
                    FROM ingresos i 
                    WHERE i.forma_pago = m.metodo 
                        AND i.anulado IS NULL 
                        AND i.fecha  >= (SELECT m.fecha_inicio  FROM metodos m WHERE i.forma_pago = m.metodo)  AND (CAST( i.fecha AS date) <= '$hasta')
                ) AS ingresos,
                (
                    SELECT SUM(e.monto) 
                    FROM egresos e 
                    WHERE e.forma_pago = m.metodo 
                        AND e.fecha  >= (SELECT m.fecha_inicio  FROM metodos m WHERE e.forma_pago =  m.metodo )  AND (CAST( e.fecha AS date) <= '$hasta')
                        AND e.anulado IS NULL 
                ) AS egresos
            FROM metodos m 
            WHERE m.anulado = 0
        ");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Obtener($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM metodos WHERE id = ?");
			          

			$stm->execute(array($id));
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
			            ->prepare("DELETE FROM pagos_tmp WHERE id = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Anular($id)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE metodos SET anulado = 1 WHERE id = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	// public function Vaciar()
	// {
	// 	try 
	// 	{
	// 		$stm = $this->pdo
	// 		            ->prepare("DELETE FROM pagos_tmp");			          

	// 		$stm->execute(array($id));
	// 	} catch (Exception $e) 
	// 	{
	// 		die($e->getMessage());
	// 	}
	// }

	public function Actualizar($data)
	{
		try 
		{
			$sql = "UPDATE metodos SET 

						metodo          = ?,
						saldo_inicial   = ?,
						porcentaje      = ?,
						fecha_inicio   = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->metodo,
				    	$data->saldo_inicial,
					    $data->porcentaje,
					    $data->fecha_inicio,
				    	$data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(metodo $data)
	{
		try 
		{
		$sql = "INSERT INTO metodos (metodo, saldo_inicial, porcentaje, fecha_inicio) 
		        VALUES (?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->metodo,
					$data->saldo_inicial,
					$data->porcentaje,
					$data->fecha_inicio
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}