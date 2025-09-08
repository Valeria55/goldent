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
	public $prod_factura;
	public $can_producto;
    
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

	public function Listar($id_venta)
	{
		try
		{
			
			if($id_venta==0){
			    
				$stm = $this->pdo->prepare("SELECT v.descuento AS descuentov, SUM(v.precio_costo*v.cantidad) AS costo, (SUM(v.total) - SUM(v.precio_costo*v.cantidad)) AS ganancia, v.id, v.id_venta as id_venta, v.comprobante, v.metodo, v.cot_real, v.cot_dolar, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto,v.can_factura, v.prod_factura, pf.producto AS prodfactura, v.id_presupuesto,
					(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
					(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon, v.id_cliente
					FROM ventas v 
					LEFT JOIN productos p ON v.id_producto = p.id
					LEFT JOIN productos pf ON v.prod_factura = pf.id  
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					GROUP BY v.id_venta ORDER BY v.id_venta DESC");
				$stm->execute();
			}else{
				$stm = $this->pdo->prepare("SELECT  v.descuento AS descuentov, v.precio_costo, v.id_venta AS id_venta, v.cot_real, v.cot_dolar, v.id, p.producto,v.comprobante, v.metodo, v.anulado, contado, p.codigo,p.iva, SUM(v.cantidad) AS cantidad, v.precio_venta, SUM(subtotal) AS subtotal, SUM(descuento) AS descuento, SUM(total) AS total, margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto,v.can_factura, v.prod_factura, pf.producto AS prodfactura, p.fardo,v.id_presupuesto,
					(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, id_gift, v.id_cliente, v.cot_dolar
					FROM ventas v 
					LEFT JOIN productos p ON v.id_producto = p.id 
					LEFT JOIN productos pf ON v.prod_factura = pf.id
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					WHERE v.id_venta = ? 
					GROUP BY v.id_producto");
				$stm->execute(array($id_venta));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarDetalles($id_venta)
	{
		try
		{
			
				$stm = $this->pdo->prepare("SELECT  v.descuento AS descuentov, v.precio_costo, v.id_venta AS id_venta, v.cot_real, v.cot_dolar, v.id, p.producto,v.comprobante, v.metodo, v.anulado, contado, p.codigo,p.iva, SUM(v.cantidad) AS cantidad, v.precio_venta, SUM(subtotal) AS subtotal, SUM(descuento) AS descuento, SUM(total) AS total, margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto,v.can_factura, v.prod_factura, pf.producto AS prodfactura, p.fardo,v.id_presupuesto,
				(SELECT dev.id_venta FROM devoluciones_ventas dev WHERE dev.id_venta = v.id_venta AND v.id_producto = dev.id_producto LIMIT 1) as devolucion,
				(SELECT SUM(dev.cantidad) FROM devoluciones_ventas dev WHERE dev.id_venta = v.id_venta AND v.id_producto = dev.id_producto LIMIT 1) as cantidad_devuelta,
				(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, id_gift, v.id_cliente, v.cot_dolar
				FROM ventas v 
				LEFT JOIN productos p ON v.id_producto = p.id 
				LEFT JOIN productos pf ON v.prod_factura = pf.id
				LEFT JOIN clientes c ON v.id_cliente = c.id 
				WHERE v.id_venta = ?
				GROUP BY v.id_producto");
				$stm->execute(array($id_venta));
			

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarVentaPresupuesto($id_presupuesto)
	{
		try
		{
			session_start();
			// $id_usuario = $_SESSION['user_id'];
			// $nivel = $_SESSION["nivel"];
		
			$result = array();


			$stm = $this->pdo->prepare("SELECT *
			FROM presupuestos
			JOIN ventas ON presupuestos.id_presupuesto = ventas.id_presupuesto
			WHERE presupuestos.id_presupuesto = ?");
			 
			$stm->execute(array($id_presupuesto));

			return $stm->fetch(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

		public function ListarFactura($id_venta)
	{
		try
		{
			
			if($id_venta==0){
			    
				$stm = $this->pdo->prepare("SELECT v.descuento AS descuentov, SUM(v.precio_costo*v.cantidad) AS costo, (SUM(v.total) - SUM(v.precio_costo*v.cantidad)) AS ganancia, v.id, v.id_venta as id_venta, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto,v.can_factura, v.prod_factura, pf.producto AS prodfactura, v.id_presupuesto,
					(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
					(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon, v.id_cliente
					FROM ventas v 
					LEFT JOIN productos p ON v.id_producto = p.id
					LEFT JOIN productos pf ON v.prod_factura = pf.id  
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					GROUP BY v.id_venta ORDER BY v.id_venta DESC");
				$stm->execute();
			}else{
				$stm = $this->pdo->prepare("SELECT  v.descuento AS descuentov, v.precio_costo, v.id_venta AS id_venta , v.id, p.producto,v.comprobante, v.metodo, v.anulado, contado, p.codigo,p.iva, SUM(v.cantidad) AS cantidad, v.precio_venta AS precio_venta, SUM(subtotal) AS subtotal, SUM(descuento) AS descuento, SUM(total) AS total, margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto,v.can_factura, v.prod_factura, pf.producto AS prodfactura, p.fardo,v.id_presupuesto,
				(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, id_gift, v.id_cliente, v.cot_dolar
				FROM ventas v 
				LEFT JOIN productos p ON v.id_producto = p.id 
				LEFT JOIN productos pf ON v.prod_factura = pf.id
				LEFT JOIN clientes c ON v.id_cliente = c.id 
				WHERE v.id_venta = ?
				GROUP BY v.id_producto");
				$stm->execute(array($id_venta));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}


	public function ListarExtracto($id_venta, $desde, $hasta)
	{
		try
		{
			

				$stm = $this->pdo->prepare("SELECT  v.descuento AS descuentov, v.id_venta AS id_venta , v.id, p.producto,v.comprobante, v.metodo, v.anulado, contado, p.codigo,p.iva, SUM(v.cantidad) AS cantidad, v.precio_venta, SUM(subtotal) AS subtotal, SUM(descuento) AS descuento, SUM(total) AS total, margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto,v.can_factura, v.prod_factura, pf.producto AS prodfactura, p.fardo,
				(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, id_gift 
				FROM ventas v 
				LEFT JOIN productos p ON v.id_producto = p.id 
				LEFT JOIN productos pf ON v.prod_factura = pf.id
				LEFT JOIN clientes c ON v.id_cliente = c.id 
				WHERE v.id_venta = ? AND v.contado = 'credito' AND v.anulado = 0 GROUP BY v.id");
				$stm->execute(array($id_venta));
			

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function consultarVenta($id_venta)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT SUM(round(monto/cambio, 2)) AS total FROM ingresos WHERE id_venta = ?");
			$stm->execute(array($id_venta));
			return $stm->fetch(PDO::FETCH_OBJ);
			
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function utilidad($fecha)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT id_venta, fecha_venta, SUM(precio_costo*cantidad) AS costo, (SUM(total) - SUM(precio_costo*cantidad)) AS ganancia, SUM(total) AS total FROM ventas WHERE MONTH(fecha_venta) = MONTH(?) AND YEAR(fecha_venta) = YEAR(?) AND anulado=0  GROUP BY id_venta ORDER BY id_venta DESC");

		$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function AgrupadoProducto($desde, $hasta)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.fecha_venta, p.producto, SUM(v.cantidad) as cantidad, SUM(v.total) as total, SUM(v.cantidad*v.precio_costo) as costo, v.id_cliente, c.nombre, cap.categoria as categoria, ca.categoria as sub_categoria FROM ventas v
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN categorias ca ON ca.id = p.id_categoria 
			LEFT JOIN categorias cap ON cap.id = ca.id_padre 
			LEFT JOIN clientes c ON v.id_cliente = c.id WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ?  AND v.anulado = 0 AND v.contado = 'Contado' GROUP BY v.id_producto ORDER BY categoria, sub_categoria, total DESC");
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
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
											SUM(p.precio_costo*v.cantidad) as costo,
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

	public function AgrupadoVenta($desde, $hasta)
	{
		try {
			$stm = $this->pdo->prepare(
				"SELECT v.fecha_venta,  v.id_venta,
				p.producto, SUM(v.cantidad) as cantidad, SUM(v.total) as total, 
				v.precio_costo as costo, v.id_cliente, c.nombre, 
				cap.categoria as categoria, ca.categoria as sub_categoria,
				v.contado,  
				(SELECT user FROM usuario WHERE id = IF(pres.id_vendedor IS NOT NULL, pres.id_vendedor, v.id_vendedor) ) as vendedor
			FROM ventas v
					LEFT JOIN presupuestos pres ON v.id_presupuesto = pres.id
					LEFT JOIN productos p ON v.id_producto = p.id
					LEFT JOIN categorias ca ON ca.id = p.id_categoria 
					LEFT JOIN categorias cap ON cap.id = ca.id_padre 
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ?  AND v.anulado = 0 AND v.contado = 'Contado' GROUP BY v.id_venta"
			);
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
								SUM(v.precio_costo * v.cantidad) AS costo,
                                SUM((v.total - v.precio_costo * v.cantidad)) AS utilidad,
                                ( ( SUM(v.total) - SUM(v.precio_costo * v.cantidad) ) / SUM(v.total)*100 ) AS margen_ganancia
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

	public function UsuariosPresupuesto($desde, $hasta, $desc = 'DESC')
	{
		try {
			$desc = ($desc == 'DESC') ? 'DESC' : 'ASC';


			$stm = $this->pdo
				->prepare("		SELECT 
										IF(v.id_presupuesto > 0,
                                            (SELECT user FROM usuario u WHERE u.id = 
                                                (SELECT p.id_vendedor FROM presupuestos p WHERE v.id_presupuesto = p.id_presupuesto LIMIT 1)),
                                            (SELECT user FROM usuario u WHERE u.id = id_vendedor)
										) AS user,
                                        SUM(v.precio_costo * v.cantidad) AS costo,
                                        ( (SUM(total) - SUM(v.precio_costo * v.cantidad)) / SUM(total) *100 ) AS margen_ganancia,
										SUM(cantidad) AS items_vendidos,
										-- SUM(v.precio_venta-v.precio_costo) AS margen_ganancia,
										SUM(v.total - (v.precio_costo * v.cantidad)) AS utilidad,
										SUM(v.total) AS total
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
									ON vent.id = comp.id;
			");
			$stm->execute(array($desde, $hasta, $desde, $hasta, $desde, $hasta, $desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	
	public function ListarProducto($id_producto, $desde, $hasta)
	{
		try
		{
			
			$rango = ($desde==0)? "":"AND fecha_venta >= '$desde' AND fecha_venta <= '$hasta'";
			$stm = $this->pdo->prepare("SELECT v.id, 
			p.producto,
			v.comprobante,
			v.metodo, 
			v.anulado, 
			v.contado, 
			p.codigo,
			v.id_presupuesto,
			p.iva,
			SUM(v.cantidad) AS cantidad, 
			AVG(v.precio_costo) AS precio_costo, 
			AVG(v.precio_venta) AS precio_venta,
			v.subtotal,
			v.descuento,
			SUM(v.total) AS total,
			v.margen_ganancia, 
			v.fecha_venta,
			v.nro_comprobante,
			c.nombre as nombre_cli,
			c.ruc, 
			c.direccion,
			c.telefono, 
			v.id_producto,
			(SELECT user FROM usuario u WHERE u.id = (SELECT pr.id_vendedor FROM presupuestos pr WHERE pr.id_presupuesto=v.id_presupuesto LIMIT 1) ) as vendedor,
			(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon 
			FROM ventas v  
			LEFT JOIN productos p ON v.id_producto = p.id 
			LEFT JOIN clientes c ON v.id_cliente = c.id WHERE v.id_producto = ? $rango AND v.anulado = 0 GROUP BY v.id_venta");
			$stm->execute(array($id_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarFiltros($desde, $hasta, $id_cliente)
	{
		try
		{
		    var_dump($id_cliente);
			die();
		    $id_cliente = $id_cliente==0 ? "":"AND v.id_cliente = '$id_cliente'";
		$stm = $this->pdo->prepare("SELECT v.descuento AS descuentov, SUM(v.precio_costo*v.cantidad) AS costo, (SUM(v.total) - SUM(v.precio_costo*v.cantidad)) AS ganancia, v.id, v.id_venta as id_venta, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto,v.can_factura, v.prod_factura, pf.producto AS prodfactura, v.id_presupuesto, v.cot_dolar,v.cot_real,
					(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
					(SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon, v.id_cliente
					FROM ventas v 
					LEFT JOIN productos p ON v.id_producto = p.id
					LEFT JOIN productos pf ON v.prod_factura = pf.id  
					LEFT JOIN clientes c ON v.id_cliente = c.id  
			WHERE CAST(v.fecha_venta AS date) >= '$desde' AND CAST(v.fecha_venta AS date) <= '$hasta' $id_cliente
			GROUP BY v.id_venta ORDER BY v.id_venta DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
		public function ListarPorItem($desde, $hasta)
	{
		try
		{
		    $fecha=date('Y-m-d');
		    $rango=($desde!='')? " AND CAST(v.fecha_venta AS date) >= '$desde' AND CAST(v.fecha_venta AS date) <= '$hasta'": "AND CAST(v.fecha_venta AS date) = '$fecha'";


			$stm = $this->pdo->prepare("SELECT  
			v.descuento AS descuentov,
			v.precio_costo, 
			v.id_venta AS id_venta , 
			v.id, 
			p.producto,
			v.comprobante, 
			v.metodo, 
			v.anulado,
			contado,
			p.codigo,
			p.iva,
			v.cantidad AS cantidad,
			v.precio_venta,
			subtotal AS subtotal,
			descuento AS descuento, 
			total AS total,
			margen_ganancia, 
			fecha_venta,
			nro_comprobante, 
			c.nombre as nombre_cli, 
			c.ruc,
			c.direccion, 
			c.telefono, 
			v.id_producto,
			v.can_factura,
			v.prod_factura,
			pf.producto AS prodfactura,
			p.fardo,
			v.id_presupuesto,
			(v.precio_venta -v.descuento) AS descuento_venta,
			((v.precio_venta -v.descuento)*v.cantidad) AS total_unidad,
			((v.precio_costo)*v.cantidad) AS total_costo,
			(((v.precio_venta -v.descuento)*v.cantidad) - ((v.precio_costo)*v.cantidad) )AS utilidad,
			(SELECT user FROM usuario u WHERE u.id = (SELECT pr.id_vendedor FROM presupuestos pr WHERE pr.id_presupuesto=v.id_presupuesto LIMIT 1) ) as vendedor,
			(SELECT uu.user FROM usuario uu WHERE uu.id = v.id_vendedor) as vendedor_caja, 
            v.id_cliente,
            v.cot_dolar,
            v.cot_real
					FROM ventas v 
					LEFT JOIN productos p ON v.id_producto = p.id 
					LEFT JOIN productos pf ON v.prod_factura = pf.id
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					WHERE v.anulado=0  $rango ORDER BY id_presupuesto DESC
					");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarProductoCat($id_cat, $desde, $hasta)
	{
		try
		{
			
			$rango = ($desde==0)? "":"AND fecha_venta >= '$desde' AND fecha_venta <= '$hasta'";
			$stm = $this->pdo->prepare("SELECT cat.id AS padre, sub.id AS hijo, cat.categoria AS categoria, sub.categoria AS sub_categoria, v.id, p.producto,v.comprobante, v.metodo, v.anulado, contado, p.codigo,p.iva, SUM(v.cantidad) AS cantidad, AVG(v.precio_costo) AS precio_costo, AVG(v.precio_venta) AS precio_venta, subtotal, descuento, SUM(total) AS total, margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, (SELECT user FROM usuario u WHERE u.id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id LEFT JOIN categorias sub ON sub.id = p.id_categoria LEFT JOIN categorias cat ON cat.id = sub.id_padre WHERE (cat.id = 4 OR sub.id = 4) AND v.anulado = 0 GROUP BY p.id");
			$stm->execute(array($id_cat, $id_cat));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarCliente($id_cliente)
	{
		try
		{
			
			
			$stm = $this->pdo->prepare("SELECT v.id, v.id_venta, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE id_cliente = ? GROUP BY v.id_venta ORDER BY v.id_venta DESC");
			$stm->execute(array($id_cliente));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarUsuarioMes($id_usuario, $mes)
	{
		try
		{
			
			$fecha = $mes."-10";
			$stm = $this->pdo->prepare("SELECT v.id, v.id_venta, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon, (SELECT comision FROM usuario WHERE id = v.id_vendedor) as comision  FROM ventas v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE vendedor_salon = ? AND MONTH(fecha_venta) = MONTH(?) AND YEAR(fecha_venta) = YEAR(?) AND v.anulado = '0' GROUP BY v.id_venta ORDER BY v.id_venta DESC");
			$stm->execute(array($id_usuario, $fecha, $fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarDia($fecha)
	{
		try
		{
			if(!isset($_SESSION['user_id'])){
			    session_start();
			}
			$id_vendedor = $_SESSION['user_id'];
			$usuario = ($_SESSION['nivel']==1)? "":"AND v.id_vendedor = $id_vendedor";
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE CAST(v.fecha_venta AS date) = ? $usuario  GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarDiaSesion()
	{
		try
		{
			if(!isset($_SESSION['user_id'])){
			    session_start();
			}
			$id_vendedor = $_SESSION['user_id'];
			$usuario = ($_SESSION['nivel']==1)? "":"AND v.id_vendedor = $id_vendedor";
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE fecha_venta >= (SELECT fecha_apertura FROM cierres WHERE id_usuario = ? AND fecha_cierre IS NULL) $usuario  GROUP BY v.id_venta DESC");
			$stm->execute(array($id_vendedor));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarDiaSinAnular($fecha)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE CAST(v.fecha_venta AS date) = ? AND v.anulado <> 1  GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarRango($desde, $hasta)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.id_cliente, v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ? AND v.anulado <> 1  GROUP BY v.id_venta DESC");
			$stm->execute(array($desde, $hasta));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarUsados($desde, $hasta)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT c.producto, v.fecha_venta, v.precio_costo, v.precio_venta, v.cantidad, (v.precio_venta*v.cantidad) as total FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ? AND v.anulado <> 1 AND v.id_cliente = 14 ORDER BY v.id DESC");
			$stm->execute(array($desde, $hasta));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
    /*
        -------------------------------------------------
         venta caja CONTADO por VENDEDOR y RANGO de FECHA
        -------------------------------------------------
    */
	public function ListarRangoSinAnularContado($desde, $hasta, $id_usuario)
	{
		try
		{
		   	if ($desde != '') {
				if ($hasta != '') {
					$rango = " AND CAST(v.fecha_venta as date) >= '$desde' AND CAST(v.fecha_venta as date) <= '$hasta'";
				} else {
					$rango = " AND CAST(v.fecha_venta as date) >= '$desde' AND CAST(v.fecha_venta as date) <= '$fecha'";
				}
			}
			if ($id_usuario != '') {
					$user = " AND v.id_vendedor = '$id_usuario'";
			}else {
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
			(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, v.id_presupuesto,
			(SELECT i.forma_pago FROM ingresos i WHERE i.id_venta = v.id_venta AND i.forma_pago='Efectivo' LIMIT 1) AS efectivo,
            (SELECT i.forma_pago FROM ingresos i WHERE i.id_venta = v.id_venta AND i.forma_pago<>'Efectivo' LIMIT 1) AS otros,
            (SELECT user FROM usuario WHERE id = (SELECT p.id_vendedor FROM presupuestos p WHERE v.id_presupuesto=p.id_presupuesto LIMIT 1) )as vendedor_salon
			FROM ventas v 
			LEFT JOIN productos c ON v.id_producto = c.id 
			LEFT JOIN clientes a ON v.id_cliente = a.id 
			WHERE v.contado='Contado'  AND v.anulado = 0 $rango $user  GROUP BY v.id_venta DESC");
			$stm->execute(array());
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	 /*
        -------------------------------------------------
         venta caja CREDITO por VENDEDOR y RANGO de FECHA 
        -------------------------------------------------
    */
	public function ListarRangoSinAnularcredito($desde, $hasta, $id_usuario)
	{
		try
		{
		   	if ($desde != '') {
				if ($hasta != '') {
					$rango = " AND CAST(v.fecha_venta as date) >= '$desde' AND CAST(v.fecha_venta as date) <= '$hasta'";
				} else {
					$rango = " AND CAST(v.fecha_venta as date) >= '$desde' AND CAST(v.fecha_venta as date) <= '$fecha'";
				}
			}
			if ($id_usuario != '') {
					$user = " AND v.id_vendedor = '$id_usuario'";
			}else {
					$user = " ";
			}
			
			
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(v.subtotal) as subtotal, SUM(v.descuento*v.cantidad) AS descuento, SUM(v.precio_costo * v.cantidad) as costo, SUM((v.precio_venta*v.cantidad)-(v.descuento*v.cantidad)) as total, AVG(v.margen_ganancia) as margen_ganancia, v.fecha_venta, v.nro_comprobante, v.id_producto, v.cantidad,v.precio_venta,
			(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, 
			(SELECT i.forma_pago FROM ingresos i WHERE i.id_venta = v.id_venta AND i.forma_pago='Efectivo' LIMIT 1) AS efectivo,
            (SELECT i.forma_pago FROM ingresos i WHERE i.id_venta = v.id_venta AND i.forma_pago<>'Efectivo' LIMIT 1) AS otros,
            (SELECT user FROM usuario WHERE id = (SELECT id_vendedor FROM presupuestos p WHERE v.id_presupuesto=p.id_presupuesto LIMIT 1) )as vendedor_salon
			FROM ventas v 
			LEFT JOIN productos c ON v.id_producto = c.id 
			LEFT JOIN clientes a ON v.id_cliente = a.id 
			WHERE v.contado='Credito'  AND v.anulado = 0 $rango $user  GROUP BY v.id_venta DESC");
			$stm->execute(array());
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarMesSinAnular($fecha)
	{
		try
		{
			$stm = $this->pdo->prepare("SELECT v.id_cliente, v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE MONTH(v.fecha_venta) = MONTH(?) AND YEAR(v.fecha_venta) = YEAR(?) AND v.anulado <> 1  GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarDiaContado($fecha)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, cli.Nombre as nombre_cli, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes cli ON v.id_cliente = cli.id WHERE CAST(v.fecha_venta AS date) = ? AND contado = 'contado' GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarMes($fecha)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.id_venta, cli.Nombre AS nombre_cli, v.metodo, c.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as ganancia, fecha_venta, nro_comprobante FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes cli ON cli.id = v.id_cliente  WHERE MONTH(v.fecha_venta) = MONTH(?) AND YEAR(v.fecha_venta) = YEAR(?) GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function Detalles($id_venta)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT c.producto, subtotal, descuento, total, margen_ganancia, fecha_venta, nro_comprobante FROM ventas v JOIN productos c ON v.id_producto = c.id WHERE v.id_venta = ?");
			$stm->execute(array($id_venta));

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
			          ->prepare("SELECT * FROM ventas WHERE id = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerVenta($id_venta)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM ventas WHERE id_venta = ? LIMIT 1");
			          

			$stm->execute(array($id_venta));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerProducto($id_venta, $id_producto)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM ventas WHERE id_venta = ? AND id_producto = ?");
			          

			$stm->execute(array($id_venta, $id_producto));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerUNO($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM ventas WHERE id_venta = ? LIMIT 1");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerCantMaxima($id_venta, $id_producto)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT v.*,
					  IFNULL(
						  (SELECT dev.cantidad FROM devoluciones_ventas dev WHERE dev.id_venta = v.id_venta AND v.id_producto = dev.id_producto LIMIT 1),
						  v.cantidad
					  ) AS cantidad_maxima
				  FROM ventas v
				  WHERE v.id_venta = ? AND v.id_producto = ?");
			          

			$stm->execute(array($id_venta, $id_producto));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Recibo($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM ventas WHERE id_venta = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Ultimo()
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT MAX(id_venta) as id_venta FROM ventas");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}


	public function Cantidad($id_item, $id_venta, $cantidad)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("UPDATE ventas SET cantidad = ?, subtotal = precio_venta * ?, total = precio_venta * ? WHERE id = ?");
			$stm->execute(array($cantidad, $cantidad, $cantidad, $id_item));
			$stm = $this->pdo
			          ->prepare("SELECT *, (SELECT SUM(total) FROM ventas WHERE id_venta = ? GROUP BY id_venta) as total_venta FROM ventas WHERE id = ?");
			$stm->execute(array($id_venta, $id_item));
		
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function CancelarItem($id_item)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("DELETE FROM ventas WHERE id = ?");			          

			$stm->execute(array($id_item));
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
			            ->prepare("DELETE FROM ventas WHERE id_venta = ?");			          

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
			            ->prepare("UPDATE ventas SET anulado = 1 WHERE id_venta = ?");			          

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
			$sql = "UPDATE ventas SET
						id_venta        = ?,
						id_vendedor     = ?,
						id_producto     = ?,
						precio_venta    = ?,
                        cantidad        = ?, 
						margen_ganancia = ?,
						fecha_venta     = ?,
						id_gift         = ?,
						id_presupuesto         = ?
						
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
						$data->id_presupuesto,
                        $data->id
					)
				);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar($data)
	{
		try 
		{

		$sql = "INSERT INTO ventas (id_venta, id_cliente, id_vendedor, vendedor_salon, id_producto, precio_costo, precio_venta, subtotal, descuento, iva, total, comprobante, nro_comprobante, cantidad, margen_ganancia, fecha_venta, metodo, banco, contado, id_gift, id_presupuesto, id_devolucion, prod_factura, can_factura, cot_dolar, cot_real, moneda, id_sucursal) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_venta,
                    $data->id_cliente,
                    $data->id_vendedor,
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
                    $data->cantidad,
                    $data->margen_ganancia, 
                    $data->fecha_venta,
                    $data->metodo,
                    $data->banco,
                    $data->contado,
                    $data->id_gift,
					$data->id_presupuesto,
					$data->id_devolucion,
					$data->prod_factura,
					$data->can_factura,
					$data->cot_dolar,
					$data->cot_real,
					$data->moneda,                   
					$data->id_sucursal                   
                )
			);

		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}