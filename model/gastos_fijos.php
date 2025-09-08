<?php
class gastos_fijos
{
    // Propiedad para almacenar la conexión a la base de datos
	private $pdo;
    
    // Propiedades para representar campos de la tabla "gastos_fijos"
    public $id;
    public $descripcion;
    public $monto;
    public $fecha;
	public $anulado;
	// public $gastos_fijos;


	// Constructor de la clase
	public function __CONSTRUCT()
	{
		try
		{
            // Inicializa la propiedad $pdo con una conexión a la base de datos
			$this->pdo = Database::StartUp();     
		}
		catch(Exception $e)
		{
            // Si ocurre un error al conectarse a la base de datos, muestra el mensaje de error y finaliza
			die($e->getMessage());
		}
	}
    public function Listar()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM gastos_fijos
			ORDER BY id ASC;");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarFiltro($desde, $hasta)
{
    try
    {
      
        $result = array();
        if (!empty($desde) && !empty($hasta)) {
            $rango = " AND CAST(g.fecha as date) >= '$desde' AND CAST(g.fecha as date) <= '$hasta'";
        } else {
            $rango = "";
		}

        $stm = $this->pdo->prepare("SELECT g.* FROM gastos_fijos g
		WHERE g.anulado IS NULL $rango
			ORDER BY g.id ASC;");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
    }
		catch(Exception $e)
    {
        die($e->getMessage());
    }
	}
	public function ListarPDF()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * 
			FROM egresos e
			WHERE id_gasto_fijo IS NOT NULL 
			ORDER BY e.id DESC;");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function Listarinforme()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, c.nombre
			FROM egresos e
			LEFT JOIN  clientes c ON c.id=e.id_cliente
            WHERE id_gasto_fijo IS NOT NULL
			ORDER BY e.id DESC;");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarDetalles($id)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT e.id as id, u.user, 
			e.fecha_gasto_fijo, e.monto, e.comprobante, c.nombre
			FROM egresos e 
			LEFT JOIN clientes c ON e.id_cliente = c.id
			LEFT JOIN usuario u ON e.id_usuario = u.id 
			WHERE e.anulado IS NULL 
			AND e.id_gasto_fijo = ?
			ORDER BY e.id DESC;");
			$stm->execute(array($id));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function SumarMontosPorFechaHoy()
{
    try
    {
        // Obtener la fecha de hoy en el formato adecuado para tu base de datos.
        $fechaHoy = date("Y-m-d");

        // Consulta SQL para sumar los montos para registros con la fecha igual a hoy.
        $query = "SELECT SUM(monto) AS total FROM gastos_fijos WHERE fecha = :fechaHoy AND anulado IS NULL";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':fechaHoy', $fechaHoy, PDO::PARAM_STR);
        $stmt->execute();

        // Obtener el resultado como un objeto.
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        // Verificar si se obtuvo un resultado válido.
        if ($result && isset($result->total)) {
            return $result->total;
        } else {
            // Si no hay resultados, devolvemos 0 o cualquier otro valor predeterminado.
            return 0;
        }
    }
	
    catch(PDOException $e)
    {
        // En lugar de usar die, es recomendable manejar las excepciones de forma más adecuada.
        // Puedes registrar errores en un archivo de registro o lanzar una excepción personalizada.
        throw new Exception($e->getMessage());
    }
}
public function SumarMontosPorFechaManana()
{
    try
    {
        // Obtener la fecha de mañana en el formato adecuado para tu base de datos.
        $fechaManana = date("Y-m-d", strtotime("+1 day"));

        // Consulta SQL para sumar los montos para registros con la fecha igual a mañana.
        $query = "SELECT SUM(monto) AS total FROM gastos_fijos WHERE fecha = :fechaManana AND anulado IS NULL";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':fechaManana', $fechaManana, PDO::PARAM_STR);
        $stmt->execute();

        // Obtener el resultado como un objeto.
        $result1 = $stmt->fetch(PDO::FETCH_OBJ);

        // Verificar si se obtuvo un resultado válido.
        if ($result1 && isset($result1->total)) {
            return $result1->total;
        } else {
            // Si no hay resultados, devolvemos 0 o cualquier otro valor predeterminado.
            return 0;
        }
    }
    catch (Exception $e)
    {
        die($e->getMessage());
    }
}
public function SumarMontosPorFechaAnterior()
{
    try
    {
        // Obtener la fecha actual en el formato adecuado para tu base de datos.
        $fechaActual = date("Y-m-d");

        // Consulta SQL para sumar los montos para registros con la fecha anterior a la fecha actual.
        $query = "SELECT SUM(monto) AS total FROM gastos_fijos WHERE fecha < :fechaActual AND anulado IS NULL";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':fechaActual', $fechaActual, PDO::PARAM_STR);
        $stmt->execute();

        // Obtener el resultado como un objeto.
        $result2 = $stmt->fetch(PDO::FETCH_OBJ);

        // Verificar si se obtuvo un resultado válido.
        if ($result2 && isset($result2->total)) {
            return $result2->total;
        } else {
            // Si no hay resultados, devolvemos 0 o cualquier otro valor predeterminado.
            return 0;
        }
    }
    catch (Exception $e)
    {
        die($e->getMessage());
    }
}

    public function Obtener($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM gastos_fijos WHERE id = ?");
			          

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
			            ->prepare("UPDATE gastos_fijos SET
						anulado = 1
						WHERE id=?");			          

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
			$sql = "UPDATE gastos_fijos SET 

						descripcion      = ?,
						monto      		= ?,
						fecha      		= ?
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->descripcion,
						$data->monto,
						$data->fecha,
				    	$data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
    public function Registrar($data)
	{
		try 
		{
		$sql = "INSERT INTO gastos_fijos (descripcion,monto,fecha) 
		        VALUES (?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->descripcion,
					$data->monto,
					$data->fecha
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}