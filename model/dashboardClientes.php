<?php

class mdlDashboardClientes {
    private $pdo;

    public function __CONSTRUCT()
    {
        try {
            $this->pdo = Database::StartUp();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function mdlAniosConIngresosValidos()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT DISTINCT YEAR(fecha) AS anio
                FROM ingresos
                WHERE anulado IS NULL
                AND categoria = 'Transferencia'
                ORDER BY anio DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getCustomerSalesPerformance($limit = null)
    {
        try {
            $query = "
                SELECT 
                    c.id, 
                    c.nombre, 
                    COALESCE(SUM(v.precio_venta * v.cantidad) - SUM(v.precio_costo * v.cantidad), 0) AS rentabilidad,
                    SUM(v.precio_venta * v.cantidad) AS total_ventas,
                    SUM(v.precio_costo * v.cantidad) AS total_costo,
                    c.mayorista = 'SI' AS es_cliente_vip
                FROM 
                    clientes c
                LEFT JOIN 
                    ventas v ON c.id = v.id_cliente
                GROUP BY 
                    c.id, c.nombre, c.mayorista
                HAVING 
                    rentabilidad > 0
                ORDER BY 
                    rentabilidad DESC
                LIMIT :limit;
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getVIPCustomerStats()
    {
        try {
            $query = "
                SELECT 
                    COUNT(*) AS total_clientes_vip,
                    COALESCE(SUM(total_ventas), 0) AS ventas_totales_vip,
                    COALESCE(SUM(rentabilidad), 0) AS rentabilidad_total_vip,
                    COALESCE(AVG(rentabilidad), 0) AS promedio_rentabilidad_vip
                FROM (
                    SELECT 
                        c.id, 
                        SUM(v.precio_venta * v.cantidad) AS total_ventas,
                        SUM(v.precio_venta * v.cantidad) - SUM(v.precio_costo * v.cantidad) AS rentabilidad
                    FROM 
                        clientes c
                    JOIN 
                        ventas v ON c.id = v.id_cliente
                    WHERE 
                        c.mayorista = 'SI'
                    GROUP BY 
                        c.id
                ) AS ventas_clientes_vip;
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function mdlTasaConversion($anio)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT 
                COUNT(DISTINCT p.id_presupuesto) AS total_presupuestos,
                SUM(CASE WHEN p.ingreso_total = COALESCE(v.total, 0) THEN 1 ELSE 0 END) AS concretados,
                SUM(CASE WHEN p.ingreso_total > COALESCE(v.total, 0) THEN 1 ELSE 0 END) AS no_concretados
            FROM 
                (SELECT 
                    id_presupuesto, 
                    SUM(precio_venta * cantidad) AS ingreso_total
                FROM 
                    presupuestos
                WHERE 
                    YEAR(fecha_presupuesto) = :anio
                GROUP BY 
                    id_presupuesto) p
            LEFT JOIN 
                (SELECT 
                    id_presupuesto, 
                    SUM(total) AS total
                FROM 
                    ventas
                WHERE 
                    YEAR(fecha_venta) = :anio
                GROUP BY 
                    id_presupuesto) v 
            ON 
                v.id_presupuesto = p.id_presupuesto;");
            
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function mdltablaInfoPresupuestosNoConcretados($anio, $orderBy = 'ingreso_total', $orderDir = 'DESC')
    {
        try {
            // Mapeo de campos para ORDER BY
            $orderByMap = [
                'fecha_presupuesto' => 'p.fecha_presupuesto',
                'ingreso_total' => 'p.ingreso_total',
                'nombre_cliente' => 'c.nombre',
                'falta_por_pagar' => '(p.ingreso_total - COALESCE(v.total, 0))'
            ];

            $orderByField = $orderByMap[$orderBy] ?? 'p.ingreso_total';
            
            $query = "SELECT 
                    p.id_presupuesto,
                    p.id_cliente,  
                    p.fecha_presupuesto,
                    p.ingreso_total,
                    v.total,
                    c.nombre AS nombre_cliente,
                    (p.ingreso_total - COALESCE(v.total, 0)) AS falta_por_pagar
                FROM 
                    (SELECT 
                        id_presupuesto, 
                        id_cliente,
                        fecha_presupuesto,
                        SUM(precio_venta * cantidad) AS ingreso_total
                    FROM 
                        presupuestos
                    WHERE 
                        YEAR(fecha_presupuesto) = :anio
                    GROUP BY 
                        id_presupuesto, id_cliente, fecha_presupuesto) p
                LEFT JOIN 
                    (SELECT 
                        id_presupuesto, 
                        SUM(total) AS total
                    FROM 
                        ventas
                    WHERE 
                        YEAR(fecha_venta) = :anio
                    GROUP BY 
                        id_presupuesto) v 
                ON 
                    v.id_presupuesto = p.id_presupuesto
                JOIN 
                    clientes c ON c.id = p.id_cliente
                WHERE 
                    p.ingreso_total > COALESCE(v.total, 0)  
                ORDER BY " . $orderByField . " " . $orderDir . ";";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function optimizeTables()
    {
        try {
            $tables = ['ingresos', 'clientes', 'ventas', 'presupuestos'];
            foreach ($tables as $table) {
                $stmt = $this->pdo->prepare("OPTIMIZE TABLE " . $table);
                $stmt->execute();
            }
            echo "Tablas optimizadas correctamente.";
        } catch (PDOException $e) {
            echo "Error al optimizar las tablas: " . $e->getMessage();
        }
    }
}
