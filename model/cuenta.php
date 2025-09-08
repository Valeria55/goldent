<?php
class cuenta
{
	private $pdo;
    
    public $id;
    public $id_cliente;
    public $fecha_emitida;
    public $fecha_pagada;
    public $comprobante;
    public $nro_comprobante;
    public $monto;
    public $saldo;
    public $estado;

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
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM cuentas ORDER BY id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	

	public function Obtener($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM cuentas WHERE id = ?");
			          

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
			            ->prepare("DELETE FROM cuentas WHERE id = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try 
		{
			$sql = "UPDATE cuentas SET 

						id_cliente        = ?,
						fecha_emitida      		= ?,
						fecha_pagada          = ?, 
						comprobante        = ?,
						nro_comprobante        = ?,
						monto        = ?,
						saldo        = ?,
						estado        = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->id_cliente,
				    	$data->fecha_emitida, 
                        $data->fecha_pagada,                        
                        $data->comprobante,
                        $data->nro_comprobante,
                        $data->monto,
                        $data->saldo,
                        $data->estado,
                        $data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(cuenta $data)
	{
		try 
		{
		$sql = "INSERT INTO cuentas (id_cliente, fecha_emitida, fecha_pagada, comprobante, nro_comprobante, monto, saldo, estado) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_cliente, 
					$data->fecha_emitida, 
                    $data->fecha_pagada,
                    $data->comprobante,
                    $data->nro_comprobante,
                    $data->monto,
                    $data->saldo,
                    $data->estado
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}