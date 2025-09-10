<?php

class mdlDashboardOperativo
{
    private $pdo;

    public function __CONSTRUCT()
    {
        try {
            $this->pdo = Database::StartUp();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public  function mdlAniosConIngresosValidos()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT YEAR(fecha) as anio
            FROM ingresos
            WHERE anulado IS NULL
            GROUP BY anio
        ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function mdlComparativaIngresoEgreso($anio)
    {
        try {
            // Consulta a la base de datos
            $stmt = $this->pdo->prepare("SELECT 
                COALESCE(ingresos.mes, egresos.mes) AS mes,
                COALESCE(ingresos.total_ingresos, 0) AS total_ingresos,
                COALESCE(egresos.total_egresos, 0) AS total_egresos,
                COALESCE(ingresos.total_ingresos, 0) - COALESCE(egresos.total_egresos, 0) AS balance
            FROM 
                (SELECT MONTH(i.fecha) AS mes, SUM(i.monto) AS total_ingresos
                FROM ingresos i
                WHERE YEAR(i.fecha) = :anio
                AND i.anulado IS NULL
                AND i.categoria <> 'Transferencia'
                GROUP BY mes) AS ingresos
            LEFT JOIN
                (SELECT MONTH(e.fecha) AS mes, SUM(e.monto) AS total_egresos
                FROM egresos e
                WHERE YEAR(e.fecha) = :anio
                AND e.anulado IS NULL
                AND e.categoria <> 'Transferencia'
                GROUP BY mes) AS egresos
            ON ingresos.mes = egresos.mes

            UNION

            SELECT 
                COALESCE(ingresos.mes, egresos.mes) AS mes,
                COALESCE(ingresos.total_ingresos, 0) AS total_ingresos,
                COALESCE(egresos.total_egresos, 0) AS total_egresos,
                COALESCE(ingresos.total_ingresos, 0) - COALESCE(egresos.total_egresos, 0) AS balance
            FROM 
                (SELECT MONTH(i.fecha) AS mes, SUM(i.monto) AS total_ingresos
                FROM ingresos i
                WHERE YEAR(i.fecha) = :anio
                AND i.anulado IS NULL
                AND i.categoria <> 'Transferencia'
                GROUP BY mes) AS ingresos
            RIGHT JOIN
                (SELECT MONTH(e.fecha) AS mes, SUM(e.monto) AS total_egresos
                FROM egresos e
                WHERE YEAR(e.fecha) = :anio
                AND e.anulado IS NULL
                AND e.categoria <> 'Transferencia'
                GROUP BY mes) AS egresos
            ON ingresos.mes = egresos.mes

            ORDER BY mes;

                ;");
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute(); // Ejecuta la consulta

            // Devuelve todas las filas como un array asociativo
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function mdlIngresosEgresosTotalesDelAnio($anio)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT 
                YEAR(fecha) as anio, 
                SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) as total_ingresos,
                SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as total_egresos,
                SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) - 
                SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as balance
            FROM 
                (SELECT fecha, monto, 'ingreso' as tipo FROM ingresos WHERE anulado IS NULL AND categoria <> 'Transferencia'
                UNION ALL
                SELECT fecha, monto, 'egreso' as tipo FROM egresos WHERE anulado IS NULL AND categoria <> 'Transferencia') as movimientos
            WHERE 
                YEAR(fecha) = :anio
                
            GROUP BY 
                anio;

                    ");

            // Pasar el parámetro del año
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getVentasAnuales($anio_actual)
    {
        try {
            $anio_anterior = $anio_actual - 1;

            $sql = "SELECT 
                        YEAR(fecha) as anio,
                        MONTH(fecha) as mes,
                        SUM(monto) as total
                    FROM ingresos 
                    WHERE YEAR(fecha) IN (?, ?)
                        AND anulado IS NULL
                        AND id_venta IS NOT NULL
                    GROUP BY YEAR(fecha), MONTH(fecha)
                    ORDER BY YEAR(fecha), MONTH(fecha)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$anio_anterior, $anio_actual]);

            // Inicializar arrays para ambos años
            $datos_actual = array_fill(1, 12, 0);
            $datos_anterior = array_fill(1, 12, 0);

            // Obtener resultados y organizarlos
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row['anio'] == $anio_actual) {
                    $datos_actual[$row['mes']] = (int)$row['total'];
                } else {
                    $datos_anterior[$row['mes']] = (int)$row['total'];
                }
            }

            return array(
                'anio_actual' => $datos_actual,
                'anio_anterior' => $datos_anterior
            );
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error en getVentasAnuales: " . $e->getMessage());
            throw new Exception("Error al obtener las ventas anuales");
        }
    }

    // Consulta para datos anuales
    public function mdlDatosAnuales()
    {
        try {
            $stmt = $this->pdo->prepare("
                    SELECT 
                        YEAR(fecha) as periodo,
                        COALESCE(SUM(ingreso), 0) AS total_ingresos,
                        COALESCE(SUM(egreso), 0) AS total_egresos
                    FROM (
                        SELECT 
                            fecha,
                            monto as ingreso,
                            0 as egreso
                        FROM ingresos
                        WHERE anulado IS NULL
                        
                        UNION ALL
                        
                        SELECT 
                            fecha,
                            0 as ingreso,
                            monto as egreso
                        FROM egresos
                        WHERE anulado IS NULL
                    ) AS movimientos
                    GROUP BY YEAR(fecha)
                    ORDER BY periodo;
                ");

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en consulta anual: " . $e->getMessage());
        }
    }

    // Consulta para datos mensuales de un año específico
    public function mdlDatosMensuales($year)
    {
        try {
            $stmt = $this->pdo->prepare("
                    SELECT 
                        DATE_FORMAT(fecha, '%Y-%m') as periodo,
                        COALESCE(SUM(ingreso), 0) AS total_ingresos,
                        COALESCE(SUM(egreso), 0) AS total_egresos
                    FROM (
                        SELECT 
                            fecha,
                            monto as ingreso,
                            0 as egreso
                        FROM ingresos
                        WHERE YEAR(fecha) = :year 
                        AND anulado IS NULL
                        
                        UNION ALL
                        
                        SELECT 
                            fecha,
                            0 as ingreso,
                            monto as egreso
                        FROM egresos
                        WHERE YEAR(fecha) = :year 
                        AND anulado IS NULL
                    ) AS movimientos
                    GROUP BY DATE_FORMAT(fecha, '%Y-%m')
                    ORDER BY periodo;
                ");
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en consulta mensual: " . $e->getMessage());
        }
    }

    // Consulta para datos diarios en un rango de hasta 8 días
    public function mdlDatosDiarios($fecha_inicio, $fecha_fin)
    {
        try {
            // Validar que el rango no exceda 8 días
            $start = new DateTime($fecha_inicio);
            $end = new DateTime($fecha_fin);
            $diff = $start->diff($end);

            if ($diff->days > 8) {
                throw new Exception("El rango de fechas no puede exceder 8 días");
            }

            $stmt = $this->pdo->prepare("
                    SELECT 
                        DATE(fecha) as periodo,
                        COALESCE(SUM(ingreso), 0) AS total_ingresos,
                        COALESCE(SUM(egreso), 0) AS total_egresos
                    FROM (
                        SELECT 
                            fecha,
                            monto as ingreso,
                            0 as egreso
                        FROM ingresos
                        WHERE fecha BETWEEN :fecha_inicio AND :fecha_fin
                        AND anulado IS NULL
                        
                        UNION ALL
                        
                        SELECT 
                            fecha,
                            0 as ingreso,
                            monto as egreso
                        FROM egresos
                        WHERE fecha BETWEEN :fecha_inicio AND :fecha_fin
                        AND anulado IS NULL
                    ) AS movimientos
                    GROUP BY DATE(fecha)
                    ORDER BY periodo;
                ");

            $stmt->bindParam(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_fin', $fecha_fin, PDO::PARAM_STR);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en consulta diaria: " . $e->getMessage());
        }
    }

    //Da informacion sobre ventas de productos, su categoria, precio unitario, costo unitario, total vendido de ese producto en el año, ganacia obtenida ese año
    //Ordenado por más rentables hacia abajo

    public function mdlVentasProductosCategoria($anio)
    {
        try {
            // Consulta a la base de datos
            $stmt = $this->pdo->prepare("SELECT 
                v.id_producto,
                p.codigo, 
                p.producto, 
                m.marca, 
                c.categoria, 
                SUM(v.cantidad) as cantidad, 
                v.precio_costo, 
                v.precio_venta as precio_unitario,
                SUM(v.cantidad * v.precio_venta) as ingreso_total, 
                SUM(v.cantidad * v.precio_costo) as gasto_total,
                SUM(v.cantidad * v.precio_venta) - SUM(v.cantidad * v.precio_costo) as diferencia
            FROM 
                ventas v
            JOIN 
                productos p ON v.id_producto = p.id
            JOIN 
                marcas m ON p.marca = m.id
            JOIN 
                categorias c on p.id_categoria = c.id
            WHERE 
                v.anulado = 0
                AND YEAR(v.fecha_venta) = :anio
            GROUP BY 
                v.id_producto, 
                m.marca
            ORDER BY 
                diferencia DESC
            LIMIT 100
                ;");
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute(); // Ejecuta la consulta

            // Devuelve todas las filas como un array asociativo
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function mdlRentabilidadProductosCategorias($anio, $filtro)
    {
        try {
            $stmt = "SELECT ";
            if ($filtro == 'categoria') {
                $stmt .= "c.categoria AS etiqueta, ";
            } else { // por defecto será 'productos'
                $stmt .= "p.producto AS etiqueta, ";
            }
            $stmt .= "
            SUM(v.cantidad) AS cantidad, 
            SUM(v.cantidad * v.precio_venta) AS ingreso_total, 
            SUM(v.cantidad * v.precio_costo) AS gasto_total,
            SUM(v.cantidad * v.precio_venta) - SUM(v.cantidad * v.precio_costo) AS ganancia
        FROM 
            ventas v
        JOIN 
            productos p ON v.id_producto = p.id
        JOIN 
            categorias c ON p.id_categoria = c.id
        WHERE 
            v.anulado = 0 
            AND YEAR(v.fecha_venta) = :anio
        GROUP BY 
            etiqueta
        ORDER BY 
            ganancia DESC
        LIMIT 10";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public  function mdlAvisoDeStock($anio)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT 
            p.stock, 
            p.codigo, 
            p.producto, 
            COUNT(v.id_producto) AS cantidad_ventas,
            SUM(v.total) AS total_ventas_gs,
            SUM(v.total) - SUM(v.precio_costo*v.cantidad) as beneficios
        FROM 
            productos p
        JOIN 
            ventas v ON v.id_producto = p.id
        WHERE 
            v.anulado = 0
            AND YEAR(v.fecha_venta) = :anio
            AND p.stock < 5
        GROUP BY 
            v.id_producto
        HAVING 
            cantidad_ventas > 10
        ORDER BY 
            cantidad_ventas DESC
        LIMIT 100;

        ");
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
