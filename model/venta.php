<?php
class venta
{
	private $pdo;

	public $id;
	public $id_venta;
	public $id_cliente;
	public $id_vendedor;
	public $vendedor_salon;
	public $id_producto;
	public $precio_costo;
	public $precio_venta;
	public $subtotal;
	public $descuento;
	public $total;
	public $comprobante;
	public $nro_comprobante;
	public $cantidad;
	public $margen_ganancia;
	public $fecha_venta;
	public $metodo;
	public $contado;
	public $id_gift;
	public $id_presupuesto;
	public $id_devolucion;
	public $estado;
	public $paciente;
	public $pagare;

	public $cot_usd;  // Nuevo campo
	public $cot_real; // Nuevo campo

	public function __CONSTRUCT()
	{
		try {
			$this->pdo = Database::StartUp();
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Listar($id_venta)
	{
		try {

			if ($id_venta == 0) {

				$stm = $this->pdo->prepare("SELECT 
				v.id_cliente, 
				v.descuento AS descuentov, 
				SUM(v.precio_costo*v.cantidad) AS costo, 
				(SUM(v.total) - SUM(v.precio_costo*v.cantidad)) AS ganancia,
				v.id, 
				v.id_venta as id_venta, 
				v.comprobante, 
				v.metodo, 
				v.anulado, 
				v.pagare,
				contado, 
				p.producto, 
				SUM(subtotal) as subtotal, 
				descuento, 
				SUM(total) as total, 
				AVG(margen_ganancia) as margen_ganancia, 
				fecha_venta, 
				IF(v.autoimpresor > 0 , CONCAT(LPAD(t.establecimiento, 3, '0'), '-', LPAD(t.punto_expedicion, 3, '0'), '-', LPAD(v.autoimpresor, 7, '0')) ,v.nro_comprobante) AS nro_comprobante, 
				c.nombre as nombre_cli, 
				c.ruc, 
				c.direccion, 
				c.telefono, 
				
				v.id_producto, 
					(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
					(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
					FROM ventas v 
					LEFT JOIN productos p ON v.id_producto = p.id 
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					LEFT JOIN timbrados t ON t.id = v.id_timbrado
					WHERE contado='Contado'
					GROUP BY v.id_venta ORDER BY v.id_venta DESC");
				$stm->execute();
			} else {
				$stm = $this->pdo->prepare("SELECT v.id_cliente, 
				v.descuento AS descuentov, 
				v.id_venta AS id_venta 
				, v.id
				, p.producto,v.comprobante
				, v.metodo
				, v.anulado
				, v.pagare
				, contado, 
				p.codigo,p.iva
				,v.cantidad
				, v.precio_venta
				,v.subtotal
				, v.descuento
				, v.total, 
				v.id_presupuesto,
                v.margen_ganancia
				, fecha_venta, nro_comprobante, 
				c.nombre as nombre_cli
				, c.ruc
				, c.direccion
				, c.telefono,
				 v.id_producto,
				u.user as vendedor, 
				id_gift,
				t.timbrado,
				t.fecha_inicio,
				t.fecha_fin,
				t.establecimiento,
				t.punto_expedicion,
				v.autoimpresor,
				v.paciente,
				10 AS iva
					FROM ventas v 
					LEFT JOIN usuario u ON v.id_vendedor = u.id
					LEFT JOIN productos p ON v.id_producto = p.id 
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					LEFT JOIN timbrados t ON t.id = v.id_timbrado
					WHERE v.id_venta = ? 
					");
				$stm->execute(array($id_venta));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarUltimos30Dias($id_venta)
	{
		try {
			if ($id_venta == 0) {
				// Calcular fechas para los últimos 30 días (incluyendo hoy)
				$fecha_hasta = date('Y-m-d');
				$fecha_desde = date('Y-m-d', strtotime('-29 days'));

				$stm = $this->pdo->prepare("SELECT 
				v.id_cliente, 
				v.descuento AS descuentov, 
				IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta AND a.anulado=0), 0) AS costo,
				(SUM(v.total) - IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta AND a.anulado=0), 0)) AS ganancia,
				v.id, 
				v.id_venta as id_venta, 
				v.comprobante, 
				v.metodo, 
				v.anulado, 
				v.pagare,
				contado, 
				p.producto, 
				SUM(subtotal) as subtotal, 
				descuento, 
				SUM(total) as total, 
				AVG(margen_ganancia) as margen_ganancia, 
				fecha_venta, 
				v.nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, 
                (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
                (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
                FROM ventas v 
                LEFT JOIN productos p ON v.id_producto = p.id 
                LEFT JOIN clientes c ON v.id_cliente = c.id 
                LEFT JOIN timbrados t ON t.id = v.id_timbrado
               
                AND CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ?
                GROUP BY v.id_venta ORDER BY v.id_venta DESC");
				$stm->execute([$fecha_desde, $fecha_hasta]);
			} else {
				$stm = $this->pdo->prepare("SELECT v.id_cliente, 
                v.descuento AS descuentov, 
                v.id_venta AS id_venta 
                , v.id
                , p.producto,v.comprobante
                , v.metodo
                , v.anulado
                , v.pagare
                , contado, 
                p.codigo,p.iva
                ,v.cantidad
                , v.precio_venta
                ,v.subtotal
                , v.descuento
                , v.total, 
                v.id_presupuesto,
                v.margen_ganancia
                , fecha_venta, v.nro_comprobante, 
                c.nombre as nombre_cli
                , c.ruc
                , c.direccion
                , c.telefono,
                 v.id_producto,
                u.user as vendedor, 
                id_gift,
                t.timbrado,
                t.fecha_inicio,
                t.fecha_fin,
                t.establecimiento,
                t.punto_expedicion,
                v.autoimpresor,
                10 AS iva
                    FROM ventas v 
                    LEFT JOIN usuario u ON v.id_vendedor = u.id
                    LEFT JOIN productos p ON v.id_producto = p.id 
                    LEFT JOIN clientes c ON v.id_cliente = c.id 
                    LEFT JOIN timbrados t ON t.id = v.id_timbrado
                    WHERE v.id_venta = ? 
                    ");
				$stm->execute(array($id_venta));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	//LISTAR PARA AJUSTES DE PRODUCTOS (DEVOLUCIONES)
	public function ListarVenta()
	{
		try {
			$stm = $this->pdo->prepare("SELECT id_venta, c.nombre AS cliente 
			FROM ventas v
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE anulado = 0
			GROUP BY id_venta 
			ORDER BY id_venta DESC");
			$stm->execute();
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function ListarAprobar($id_venta)
	{
		try {

			if ($id_venta == 0) {

				$stm = $this->pdo->prepare("SELECT 
				v.id_cliente, 
				v.descuento AS descuentov, 
				SUM(v.precio_costo*v.cantidad) AS costo, 
				(SUM(v.total) - SUM(v.precio_costo*v.cantidad)) AS ganancia, 
				v.id,
				 v.id_venta as id_venta, 
				 v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, v.estado,
					(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
					(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
					FROM ventas v 
					LEFT JOIN productos p ON v.id_producto = p.id 
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					WHERE contado='Credito' AND v.estado='PENDIENTE'
					GROUP BY v.id_venta ORDER BY v.id_venta DESC");
				$stm->execute();
			} else {
				$stm = $this->pdo->prepare("SELECT v.id_cliente, 
				v.descuento AS descuentov, 
				v.id_venta AS id_venta 
				, v.id
				, p.producto,v.comprobante
				, v.metodo
				, v.anulado
				, contado, 
				p.codigo,p.iva
				,v.cantidad
				, v.precio_venta
				,v.subtotal
				, v.descuento
				, v.total, 
                v.margen_ganancia
				, fecha_venta, nro_comprobante, 
				c.nombre as nombre_cli
				, c.ruc
				, c.direccion
				, c.telefono,
				 v.id_producto,
				 v.estado,
				u.user as vendedor, 
				id_gift 
					FROM ventas v 
					LEFT JOIN usuario u ON v.id_vendedor = u.id
					LEFT JOIN productos p ON v.id_producto = p.id 
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					WHERE v.id_venta = ? 
					AND contado='Credito' AND estado='PENDIENTE'
					");
				$stm->execute(array($id_venta));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function ListarAprobarultimos30dias($id_venta)
	{
		try {

			if ($id_venta == 0) {

				$stm = $this->pdo->prepare("SELECT 
				v.id_cliente, 
				v.descuento AS descuentov, 
				SUM(v.precio_costo*v.cantidad) AS costo, 
				(SUM(v.total) - SUM(v.precio_costo*v.cantidad)) AS ganancia, 
				v.id,
				 v.id_venta as id_venta, 
				 v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, v.estado,
					(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
					(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
					FROM ventas v 
					LEFT JOIN productos p ON v.id_producto = p.id 
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					WHERE contado='Credito' AND v.estado='PENDIENTE' AND fecha_venta >= NOW() - INTERVAL 30 DAY
					GROUP BY v.id_venta ORDER BY v.id_venta DESC");
				$stm->execute();
			} else {
				$stm = $this->pdo->prepare("SELECT v.id_cliente, 
				v.descuento AS descuentov, 
				v.id_venta AS id_venta 
				, v.id
				, p.producto,v.comprobante
				, v.metodo
				, v.anulado
				, contado, 
				p.codigo,p.iva
				,v.cantidad
				, v.precio_venta
				,v.subtotal
				, v.descuento
				, v.total, 
                v.margen_ganancia
				, fecha_venta, nro_comprobante, 
				c.nombre as nombre_cli
				, c.ruc
				, c.direccion
				, c.telefono,
				 v.id_producto,
				 v.estado,
				u.user as vendedor, 
				id_gift 
					FROM ventas v 
					LEFT JOIN usuario u ON v.id_vendedor = u.id
					LEFT JOIN productos p ON v.id_producto = p.id 
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					WHERE v.id_venta = ? 
					AND contado='Credito' AND estado='PENDIENTE'
					");
				$stm->execute(array($id_venta));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarAprobado($id_venta)
	{
		try {

			if ($id_venta == 0) {

				$stm = $this->pdo->prepare("SELECT v.id_cliente, v.descuento AS descuentov, SUM(v.precio_costo*v.cantidad) AS costo, (SUM(v.total) - SUM(v.precio_costo*v.cantidad)) AS ganancia, v.id, v.id_venta as id_venta, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, v.estado,
					(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
					(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
					FROM ventas v 
					LEFT JOIN productos p ON v.id_producto = p.id 
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					WHERE contado='Credito' AND v.estado!='PENDIENTE'
					GROUP BY v.id_venta ORDER BY v.id_venta DESC");
				$stm->execute();
			} else {
				$stm = $this->pdo->prepare("SELECT v.id_cliente, 
				v.descuento AS descuentov, 
				v.id_venta AS id_venta 
				, v.id
				, p.producto,v.comprobante
				, v.metodo
				, v.anulado
				, contado, 
				p.codigo,p.iva
				,v.cantidad
				, v.precio_venta
				,v.subtotal
				, v.descuento
				, v.total, 
                v.margen_ganancia
				, fecha_venta, nro_comprobante, 
				c.nombre as nombre_cli
				, c.ruc
				, c.direccion
				, c.telefono,
				 v.id_producto,
				u.user as vendedor, 
				v.estado,
				id_gift 
					FROM ventas v 
					LEFT JOIN usuario u ON v.id_vendedor = u.id
					LEFT JOIN productos p ON v.id_producto = p.id 
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					WHERE v.id_venta = ? 
					AND contado='Credito' AND v.estado!='PENDIENTE'
					");
				$stm->execute(array($id_venta));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarAprobadoUltimos30dias($id_venta)
	{
		try {
			if ($id_venta == 0) {
				// Calcular fechas para los últimos 30 días (incluyendo hoy)
				$fecha_hasta = date('Y-m-d');
				$fecha_desde = date('Y-m-d', strtotime('-29 days'));

				$stm = $this->pdo->prepare("SELECT v.id_cliente, v.descuento AS descuentov, SUM(v.precio_costo*v.cantidad) AS costo, (SUM(v.total) - SUM(v.precio_costo*v.cantidad)) AS ganancia, v.id, v.id_venta as id_venta, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, v.estado,
                (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
                (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
                FROM ventas v 
                LEFT JOIN productos p ON v.id_producto = p.id 
                LEFT JOIN clientes c ON v.id_cliente = c.id 
                WHERE contado='Credito' AND v.estado!='PENDIENTE'
                AND CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ?
                GROUP BY v.id_venta ORDER BY v.id_venta DESC");
				$stm->execute([$fecha_desde, $fecha_hasta]);
			} else {
				$stm = $this->pdo->prepare("SELECT v.id_cliente, 
                v.descuento AS descuentov, 
                v.id_venta AS id_venta 
                , v.id
                , p.producto,v.comprobante
                , v.metodo
                , v.anulado
                , contado, 
                p.codigo,p.iva
                ,v.cantidad
                , v.precio_venta
                ,v.subtotal
                , v.descuento
                , v.total, 
                v.margen_ganancia
                , fecha_venta, nro_comprobante, 
                c.nombre as nombre_cli
                , c.ruc
                , c.direccion
                , c.telefono,
                 v.id_producto,
                u.user as vendedor, 
                v.estado,
                id_gift 
                FROM ventas v 
                LEFT JOIN usuario u ON v.id_vendedor = u.id
                LEFT JOIN productos p ON v.id_producto = p.id 
                LEFT JOIN clientes c ON v.id_cliente = c.id 
                WHERE v.id_venta = ? 
                AND contado='Credito' AND v.estado!='PENDIENTE'
                ");
				$stm->execute(array($id_venta));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function utilidad($fecha)
	{
		try {

			$stm = $this->pdo->prepare("SELECT id_venta, fecha_venta, SUM(precio_costo*cantidad) AS costo, (SUM(total) - SUM(precio_costo*cantidad)) AS ganancia, SUM(total) AS total FROM ventas WHERE MONTH(fecha_venta) = MONTH(?) AND YEAR(fecha_venta) = YEAR(?) AND anulado=0  GROUP BY id_venta ORDER BY id_venta DESC");

			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function AgrupadoVenta($desde, $hasta)
	{
		try {
			$stm = $this->pdo->prepare(
				"SELECT 
				v.fecha_venta,  
				v.id_venta,
				p.producto, 
				SUM(v.cantidad) as cantidad, 
				SUM(v.total) as total, 
				IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta AND a.anulado=0), 0) AS costo, 
				v.id_cliente, 
				c.nombre, 
				cap.categoria as categoria, 
				ca.categoria as sub_categoria,
				v.contado,  
				(SELECT user FROM usuario WHERE id = IF(pres.id_vendedor IS NOT NULL, pres.id_vendedor, v.id_vendedor) ) as vendedor
				FROM ventas v
                		LEFT JOIN presupuestos pres ON v.id_presupuesto = pres.id
						LEFT JOIN productos p ON v.id_producto = p.id
						LEFT JOIN categorias ca ON ca.id = p.id_categoria 
						LEFT JOIN categorias cap ON cap.id = ca.id_padre 
						LEFT JOIN clientes c ON v.id_cliente = c.id 
						WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ?  AND v.anulado = 0 AND v.contado = 'Contado' GROUP BY v.id_venta  
				ORDER BY `vendedor` DESC"
			);
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function AgrupadoVentaCredito($desde, $hasta)
	{
		try {
			$stm = $this->pdo->prepare(
				"SELECT v.fecha_venta,  v.id_venta,
					p.producto, SUM(v.cantidad) as cantidad, SUM(v.total) as total, 
					IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta AND a.anulado=0), 0) AS costo, v.id_cliente, c.nombre, 
					cap.categoria as categoria, ca.categoria as sub_categoria,
					v.contado,  
					(SELECT user FROM usuario WHERE id = IF(pres.id_vendedor IS NOT NULL, pres.id_vendedor, v.id_vendedor) ) as vendedor
				FROM ventas v
                		LEFT JOIN presupuestos pres ON v.id_presupuesto = pres.id
						LEFT JOIN productos p ON v.id_producto = p.id
						LEFT JOIN categorias ca ON ca.id = p.id_categoria 
						LEFT JOIN categorias cap ON cap.id = ca.id_padre 
						LEFT JOIN clientes c ON v.id_cliente = c.id 
						WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ?  AND v.anulado = 0 AND v.contado != 'Contado' AND v.estado = 'APROBADO' GROUP BY v.id_venta  
				ORDER BY `vendedor` DESC"
			);
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function CompraVentaPorProducto($desde, $hasta)
	{
		try {
			// if (!isset($_SESSION)) session_start();
			$stm = $this->pdo
				->prepare("SELECT 
									vent.producto, vent.codigo, comp.total_compra, comp.cantidad_compra, vent.total_venta, vent.cantidad_venta, vent.porcentaje_ganancia
								FROM 
									(SELECT p.id, p.producto, p.codigo, SUM(v.cantidad) as cantidad_venta, SUM(v.total) as total_venta,
                                     	( ( ( SUM(v.precio_venta) - SUM(v.precio_costo) ) / SUM(v.precio_venta) * 100) ) as porcentaje_ganancia
										FROM ventas v
										LEFT JOIN productos p ON v.id_producto = p.id
										WHERE CAST(v.fecha_venta AS date) >= ?
										AND CAST(v.fecha_venta AS date) <= ?  
										AND v.anulado = 0 
										GROUP BY v.id_producto ) vent
									LEFT JOIN 
									(SELECT 
											p.id, NULL AS producto, SUM(v.cantidad) as cantidad_compra, 
											SUM(v.total) as total_compra 
										FROM compras v 
											LEFT JOIN productos p ON v.id_producto = p.id 
											LEFT JOIN clientes c ON v.id_cliente = c.id 
										WHERE CAST(v.fecha_compra AS date) >= ?
										AND CAST(v.fecha_compra AS date) <= ?
										AND v.anulado = 0 GROUP BY v.id_producto ORDER BY v.id_compra DESC) comp 
									ON vent.id = comp.id
								UNION
								SELECT 	
									comp.producto, comp.codigo, comp.total_compra, comp.cantidad_compra, vent.total_venta, vent.cantidad_venta, vent.porcentaje_ganancia
									FROM 
									(SELECT p.id, NULL AS producto, SUM(v.cantidad) as cantidad_venta, 
									SUM(v.total) as total_venta, 
                                     	( ( ( SUM(v.precio_venta) - SUM(v.precio_costo) ) / SUM(v.precio_venta) * 100) ) as porcentaje_ganancia
										FROM ventas v
										LEFT JOIN productos p ON v.id_producto = p.id
										WHERE CAST(v.fecha_venta AS date) >= ? 
										AND CAST(v.fecha_venta AS date) <= ?  
										AND v.anulado = 0 
										GROUP BY v.id_producto ) vent
									RIGHT JOIN 
									(SELECT 
											p.id, p.producto, p.codigo, SUM(v.cantidad) as cantidad_compra, 
											SUM(v.total) as total_compra 
										FROM compras v 
											LEFT JOIN productos p ON v.id_producto = p.id 
											LEFT JOIN clientes c ON v.id_cliente = c.id 
										WHERE CAST(v.fecha_compra AS date) >= ? 
										AND CAST(v.fecha_compra AS date) <= ? 
										AND v.anulado = 0 GROUP BY v.id_producto ORDER BY v.id_compra DESC) comp 
									ON vent.id = comp.id  ORDER BY 1;
			");
			$stm->execute(array($desde, $hasta, $desde, $hasta, $desde, $hasta, $desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function UltimoAutoimpresor()
	{
		try {

			$stm = $this->pdo->prepare("SELECT MAX(autoimpresor) AS autoimpresor FROM ventas");
			$stm->execute();

			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function AgrupadoProductoVenta($desde, $hasta)
	{
		try {
			$result = array();
			$fecha = date('Y-m-d');

			if ($desde != '') {
				if ($hasta != '') {
					$rango = " AND CAST(v.fecha_venta as date) >= '$desde' AND CAST(v.fecha_venta as date) <= '$hasta'";
				} else {
					$rango = " AND CAST(v.fecha_venta as date) >= '$desde' AND CAST(v.fecha_venta as date) <= '$fecha'";
				}
			}

			$stm = $this->pdo->prepare("SELECT  v.id, v.id_venta, v.id_vendedor, v.comprobante, v.metodo, v.anulado, contado, p.producto, c.ruc, p.codigo, SUM(v.cantidad) AS cantidad, v.precio_venta,
											SUM(subtotal) as subtotal, descuento, 
											SUM(total) as total, 
											SUM(v.precio_costo*v.cantidad) as costo,
											AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, 
											c.nombre as nombre_cli,  v.id_producto, 
											(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
											(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
											FROM ventas v   
											LEFT JOIN productos p ON v.id_producto = p.id 
											LEFT JOIN clientes c ON v.id_cliente = c.id 
											WHERE  v.anulado = 0 $rango -- AND v.contado='Contado'
											GROUP BY v.id_producto ");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function AgrupadoProducto($desde, $hasta)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.fecha_venta, p.producto, SUM(v.cantidad) as cantidad, SUM(v.total) as total, SUM(v.cantidad*v.precio_costo) as costo, v.id_cliente, c.nombre, cap.categoria as categoria, ca.categoria as sub_categoria FROM ventas v
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN categorias ca ON ca.id = p.id_categoria 
			LEFT JOIN categorias cap ON cap.id = ca.id_padre 
			LEFT JOIN clientes c ON v.id_cliente = c.id WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ?  AND v.anulado = 0 AND v.contado = 'Contado' GROUP BY v.id_producto ORDER BY categoria, sub_categoria, total DESC");
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ClientesVentas($desde, $hasta, $asc = 'ASC')
	{
		try {
			$asc = ($asc == 'ASC') ? 'ASC' : 'DESC';


			$stm = $this->pdo
				->prepare("SELECT 
								v.id_cliente, c.nombre as nombre_cliente, c.ruc,
								c.direccion, c.telefono,
                                SUM(v.total) as total,
								IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta AND a.anulado=0), 0) AS costo,
                                SUM((v.total - v.precio_costo * v.cantidad)) AS utilidad,
								(SUM(v.total) - IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta AND a.anulado=0), 0)) AS utilidad,
                               ROUND(
									(
										(SUM(v.total) - IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta AND a.anulado=0), 0))
										/ NULLIF(SUM(v.total), 0)
									) * 100, 2
									) AS margen_ganancia
							FROM ventas v
								LEFT JOIN clientes c
									ON v.id_cliente = c.id
							WHERE 
								CAST(v.fecha_venta AS date) >= ? 
								AND CAST(v.fecha_venta AS date) <= ? 
								AND v.anulado = 0 
							
							GROUP BY v.id_cliente ORDER BY total $asc;");
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarProducto($id_producto, $desde, $hasta)
	{
		try {

			$rango = ($desde == 0) ? "" : "AND fecha_venta >= '$desde' AND fecha_venta <= '$hasta'";
			$stm = $this->pdo->prepare("	SELECT v.id_venta, v.id, p.producto,v.comprobante, 
													v.metodo, v.anulado, contado, p.codigo,p.iva, SUM(v.cantidad) AS cantidad, 
													AVG(v.precio_costo) AS precio_costo, 
													AVG(v.precio_venta) AS precio_venta, subtotal, descuento, SUM(total) AS total, margen_ganancia, 
													DATE_FORMAT(v.fecha_venta, '%d/%m/%y %H:%i') AS fecha_venta, nro_comprobante, c.nombre as nombre_cli, 
													c.ruc, c.direccion, c.telefono, v.id_producto, 
													(SELECT user FROM usuario u WHERE u.id = v.id_vendedor) as vendedor, 
													(SELECT user FROM usuario u WHERE u.id = v.id_vendedor) as vendedor_caja,
													v.id_presupuesto AS presupuestario 
													FROM ventas v 
													LEFT JOIN productos p ON v.id_producto = p.id 
													LEFT JOIN clientes c ON v.id_cliente = c.id 
													WHERE v.id_producto = ? $rango AND v.anulado = 0 
													GROUP BY v.id_venta");
			$stm->execute(array($id_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarFiltros($desde, $hasta)
	{
		try {


			$stm = $this->pdo->prepare("SELECT 
			IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta), 0) AS costo,
				(SUM(v.total) - IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta), 0)) AS ganancia,
			(SUM(v.total) - IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta), 0)) AS ganancia, v.id, v.id_venta AS id_venta, v.comprobante, v.metodo, v.anulado, v.pagare, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, v.nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, 
			(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor,
			(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
			FROM ventas v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN clientes c ON v.id_cliente = c.id 
			LEFT JOIN timbrados t ON t.id = v.id_timbrado
			WHERE CAST(v.fecha_venta AS date) >= '$desde' AND CAST(v.fecha_venta AS date) <= '$hasta'
			GROUP BY v.id_venta ORDER BY v.id_venta DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarFiltrosAprobar($desde, $hasta)
	{
		try {


			$stm = $this->pdo->prepare("SELECT SUM(v.precio_costo*v.cantidad) AS costo, (SUM(v.total) - SUM(v.precio_costo*v.cantidad)) AS ganancia, v.id, v.id_venta AS id_venta, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, v.estado,
			(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor,
			(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
			FROM ventas v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN clientes c ON v.id_cliente = c.id 
			WHERE CAST(v.fecha_venta AS date) >= '$desde' AND CAST(v.fecha_venta AS date) <= '$hasta'
			AND contado='Credito' AND v.estado='PENDIENTE'
			GROUP BY v.id_venta ORDER BY v.id_venta DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarFiltrosAprobado($desde, $hasta)
	{
		try {


			$stm = $this->pdo->prepare("SELECT SUM(v.precio_costo*v.cantidad) AS costo, (SUM(v.total) - SUM(v.precio_costo*v.cantidad)) AS ganancia, v.id, v.id_venta AS id_venta, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, v.estado,
			(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor,
			(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
			FROM ventas v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN clientes c ON v.id_cliente = c.id 
			WHERE CAST(v.fecha_venta AS date) >= '$desde' AND CAST(v.fecha_venta AS date) <= '$hasta'
			AND contado='Credito' AND v.estado='APROBADO'
			GROUP BY v.id_venta ORDER BY v.id_venta DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarProductoCat($id_cat, $desde, $hasta)
	{
		try {

			$rango = ($desde == 0) ? "" : "AND fecha_venta >= '$desde' AND fecha_venta <= '$hasta'";
			$stm = $this->pdo->prepare("SELECT cat.id AS padre, sub.id AS hijo, cat.categoria AS categoria, sub.categoria AS sub_categoria, v.id, p.producto,v.comprobante, v.metodo, v.anulado, contado, p.codigo,p.iva, SUM(v.cantidad) AS cantidad, AVG(v.precio_costo) AS precio_costo, AVG(v.precio_venta) AS precio_venta, subtotal, descuento, SUM(total) AS total, margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, (SELECT user FROM usuario u WHERE u.id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id LEFT JOIN categorias sub ON sub.id = p.id_categoria LEFT JOIN categorias cat ON cat.id = sub.id_padre WHERE (cat.id = 4 OR sub.id = 4) AND v.anulado = 0 GROUP BY p.id");
			$stm->execute(array($id_cat, $id_cat));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarCliente($id_cliente)
	{
		try {


			$stm = $this->pdo->prepare("SELECT v.id, v.id_venta, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE id_cliente = ? GROUP BY v.id_venta ORDER BY v.id_venta DESC");
			$stm->execute(array($id_cliente));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarUsuarioMes($id_usuario, $mes)
	{
		try {

			$fecha = $mes . "-10";
			$stm = $this->pdo->prepare("SELECT v.id, v.id_venta, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon, (SELECT comision FROM usuario WHERE id = v.id_vendedor) as comision  FROM ventas v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE vendedor_salon = ? AND MONTH(fecha_venta) = MONTH(?) AND YEAR(fecha_venta) = YEAR(?) AND v.anulado = '0' GROUP BY v.id_venta ORDER BY v.id_venta DESC");
			$stm->execute(array($id_usuario, $fecha, $fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDia($fecha)
	{
		try {
			if (!isset($_SESSION['user_id'])) {
				if (!isset($_SESSION)) session_start();
			}
			$id_vendedor = $_SESSION['user_id'];
			$usuario = ($_SESSION['nivel'] == 1) ? "" : "AND v.id_vendedor = $id_vendedor";
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE CAST(v.fecha_venta AS date) = ? $usuario  GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDiaSesion()
	{
		try {
			if (!isset($_SESSION['user_id'])) {
				if (!isset($_SESSION)) session_start();
			}
			$id_vendedor = $_SESSION['user_id'];
			$usuario = ($_SESSION['nivel'] == 1) ? "" : "AND v.id_vendedor = $id_vendedor";
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE fecha_venta >= (SELECT fecha_apertura FROM cierres WHERE id_usuario = ? AND fecha_cierre IS NULL) $usuario  GROUP BY v.id_venta DESC");
			$stm->execute(array($id_vendedor));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDiaSinAnular($fecha)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
			FROM ventas v 
			LEFT JOIN productos c ON v.id_producto = c.id 
			LEFT JOIN clientes a ON v.id_cliente = a.id 
			WHERE CAST(v.fecha_venta AS date) = ? AND v.anulado <> 1  
			GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarRango($desde, $hasta)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.id_cliente, v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ? AND v.anulado <> 1  GROUP BY v.id_venta DESC");
			$stm->execute(array($desde, $hasta));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarUsados($desde, $hasta)
	{
		try {

			$stm = $this->pdo->prepare("SELECT c.producto, v.fecha_venta, v.precio_costo, v.precio_venta, v.cantidad, (v.precio_venta*v.cantidad) as total FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ? AND v.anulado <> 1 AND v.id_cliente = 14 ORDER BY v.id DESC");
			$stm->execute(array($desde, $hasta));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarRangoSinAnular($desde, $hasta, $id_vendedor)
	{
		try {
			// Mejorar el logging de la consulta
			error_log("ListarRangoSinAnular ejecutándose con parámetros:");
			error_log("- desde: '$desde'");
			error_log("- hasta: '$hasta'");
			error_log("- id_vendedor: '$id_vendedor'");

			// Usar la comparación datetime directa ya que los parámetros son datetime completos
			$sql = "SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, 
					SUM(v.subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, 
					SUM(v.total) as total, AVG(v.margen_ganancia) as margen_ganancia, v.fecha_venta, 
					nro_comprobante, v.id_producto, 
					(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
					(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
					FROM ventas v 
					LEFT JOIN productos c ON v.id_producto = c.id 
					LEFT JOIN clientes a ON v.id_cliente = a.id 
					WHERE fecha_venta >= ? AND fecha_venta <= ? AND v.anulado <> 1 AND id_vendedor = ? 
					GROUP BY v.id_venta 
					ORDER BY v.id_venta DESC";

			error_log("SQL Query: " . $sql);

			$stm = $this->pdo->prepare($sql);
			$stm->execute(array($desde, $hasta, $id_vendedor));
			$result = $stm->fetchAll(PDO::FETCH_OBJ);

			error_log("Resultado de ListarRangoSinAnular: " . count($result) . " registros");

			// Si no hay resultados, hacer una consulta de diagnóstico
			if (empty($result)) {
				error_log("Sin resultados - ejecutando consulta de diagnóstico...");

				// Consulta 1: Verificar si hay ventas del vendedor sin filtro de fecha
				$sql_diag1 = "SELECT COUNT(*) as total FROM ventas WHERE id_vendedor = ? AND anulado <> 1";
				$stm_diag1 = $this->pdo->prepare($sql_diag1);
				$stm_diag1->execute(array($id_vendedor));
				$count_vendedor = $stm_diag1->fetch(PDO::FETCH_OBJ);
				error_log("Total ventas del vendedor $id_vendedor: " . $count_vendedor->total);

				// Consulta 2: Verificar ventas en el rango sin filtro de vendedor
				$sql_diag2 = "SELECT COUNT(*) as total FROM ventas WHERE fecha_venta >= ? AND fecha_venta <= ? AND anulado <> 1";
				$stm_diag2 = $this->pdo->prepare($sql_diag2);
				$stm_diag2->execute(array($desde, $hasta));
				$count_rango = $stm_diag2->fetch(PDO::FETCH_OBJ);
				error_log("Total ventas en el rango de fechas: " . $count_rango->total);

				// Consulta 3: Ver qué vendedores tienen ventas en el rango
				$sql_diag3 = "SELECT id_vendedor, COUNT(*) as total FROM ventas WHERE fecha_venta >= ? AND fecha_venta <= ? AND anulado <> 1 GROUP BY id_vendedor ORDER BY total DESC LIMIT 5";
				$stm_diag3 = $this->pdo->prepare($sql_diag3);
				$stm_diag3->execute(array($desde, $hasta));
				$vendedores = $stm_diag3->fetchAll(PDO::FETCH_OBJ);
				error_log("Vendedores con ventas en el rango:");
				foreach ($vendedores as $v) {
					error_log("- Vendedor {$v->id_vendedor}: {$v->total} ventas");
				}
			}

			return $result;
		} catch (Exception $e) {
			error_log("Error en ListarRangoSinAnular: " . $e->getMessage());
			die($e->getMessage());
		}
	}

	// Método específico para PDF de cierre que incluye el monto realmente cobrado
	public function ListarRangoSinAnularConCobrado($desde, $hasta, $id_vendedor)
	{
		try {
			error_log("ListarRangoSinAnularConCobrado ejecutándose con parámetros:");
			error_log("- desde: '$desde'");
			error_log("- hasta: '$hasta'");
			error_log("- id_vendedor: '$id_vendedor'");

			// Consulta que incluye el monto realmente cobrado desde la tabla ingresos
			$sql = "SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, 
					SUM(v.subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, 
					SUM(v.total) as total, AVG(v.margen_ganancia) as margen_ganancia, v.fecha_venta, 
					v.nro_comprobante, v.id_producto, 
					(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
					(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon,
					COALESCE(
						(SELECT SUM(i.monto * i.cambio) -- Monto cobrado en la moneda del ingreso
						 FROM ingresos i 
						 WHERE i.id_venta = v.id_venta 
						   AND i.anulado IS NULL), 
						0
					) as cobrado
					FROM ventas v 
					LEFT JOIN productos c ON v.id_producto = c.id 
					LEFT JOIN clientes a ON v.id_cliente = a.id 
					WHERE v.fecha_venta >= ? AND v.fecha_venta <= ? 
					  AND v.anulado <> 1 AND v.id_vendedor = ? 
					GROUP BY v.id_venta 
					ORDER BY v.id_venta DESC";

			error_log("SQL Query con cobrado: " . $sql);

			$stm = $this->pdo->prepare($sql);
			$stm->execute(array($desde, $hasta, $id_vendedor));
			$result = $stm->fetchAll(PDO::FETCH_OBJ);

			error_log("Resultado de ListarRangoSinAnularConCobrado: " . count($result) . " registros");

			// Log adicional para verificar los montos cobrados
			foreach ($result as $venta) {
				error_log("Venta {$venta->id_venta}: Total={$venta->total}, Cobrado={$venta->cobrado}, Contado={$venta->contado}");
			}

			return $result;
		} catch (Exception $e) {
			error_log("Error en ListarRangoSinAnularConCobrado: " . $e->getMessage());
			die($e->getMessage());
		}
	}

	public function ListarMesSinAnular($fecha)
	{
		try {
			$stm = $this->pdo->prepare("SELECT v.id_cliente, v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE MONTH(v.fecha_venta) = MONTH(?) AND YEAR(v.fecha_venta) = YEAR(?) AND v.anulado <> 1  GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDiaContado($fecha)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, cli.Nombre as nombre_cli, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes cli ON v.id_cliente = cli.id WHERE CAST(v.fecha_venta AS date) = ? AND contado = 'contado' GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMes($fecha)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.id_venta, cli.Nombre AS nombre_cli, v.metodo, c.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as ganancia, fecha_venta, nro_comprobante FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes cli ON cli.id = v.id_cliente  WHERE MONTH(v.fecha_venta) = MONTH(?) AND YEAR(v.fecha_venta) = YEAR(?) GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function UsuariosPresupuesto($desde, $hasta, $desc = 'DESC')
	{
		try {
			$desc = ($desc == 'DESC') ? 'DESC' : 'ASC';


			$stm = $this->pdo
				->prepare("		SELECT 
									IF(v.id_presupuesto > 0,
                                        (SELECT user FROM usuario u WHERE u.id = 
                                        (SELECT p.id_vendedor FROM presupuestos p WHERE v.id_presupuesto = p.id_presupuesto LIMIT 1)),
                                        (SELECT user FROM usuario u WHERE u.id = id_vendedor)) AS user,
                                        IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta AND a.anulado=0), 0) AS costo,
                                        ((SUM(total) - IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta AND a.anulado=0), 0)) / SUM(total) *100 ) AS margen_ganancia,
										SUM(cantidad) AS items_vendidos,
										-- SUM(v.precio_venta-v.precio_costo) AS margen_ganancia,
										(SUM(v.total) - IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta AND a.anulado=0), 0)) AS utilidad,
										SUM(v.total) AS total,
										(SELECT SUM(total) FROM devoluciones_ventas d WHERE d.id_venta = v.id_venta GROUP BY d.id_venta) AS devolucion,
										(SELECT SUM(precio_costo) FROM devoluciones_ventas dc WHERE dc.id_venta = v.id_venta GROUP BY dc.id_venta) AS devolucion_costo
									FROM ventas v 
									WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ? AND v.anulado = 0
									GROUP BY user
									ORDER BY total $desc
								;");
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Detalles($id_venta)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT c.producto, subtotal, descuento, total, margen_ganancia, fecha_venta, nro_comprobante FROM ventas v JOIN productos c ON v.id_producto = c.id WHERE v.id_venta = ?");
			$stm->execute(array($id_venta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Obtener($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM ventas WHERE id = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerVenta($id_venta)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT v.*, c.nombre AS cliente, c.ruc, c.direccion, c.telefono, SUM(precio_venta*cantidad) AS total
				 FROM ventas v 
				 LEFT JOIN clientes c ON v.id_cliente = c.id
				 WHERE v.id_venta = ? LIMIT 1");


			$stm->execute(array($id_venta));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerProducto($id_venta, $id_producto)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM ventas WHERE id_venta = ? AND id_producto = ?");


			$stm->execute(array($id_venta, $id_producto));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerUNO($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM ventas WHERE id_venta = ? LIMIT 1");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Recibo($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM ventas WHERE id_venta = ?");


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
				->prepare("SELECT MAX(id_venta) as id_venta FROM ventas");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function UltimoComprobante()
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT MAX(nro_comprobante) as nro FROM ventas");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Cantidad($id_item, $id_venta, $cantidad)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE ventas SET cantidad = ?, subtotal = precio_venta * ?, total = precio_venta * ? WHERE id = ?");
			$stm->execute(array($cantidad, $cantidad, $cantidad, $id_item));
			$stm = $this->pdo
				->prepare("SELECT *, (SELECT SUM(total) FROM ventas WHERE id_venta = ? GROUP BY id_venta) as total_venta FROM ventas WHERE id = ?");
			$stm->execute(array($id_venta, $id_item));

			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function CancelarItem($id_item)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM ventas WHERE id = ?");

			$stm->execute(array($id_item));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Eliminar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM ventas WHERE id_venta = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function aprobarVenta($id)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE ventas SET estado='APROBADO' WHERE id_venta = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}



	public function Anular($id)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE ventas SET anulado = 1 WHERE id_venta = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Editar($data)
	{
		try {
			$sql = "UPDATE ventas SET
						
                        id_cliente        = ?, 
						comprobante       = ?,
						nro_comprobante   = ?,
						pagare            = ?
						
				    WHERE id_venta = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(

						$data->id_cliente,
						$data->comprobante,
						$data->nro_comprobante,
						$data->pagare,
						$data->id_venta
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE ventas SET
						id_venta        = ?,
						id_vendedor     = ?,
						id_producto     = ?,
						precio_venta    = ?,
                        cantidad        = ?, 
						margen_ganancia = ?,
						fecha_venta     = ?,
						id_gift         = ?,
						id_gift         = ?,
						id_presupuesto  = ?,
						pagare          = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_venta,
						$data->id_vendedor,
						$data->id_producto,
						$data->precio_venta,
						$data->cantidad,
						$data->margen_ganancia,
						$data->fecha_venta,
						$data->id_gift,
						$data->id_gift,
						$data->id_presupuesto,
						$data->pagare,
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
			$sql = "INSERT INTO ventas (id_venta, id_cliente, id_vendedor, id_presupuesto, vendedor_salon, id_producto, precio_costo, precio_venta, subtotal, descuento, iva, total, comprobante, nro_comprobante, id_timbrado, autoimpresor, cantidad, margen_ganancia, fecha_venta, metodo, contado, banco, id_devolucion, id_gift, estado, cot_usd, cot_rs, moneda, paciente, pagare) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_venta,
						$data->id_cliente,
						$data->id_vendedor,
						$data->id_presupuesto,
						$data->vendedor_salon,
						$data->id_producto,
						$data->precio_costo,
						$data->precio_venta,
						$data->subtotal,
						$data->descuento,
						$data->iva,
						$data->total,
						$data->comprobante,
						$data->nro_comprobante,
						$data->id_timbrado,
						$data->autoimpresor,
						$data->cantidad,
						$data->margen_ganancia,
						$data->fecha_venta,
						$data->metodo,
						$data->contado,
						$data->banco,
						$data->id_devolucion,
						$data->id_gift,
						$data->estado,
						$data->cot_usd,
						$data->cot_rs,
						$data->moneda,
						$data->paciente,
						$data->pagare
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarRangoSinAnularContado($desde, $hasta, $id_usuario)
	{
		try {
			if ($desde != '') {
				if ($hasta != '') {
					$rango = " AND v.fecha_venta >= '$desde' AND v.fecha_venta  <= '$hasta'";
				} else {
					$rango = " AND v.fecha_venta  >= '$desde' AND v.fecha_venta  <= '$hasta'";
				}
			}
			if ($id_usuario != '') {
				$user = " AND v.id_vendedor = '$id_usuario'";
			} else {
				$user = " ";
			}


			$stm = $this->pdo->prepare("SELECT
			 v.metodo, 
			 v.contado,
			v.id_venta,
			a.nombre as nombre_cli, 
			v.anulado, 
			c.producto, 
			SUM(v.subtotal) as subtotal,
			SUM(v.descuento*v.cantidad) AS descuento, 
			SUM(v.precio_costo * v.cantidad) as costo,
			SUM((v.precio_venta*v.cantidad)-(v.descuento*v.cantidad)) as total, 
			AVG(v.margen_ganancia) as margen_ganancia, 
			v.fecha_venta, 
			v.nro_comprobante,
			 v.id_producto, 
			 v.cantidad,
			 v.precio_venta,
			(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
			(SELECT i.forma_pago FROM ingresos i WHERE i.id_venta = v.id_venta AND i.forma_pago='Efectivo' LIMIT 1) AS efectivo,
            (SELECT i.forma_pago FROM ingresos i WHERE i.id_venta = v.id_venta AND i.forma_pago<>'Efectivo' LIMIT 1) AS otros,
            (SELECT user FROM usuario WHERE id = (SELECT p.id_vendedor FROM presupuestos p WHERE v.id_presupuesto=p.id_presupuesto LIMIT 1) )as vendedor_salon
			FROM ventas v 
			LEFT JOIN productos c ON v.id_producto = c.id 
			LEFT JOIN clientes a ON v.id_cliente = a.id 
			WHERE v.contado='Contado'  AND v.anulado = 0 $rango $user  GROUP BY v.id_venta DESC");
			$stm->execute(array());
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarRangoSinAnularCredito($desde, $hasta, $id_usuario)
	{
		try {
			if ($desde != '') {
				if ($hasta != '') {
					$rango = " AND v.fecha_venta >= '$desde' AND v.fecha_venta  <= '$hasta'";
				} else {
					$rango = " AND v.fecha_venta  >= '$desde' AND v.fecha_venta  <= '$hasta'";
				}
			}
			if ($id_usuario != '') {
				$user = " AND v.id_vendedor = '$id_usuario'";
			} else {
				$user = " ";
			}

			$stm = $this->pdo->prepare("SELECT
			 v.metodo, 
			 v.contado,
			v.id_venta,
			a.nombre as nombre_cli, 
			v.anulado, 
			c.producto, 
			SUM(v.subtotal) as subtotal,
			SUM(v.descuento*v.cantidad) AS descuento, 
			SUM(v.precio_costo * v.cantidad) as costo,
			SUM((v.precio_venta*v.cantidad)-(v.descuento*v.cantidad)) as total, 
			AVG(v.margen_ganancia) as margen_ganancia, 
			v.fecha_venta, 
			v.nro_comprobante,
			 v.id_producto, 
			 v.cantidad,
			 v.precio_venta,
			(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
			(SELECT i.forma_pago FROM ingresos i WHERE i.id_venta = v.id_venta AND i.forma_pago='Efectivo' LIMIT 1) AS efectivo,
            (SELECT i.forma_pago FROM ingresos i WHERE i.id_venta = v.id_venta AND i.forma_pago<>'Efectivo' LIMIT 1) AS otros,
            (SELECT user FROM usuario WHERE id = (SELECT p.id_vendedor FROM presupuestos p WHERE v.id_presupuesto=p.id_presupuesto LIMIT 1) )as vendedor_salon
			FROM ventas v 
			LEFT JOIN productos c ON v.id_producto = c.id 
			LEFT JOIN clientes a ON v.id_cliente = a.id 
			WHERE v.contado!='Contado' AND v.anulado = 0 $rango $user  GROUP BY v.id_venta DESC");
			$stm->execute(array());
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ResumenVentasPorRango($desde, $hasta)
	{
		try {
			$stm = $this->pdo->prepare("SELECT 
					COALESCE(SUM(CASE WHEN v.contado = 'Contado' THEN (v.precio_venta * v.cantidad) * (1 - v.descuento/100) ELSE 0 END), 0) as ventas_contado,
					COALESCE(SUM(CASE WHEN v.contado != 'Contado' THEN (v.precio_venta * v.cantidad) * (1 - v.descuento/100) ELSE 0 END), 0) as ventas_credito,
					COALESCE(SUM((v.precio_venta * v.cantidad) * (1 - v.descuento/100)), 0) as total_ventas,
					IFNULL((SELECT SUM(a.total) FROM devoluciones a WHERE a.venta = v.id_venta AND a.anulado=0), 0) AS costo_productos
				FROM ventas v 
				WHERE 
					CAST(v.fecha_venta AS date) >= ? 
					AND CAST(v.fecha_venta AS date) <= ? 
					AND v.anulado = 0
			");
			$stm->execute(array($desde, $hasta));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	// Métodos para CONTROL DE CAJA

	/**
	 * Obtiene el total de ventas realmente cobradas (contado + crédito cobrado)
	 * Solo considera los cobros realizados dentro del rango de fechas
	 */
	public function VentasCobradasPorRango($desde, $hasta)
	{
		try {
			$stm = $this->pdo->prepare("SELECT 
					COALESCE(SUM(CASE 
						WHEN i.moneda = 'USD' THEN i.monto * COALESCE(i.cambio, 7500)
						WHEN i.moneda = 'RS' THEN i.monto * COALESCE(i.cambio, 1500)
						ELSE i.monto 
					END), 0) as total_cobrado
				FROM ingresos i
				WHERE 
					CAST(i.fecha AS date) >= ? 
					AND CAST(i.fecha AS date) <= ? 
					AND i.anulado IS NULL
					AND i.categoria = 'Venta'
			");
			$stm->execute(array($desde, $hasta));
			$result = $stm->fetch(PDO::FETCH_OBJ);
			return $result ? $result->total_cobrado : 0;
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/**
	 * Obtiene el total de cobros de deudas (sin incluir ventas nuevas)
	 */
	public function CobrosDeudasPorRango($desde, $hasta)
	{
		try {
			$stm = $this->pdo->prepare("SELECT 
					COALESCE(SUM(CASE 
						WHEN i.moneda = 'USD' THEN i.monto * COALESCE(i.cambio, 7500)
						WHEN i.moneda = 'RS' THEN i.monto * COALESCE(i.cambio, 1500)
						ELSE i.monto 
					END), 0) as total_cobros_deudas
				FROM ingresos i
				WHERE 
					CAST(i.fecha AS date) >= ? 
					AND CAST(i.fecha AS date) <= ? 
					AND i.anulado IS NULL
					AND i.categoria = 'Cobro de deuda'
					AND i.id_deuda IS NOT NULL
			");
			$stm->execute(array($desde, $hasta));
			$result = $stm->fetch(PDO::FETCH_OBJ);
			return $result ? $result->total_cobros_deudas : 0;
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/**
	 * Obtiene el total de pagos de gastos efectivamente realizados
	 */
	public function PagosGastosPorRango($desde, $hasta)
	{
		try {
			$stm = $this->pdo->prepare("SELECT 
					COALESCE(SUM(CASE 
						WHEN e.moneda = 'USD' THEN e.monto * COALESCE(e.cambio, 7500)
						WHEN e.moneda = 'RS' THEN e.monto * COALESCE(e.cambio, 1500)
						ELSE e.monto 
					END), 0) as total_pagos_gastos
				FROM egresos e
				WHERE 
					CAST(e.fecha AS date) >= ? 
					AND CAST(e.fecha AS date) <= ? 
					AND e.anulado IS NULL
					AND e.categoria != 'Transferencia'
					AND e.categoria != 'compra'
					AND e.categoria != 'COMPRA DE MERCADERIAS'
					AND e.id_compra IS NULL
					AND e.id_acreedor IS NULL
			");
			$stm->execute(array($desde, $hasta));
			$result = $stm->fetch(PDO::FETCH_OBJ);
			return $result ? $result->total_pagos_gastos : 0;
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/**
	 * Obtiene el total de pagos a proveedores efectivamente realizados
	 */
	public function PagosProveedoresPorRango($desde, $hasta)
	{
		try {
			$stm = $this->pdo->prepare("SELECT 
					COALESCE(SUM(CASE 
						WHEN e.moneda = 'USD' THEN e.monto * COALESCE(e.cambio, 7500)
						WHEN e.moneda = 'RS' THEN e.monto * COALESCE(e.cambio, 1500)
						ELSE e.monto 
					END), 0) as total_pagos_proveedores
				FROM egresos e
				WHERE 
					CAST(e.fecha AS date) >= ? 
					AND CAST(e.fecha AS date) <= ? 
					AND e.anulado IS NULL
					AND e.id_acreedor IS NOT NULL
			");
			$stm->execute(array($desde, $hasta));
			$result = $stm->fetch(PDO::FETCH_OBJ);
			return $result ? $result->total_pagos_proveedores : 0;
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/**
	 * Obtiene un resumen completo del flujo de caja
	 * Solo considera los 4 conceptos principales del control de caja
	 */
	public function FlujoCajaPorRango($desde, $hasta)
	{
		try {
			$ventas_cobradas = $this->VentasCobradasPorRango($desde, $hasta);
			$cobros_deudas = $this->CobrosDeudasPorRango($desde, $hasta);
			$pagos_gastos = $this->PagosGastosPorRango($desde, $hasta);
			$pagos_proveedores = $this->PagosProveedoresPorRango($desde, $hasta);

			$total_ingresos = $ventas_cobradas + $cobros_deudas;
			$total_egresos = $pagos_gastos + $pagos_proveedores;
			$flujo_neto = $total_ingresos - $total_egresos;

			return (object) [
				'ventas_cobradas' => $ventas_cobradas,
				'cobros_deudas' => $cobros_deudas,
				'total_ingresos' => $total_ingresos,
				'pagos_gastos' => $pagos_gastos,
				'pagos_proveedores' => $pagos_proveedores,
				'total_egresos' => $total_egresos,
				'flujo_neto' => $flujo_neto
			];
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function InformeGeneral($desde, $hasta)
	{
		try {
			// Ingresos (incluye ventas al contado)
			$stmIngresos = $this->pdo->prepare("SELECT
				i.id_venta AS id, 
                'Ingreso' AS tipo,
                i.fecha AS fecha,
                i.concepto,
                i.categoria,
				i.monto,
                i.monto * i.cambio AS monto_guaranies,
                i.moneda,
                i.forma_pago,
                c.nombre AS cliente
            FROM ingresos i
            LEFT JOIN clientes c ON i.id_cliente = c.id
            WHERE i.anulado IS NULL
                AND i.categoria <> 'Transferencia'
                AND CAST(i.fecha AS date) >= ?
                AND CAST(i.fecha AS date) <= ?
        ");
			$stmIngresos->execute([$desde, $hasta]);
			$ingresos = $stmIngresos->fetchAll(PDO::FETCH_OBJ);

			// Egresos (excluye compras y transferencias)
			$stmEgresos = $this->pdo->prepare("SELECT 
				e.id_compra AS id,
                'Egreso' AS tipo,
                e.fecha AS fecha,
                e.concepto,
                e.categoria,
				e.monto,
                e.monto * e.cambio AS monto_guaranies,
                e.moneda,
                e.forma_pago,
                c.nombre AS cliente
            FROM egresos e
            LEFT JOIN clientes c ON e.id_cliente = c.id
            WHERE e.anulado IS NULL
                AND e.categoria <> 'Transferencia'
                -- AND e.categoria <> 'compra'
                -- AND e.categoria <> 'COMPRA DE MERCADERIAS'
                AND CAST(e.fecha AS date) >= ?
                AND CAST(e.fecha AS date) <= ?
        ");
			$stmEgresos->execute([$desde, $hasta]);
			$egresos = $stmEgresos->fetchAll(PDO::FETCH_OBJ);

			// Ventas a crédito no anuladas (solo aprobadas)
			$stmVentasCredito = $this->pdo->prepare("SELECT 
				v.id_venta AS id,
                'Venta Crédito' AS tipo,
                v.fecha_venta AS fecha,
                CONCAT('Venta a crédito: ', c.nombre) AS concepto,
                'Venta a crédito' AS categoria,
				SUM(v.precio_venta * v.cantidad) AS monto,
                SUM(v.total) AS monto_guaranies,
                'GS' AS moneda,
                v.metodo AS forma_pago,
                c.nombre AS cliente
            FROM ventas v
            LEFT JOIN clientes c ON v.id_cliente = c.id
            WHERE v.anulado = 0
                AND v.contado != 'Contado'
                AND v.estado = 'APROBADO'
                AND CAST(v.fecha_venta AS date) >= ?
                AND CAST(v.fecha_venta AS date) <= ?
			GROUP BY v.id_venta
        ");
			$stmVentasCredito->execute([$desde, $hasta]);
			$ventasCredito = $stmVentasCredito->fetchAll(PDO::FETCH_OBJ);

			// Unir todos y ordenar por fecha
			$movimientos = array_merge($ingresos, $egresos, $ventasCredito);
			usort($movimientos, function ($a, $b) {
				return strtotime($a->fecha) <=> strtotime($b->fecha);
			});

			return $movimientos;
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
