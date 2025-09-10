<?php

class mdlDashboardActual
{
    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = Database::StartUp();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function mdlMontoventas()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT SUM(total) as monto_total
                                        FROM ventas
                                        WHERE DATE(fecha_venta) = CURRENT_DATE;");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function mdlVentasRecientes($limit = 20)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT v.id, v.id_cliente, c.nombre, p.producto, v.total, v.fecha_venta, v.estado
                                        FROM ventas v
                                        JOIN productos p ON p.id = v.id_producto
                                        JOIN clientes c ON c.id = v.id_cliente
                                        ORDER BY fecha_venta DESC
                                        LIMIT :limit;");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function mdlProductosTopVentas($limit = 5)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT p.producto, SUM(v.total - (v.precio_costo*v.cantidad)) AS cantidad
                                        FROM ventas v
                                        JOIN productos p ON v.id_producto = p.id
                                        WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE)
                                        GROUP BY v.id_producto
                                        ORDER BY cantidad DESC
                                        LIMIT :limit;");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function mdlComparativaMensual($numMeses = 6)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT DATE_FORMAT(fecha_venta, '%b') AS mes, SUM(total) AS total
                                        FROM ventas
                                        WHERE YEAR(fecha_venta) = YEAR(CURRENT_DATE)
                                        AND MONTH(fecha_venta) >= MONTH(DATE_SUB(CURRENT_DATE, INTERVAL :numMeses MONTH))
                                        AND MONTH(fecha_venta) <= MONTH(CURRENT_DATE)
                                        GROUP BY MONTH(fecha_venta)
                                        ORDER BY MONTH(fecha_venta);");
            $stmt->bindParam(':numMeses', $numMeses, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

public function LucroEgresoMensual($mesAnterior = false)
{
    try {
        // Definir fechas según el mes seleccionado
        if ($mesAnterior) {
            $desde = date('Y-m-01', strtotime('first day of last month'));
            $hasta = date('Y-m-t', strtotime('last day of last month'));
        } else {
            $desde = date('Y-m-01'); // Primer día del mes actual
            $hasta = date('Y-m-t');  // Último día del mes actual
        }

        // Obtener total de ventas del mes
        $ventas = $this->AgrupadoVentaSinFiltro($desde, $hasta);

        $totalVentas = array_sum(array_column($ventas, 'total')) ?? 0;
        $totalCostos = array_sum(array_column($ventas, 'costo')) ?? 0;
        $utilidadBruta= $totalVentas - $totalCostos;

       

        // Obtener total de egresos
        $stmt = $this->pdo->prepare("
            SELECT SUM(monto) AS total_egresos
            FROM egresos
            WHERE CAST(fecha AS date) BETWEEN ? AND ?
              AND id_acreedor IS NULL 
              AND anulado IS NULL 
              AND categoria NOT IN ('compra', 'Devolución', 'COMPRA DE MERCADERIAS', 'Transferencia')
        ");
        $stmt->execute([$desde, $hasta]);
        $totalGastos = $stmt->fetchColumn() ?? 0;

        // Calcular utilidad bruta
        $lucro = $utilidadBruta - $totalGastos;

         return [
            'utilidad_bruta' => $utilidadBruta,
            'lucro' => $lucro,
            'total_gastos' => $totalGastos
        ];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}


    
         




public function LucroMensualPorcentajeCambio()
{
    try {
        // Datos del mes actual
        $datosMesActual = $this->LucroEgresoMensual();
        $lucroMesActual = $datosMesActual['lucro'];

        // Datos del mes anterior
        $datosMesAnterior = $this->LucroEgresoMensual(true);
        $lucroMesAnterior = $datosMesAnterior['lucro'];
        $utilidadBrutaMesAnterior = $datosMesAnterior['utilidad_bruta'];
        $gastosMesAnterior = $datosMesAnterior['total_gastos'];

        // Calcular el porcentaje de cambio
        $porcentajeCambio = ($lucroMesAnterior == 0) 
            ? ($lucroMesActual > 0 ? 100 : 0) 
            : (($lucroMesActual - $lucroMesAnterior) / $lucroMesAnterior) * 100;

        return [
            'lucro_mes_actual' => $lucroMesActual,
            'lucro_mes_anterior' => $lucroMesAnterior,
            'utilidad_bruta_anterior' =>  $utilidadBrutaMesAnterior,
            'gastos_mes_anterior' => $gastosMesAnterior,
            'porcentaje_cambio' => $porcentajeCambio
        ];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}




public function EgresosMensualesPorcentajeCambio()
{
    try {
        // Datos del mes actual
        $datosMesActual = $this->LucroEgresoMensual();
        $totalMesActual = $datosMesActual['total_egresos'];

        // Datos del mes anterior
        $datosMesAnterior = $this->LucroEgresoMensual(true);
        $totalMesAnterior = $datosMesAnterior['total_egresos'];

        // Calcular el porcentaje de cambio
        $porcentajeCambio = ($totalMesAnterior == 0) 
            ? ($totalMesActual > 0 ? 100 : 0) 
            : (($totalMesActual - $totalMesAnterior) / $totalMesAnterior) * 100;

        return [
            'egresos_mes_actual' => $totalMesActual,
            'egresos_mes_anterior' => $totalMesAnterior,
            'porcentaje_cambio' => $porcentajeCambio
        ];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}




    public function mdlCantidadVentasSemanal()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT 
            DAYNAME(fecha_venta) AS dia,  
            DATE(fecha_venta) AS fecha,   
            COUNT(*) AS cantidad_ventas   
        FROM 
            ventas
        WHERE 
            YEAR(fecha_venta) = YEAR(CURDATE())              -- Año actual
            AND MONTH(fecha_venta) = MONTH(CURDATE())        -- Mes actual
            AND WEEK(fecha_venta, 1) = WEEK(CURDATE(), 1)    -- Semana actual
        GROUP BY 
            fecha
        ORDER BY 
            fecha ASC;");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function mdlCantidadVentasMensual()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT 
            DAY(fecha_venta) AS dia,  
            DATE(fecha_venta) AS fecha,   
            COUNT(*) AS cantidad_ventas   
        FROM 
            ventas
        WHERE 
            YEAR(fecha_venta) = YEAR(CURDATE())              -- Año actual
            AND MONTH(fecha_venta) = MONTH(CURDATE())        -- Mes actual
        GROUP BY 
            fecha
        ORDER BY 
            fecha ASC;");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function mdlMetodosPagosMes()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT DISTINCT(forma_pago) AS metodo, SUM(monto) AS cantidad
            FROM `ingresos`
            WHERE YEAR(fecha) = YEAR(CURDATE())
            AND MONTH(fecha) = MONTH(CURDATE())
            AND categoria <> 'Transferencia'
            GROUP BY forma_pago;");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function mdlDetalleVentasSemanal()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT v.fecha_venta, v.id_venta, v.id_cliente, c.nombre, v.id_producto, p.producto, v.id_vendedor, v.total
            FROM ventas v
            JOIN clientes c ON c.id = v.id_cliente
            JOIN productos p ON p.id = v.id_producto
            WHERE YEAR(v.fecha_venta) = YEAR(CURRENT_DATE)
            AND WEEK(v.fecha_venta, 1) = WEEK(CURRENT_DATE, 1);");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function mdlDetalleVentasMensual()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT v.fecha_venta, v.id_venta, v.id_cliente, c.nombre, v.id_producto, p.producto, v.id_vendedor, v.total
            FROM ventas v
            JOIN clientes c ON c.id = v.id_cliente
            JOIN productos p ON p.id = v.id_producto
            WHERE YEAR(v.fecha_venta) = YEAR(CURRENT_DATE)
            AND MONTH(v.fecha_venta, 1) = MONTH(CURRENT_DATE, 1);");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function mdlVentasDiariasPorcentajeCambio()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT SUM(total) as total_hoy FROM ventas WHERE DATE(fecha_venta) = CURRENT_DATE;");
            $stmt->execute();
            $totalHoy = $stmt->fetch(PDO::FETCH_ASSOC)['total_hoy'];
            $totalHoy = $totalHoy ? $totalHoy : 0;

            $stmt = $this->pdo->prepare("SELECT SUM(total) as total_ayer FROM ventas WHERE DATE(fecha_venta) = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY);");
            $stmt->execute();
            $totalAyer = $stmt->fetch(PDO::FETCH_ASSOC)['total_ayer'];
            $totalAyer = $totalAyer ? $totalAyer : 0;

            if ($totalAyer == 0) {
                $porcentajeCambio = $totalHoy > 0 ? 100 : 0;
            } else {
                $porcentajeCambio = (($totalHoy - $totalAyer) / $totalAyer) * 100;
            }

            return [
                'total_hoy' => $totalHoy,
                'porcentaje_cambio' => $porcentajeCambio
            ];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function mdlVentasPorHorasHoy()
    {
        try {
            // Consulta para obtener las ventas totales por cada hora del día de hoy
            $stmt = $this->pdo->prepare("
                SELECT HOUR(fecha_venta) as hora, SUM(total) as total
                FROM ventas
                WHERE DATE(fecha_venta) = CURRENT_DATE
                GROUP BY HOUR(fecha_venta)
                ORDER BY HOUR(fecha_venta);
            ");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Inicializar el array con 24 horas del día, todas en 0
            $ventasPorHoras = array_fill(0, 24, 0);

            // Llenar el array con los totales de las horas donde hay registros
            foreach ($result as $row) {
                $ventasPorHoras[$row['hora']] = (float)$row['total'];
            }

            return $ventasPorHoras;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

   public function mdlVentasMensualesPorcentajeCambio()
    {
        try {
            // Ventas del mes actual
            $stmt = $this->pdo->prepare("SELECT SUM(total) as total_mes_actual FROM ventas WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE) AND YEAR(fecha_venta) = YEAR(CURRENT_DATE) AND anulado=0;");
            $stmt->execute();
            $totalMesActual = $stmt->fetch(PDO::FETCH_ASSOC)['total_mes_actual'];
            $totalMesActual = $totalMesActual ? $totalMesActual : 0;

            // Ventas del mes anterior
            $stmt = $this->pdo->prepare("SELECT SUM(total) as total_mes_anterior FROM ventas WHERE MONTH(fecha_venta) = MONTH(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)) AND YEAR(fecha_venta) = YEAR(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH));");
            $stmt->execute();
            $totalMesAnterior = $stmt->fetch(PDO::FETCH_ASSOC)['total_mes_anterior'];
            $totalMesAnterior = $totalMesAnterior ? $totalMesAnterior : 0;

            // Calcular el porcentaje de cambio
            if ($totalMesAnterior == 0) {
                $porcentajeCambio = $totalMesActual > 0 ? 100 : 0;
            } else {
                $porcentajeCambio = (($totalMesActual - $totalMesAnterior) / $totalMesAnterior) * 100;
            }

            return [
                'total_mes_actual' => $totalMesActual,
                'porcentaje_cambio' => $porcentajeCambio
            ];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function mdlVentasDiariasMesActual()
    {
        try {
            // Preparar la consulta para obtener las ventas diarias en el mes actual
            $stmt = $this->pdo->prepare("
            SELECT DATE(fecha_venta) as dia, SUM(total) as total
            FROM ventas
            WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE) 
            AND YEAR(fecha_venta) = YEAR(CURRENT_DATE)
            GROUP BY DATE(fecha_venta)
            ORDER BY dia ASC;
        ");

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Crear un array con las ventas diarias, inicializando días sin ventas con valor 0
            $ventasDiariasMes = [];
            $diasMes = date('t');  // Número de días en el mes actual

            // Rellenar el array con cada día del mes
            for ($day = 1; $day <= $diasMes; $day++) {
                $fecha = date('Y-m') . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                $ventasDiariasMes[$fecha] = 0;
            }

            // Asignar los valores de las ventas del resultado de la consulta
            foreach ($result as $row) {
                $ventasDiariasMes[$row['dia']] = (float) $row['total'];
            }

            return $ventasDiariasMes;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

   

    public function mdlIngresosDiasSemanaActual()
    {
        try {
            // Preparar la consulta para obtener ingresos diarios de lunes a sábado de la semana actual
            $stmt = $this->pdo->prepare("
            SELECT DAYOFWEEK(fecha) AS dia_semana, SUM(monto) AS total
            FROM ingresos
            WHERE WEEK(fecha, 1) = WEEK(CURRENT_DATE, 1)
            AND YEAR(fecha) = YEAR(CURRENT_DATE)
            AND DAYOFWEEK(fecha) BETWEEN 2 AND 7
            GROUP BY dia_semana
            ORDER BY dia_semana ASC;
        ");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Inicializar un array para los días 2 (lunes) a 7 (sábado) con valores iniciales de 0
            $ingresosSemana = array_fill(2, 6, 0); // Claves del 2 al 7

            // Asignar los valores de los ingresos según el resultado de la consulta
            foreach ($result as $row) {
                $ingresosSemana[$row['dia_semana']] = (float) $row['total'];
            }

            return $ingresosSemana;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerEgresosPorDiaSemana()
    {
        try {
            $stmt = $this->pdo->prepare("
            SELECT 
                CASE DAYOFWEEK(fecha)
                    WHEN 1 THEN 'Dom'
                    WHEN 2 THEN 'Lun'
                    WHEN 3 THEN 'Mar'
                    WHEN 4 THEN 'Mié'
                    WHEN 5 THEN 'Jue'
                    WHEN 6 THEN 'Vie'
                    WHEN 7 THEN 'Sáb'
                END as dia,
                SUM(monto) as total_egresos
            FROM egresos
            WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
              AND fecha < DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 1 WEEK)
              AND categoria <> 'Transferencia'
            GROUP BY dia
            ORDER BY 
                CASE dia
                    WHEN 'Lun' THEN 1
                    WHEN 'Mar' THEN 2
                    WHEN 'Mié' THEN 3
                    WHEN 'Jue' THEN 4
                    WHEN 'Vie' THEN 5
                    WHEN 'Sáb' THEN 6
                    WHEN 'Dom' THEN 7
                END
        ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle error
            return [];
        }
    }
    
    public function AgrupadoVentaSinFiltro($desde, $hasta)
	{
		try {
			$stm = $this->pdo->prepare(
				"SELECT v.fecha_venta,  v.id_venta,
					p.producto, SUM(v.cantidad) as cantidad, SUM(v.total) as total, 
					SUM(v.precio_costo) as costo, v.id_cliente, c.nombre, 
					cap.categoria as categoria, ca.categoria as sub_categoria,
					v.contado,  
					(SELECT user FROM usuario WHERE id = IF(pres.id_vendedor IS NOT NULL, pres.id_vendedor, v.id_vendedor) ) as vendedor
				FROM ventas v
                		LEFT JOIN presupuestos pres ON v.id_presupuesto = pres.id
						LEFT JOIN productos p ON v.id_producto = p.id
						LEFT JOIN categorias ca ON ca.id = p.id_categoria 
						LEFT JOIN categorias cap ON cap.id = ca.id_padre 
						LEFT JOIN clientes c ON v.id_cliente = c.id 
						WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ?  AND v.anulado = 0  GROUP BY v.id_venta  
				ORDER BY `vendedor` DESC"
			);
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

   
}
