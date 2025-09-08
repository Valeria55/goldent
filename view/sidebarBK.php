<nav id="sidebar">
    <div class="sidebar-header" align="center">
        <img src="assets/img/sionlogo.png" width="150" align="center">
    </div>

    <ul class="list-unstyled components">

        <?php $cumples=count($this->cliente->ListarCumple(date("d"), date("m")));
              $cumples = ($cumples > 0)? ' <i class="fas fa-birthday-cake"></i> '.$cumples:''; ?>
        
        <?php if($_SESSION['nivel']==0){ ?>
        <li <?php if(isset($_GET['c']) && $_GET['c'] =='sucursal') echo "class='active'"; ?>>
            <a href="?c=sucursal">Sucursales</a>
        </li>
        <?php } ?>
        
        <?php if($_SESSION['nivel']==1){ ?>
        <li <?php if($_GET['c']=='usuario') echo "class='active'"; ?>>
            <a href="?c=usuario">Usuarios</a>
        </li>
        <li>
            <a href="#productoSubmenu" data-toggle="collapse" aria-expanded="false">Productos</a>
            <ul class="collapse list-unstyled 
            <?php 
                if($_GET['c']=='producto' || 
                   $_GET['c']=='categoria'||
                   $_GET['c']=='marca') 
                    echo " in"; 
                ?>
            " id="productoSubmenu">
                <li <?php if($_GET['c']=='producto') echo "class='active'"; ?>>
                    <a href="?c=producto">Productos</a>
                </li>
                <li <?php if($_GET['c']=='categoria') echo "class='active'"; ?>>
                    <a href="?c=categoria">Categorías</a>
                </li>
                 <li <?php if($_GET['c']=='marca') echo "class='active'"; ?>>
                    <a href="?c=marca">Marcas</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#cajaSubmenu" data-toggle="collapse" aria-expanded="false">Control de caja</a>
            <ul class="collapse list-unstyled 
                <?php 
                    if($_GET['c']=='egreso' || 
                       $_GET['c']=='ingreso'||
                       $_GET['c']=='deuda'  ||
                       $_GET['c']=='acreedor') 
                        echo " in"; 
                ?>
                " id="cajaSubmenu">
                <li <?php if($_GET['c']=='ingreso' && !isset($_GET['a'])) echo "class='active'"; ?>>
                    <a href="?c=ingreso">Ingresos</a>
                </li>
                <li <?php if($_GET['c']=='egreso') echo "class='active'"; ?>>
                    <a href="?c=egreso">Egresos</a>
                </li>
                <li <?php if($_GET['c']=='deuda') echo "class='active'"; ?>>
                    <a href="?c=deuda">Deudores</a>
                </li>
                <li <?php if($_GET['c']=='acreedor') echo "class='active'"; ?>>
                    <a href="?c=acreedor">Acreedores</a>
                </li>
                <li <?php if(isset($_GET['a']) && $_GET['a']=='balance') echo "class='active'"; ?>>
                    <a href="?c=ingreso&a=balance">Balance</a>
                </li>
            </ul>
        </li>

        <li>
            <a href="#compraSubmenu" data-toggle="collapse" aria-expanded="false">Compras</a>
            <ul class="collapse list-unstyled 
            <?php 
                if($_GET['c']=='compra' || 
                   $_GET['c']=='compra_tmp') 
                    echo " in"; 
            ?>
            " id="compraSubmenu">
                <li <?php if(!isset($_GET['a']) && $_GET['c']=='compra') echo "class='active'"; ?>>
                    <a href="?c=compra">Compras</a>
                </li>
                <!--<li class='active'><a href="#">Ventas no finalizadas</a></li>-->
                <li <?php if(isset($_GET['a']) && $_GET['a']=='listardia') echo "class='active'"; ?>>
                    <a href="?c=compra&a=listardia">Compras del día</a>
                </li>
                <li <?php if(isset($_GET['c']) && $_GET['c']=='compra_tmp') echo "class='active'"; ?>>
                    <a href="?c=compra_tmp">+ Nueva compra</a></li>
                </ul>
        </li>
        <?php } ?>
        
        

        <li <?php if(isset($_GET['c']) && $_GET['c']=='cliente') echo "class='active'"; ?>>
            <a href="?c=cliente">Clientes <span class="badge"><?php echo $cumples ?></span></a>
        </li>

        <li <?php if(isset($_GET['c']) && $_GET['c']=='transferencia') echo "class='active'"; ?>>
            <a href="?c=transferencia">Transferencias <span class="badge"><?php echo $cumples ?></span></a>
        </li>
        
        
        
        <?php if($_SESSION['nivel']==1){ ?>
        <li>
            <a href="#ventaSubmenu" data-toggle="collapse" aria-expanded="false">Ventas</a>
            <ul class="collapse list-unstyled
            <?php 
                if($_GET['c']=='venta' || 
                   $_GET['c']=='venta_tmp' ||
                   $_GET['c']=='cierre') 
                    echo " in"; 
            ?>
            " id="ventaSubmenu">
                <li <?php if(isset($_GET['c']) && $_GET['c']=='venta') echo "class='active'"; ?>>
                    <a href="?c=venta">Ventas</a>
                </li>
                <li <?php if(isset($_GET['c']) && $_GET['c']=='cierre') echo "class='active'"; ?>>
                    <a href="?c=cierre">Sesiones</a>
                </li>
                <!--<li class='active'><a href="#">Ventas no finalizadas</a></li>-->
                <li <?php if(isset($_GET['a']) && $_GET['a']=='listardia') echo "class='active'"; ?>>
                    <a href="?c=venta&a=listardia">Ventas del día</a>
                </li>
                <li <?php if(isset($_GET['c']) && $_GET['c']=='venta_tmp') echo "class='active'"; ?>>
                    <a href="?c=venta_tmp">+ Nueva venta</a>
                </li>
            </ul>
        </li>
        <?php } ?>
        <?php if($_SESSION['nivel']>1){ ?>
        <li <?php if(isset($_GET['a']) && $_GET['a']=='deposito') echo "class='active'"; ?>>
            <a href="?c=ingreso&a=deposito">Depósitos</a>
        </li>
        <li <?php if(isset($_GET['a']) && $_GET['a']=='extraccion') echo "class='active'"; ?>>
            <a href="?c=egreso&a=extraccion">Extracciones</a>
        </li>
        <li <?php if(isset($_GET['a']) && $_GET['a']=='sesion') echo "class='active'"; ?>>
            <a href="?c=venta&a=sesion">Ventas de la sesión</a>
        </li>
        <?php } ?>
        <li <?php if(isset($_GET['c']) && $_GET['c']=='venta_tmp') echo "class='active'"; ?>> 
            <a href="?c=venta_tmp" >
                <?php if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])): ?>
                + Nueva venta (F2)
                <?php else: ?>
                Apertura de Caja
                <?php endif ?>
            </a>
        </li>
        
    </ul>

    <ul class="list-unstyled CTAs">
        <li><a href="https://trinitytech.com.py" class="download">&copy;TRINITY TECH <?php echo date("Y") ?></a></li>
    </ul>
</nav>
