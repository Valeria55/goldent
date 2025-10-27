<nav id="sidebar">
    <div class="sidebar-header" align="center">

        <img src="assets/img/LogoTrinity.PNG" width="200" align="center">

    </div>
    <?php //NIVEL 2 CAJERO 
    ?>
    <ul class="list-unstyled components">
        <?php if (($_SESSION['nivel'] == 1)) : ?>
            <li>
                <a href="#dashboardSubmenu" data-toggle="collapse" aria-expanded="false">Dashboards</a>
                <ul class="collapse list-unstyled 
    <?php
            if (
                $_GET['c'] == 'dashboardActual' ||
                $_GET['c'] == 'dashboardOperativo' ||
                $_GET['c'] == 'dashboardClientes'
            )
                echo " in";
    ?>
    " id="dashboardSubmenu">
                    <li <?php if ($_GET['c'] == 'dashboardActual') echo "class='active'"; ?>>
                        <a href="?c=dashboardActual">Actual</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'dashboardOperativo') echo "class='active'"; ?>>
                        <a href="?c=dashboardOperativo">Operativo</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'dashboardClientes') echo "class='active'"; ?>>
                        <a href="?c=dashboardClientes">Clientes</a>
                    </li>
                </ul>
            </li>

        <?php endif; ?>

        <?php if ($_SESSION['nivel'] == 2) { ?>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'caja') echo "class='active'"; ?>>
                <a href="?c=caja">Cajas</a>
            </li>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'egreso' && isset($_GET['a']) && $_GET['a'] == 'extraccion') echo "class='active'"; ?>>
                <a href="?c=egreso&a=extraccion">Extracción</a>
            </li>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'cliente') echo "class='active'"; ?>>
                <a href="?c=cliente">Personas</a>
            </li>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'presupuesto') echo "class='active'"; ?>>
                <a href="?c=presupuesto">Presupuesto</a>
            </li>
            <li>
                <a href="#cajaSubmenu" data-toggle="collapse" aria-expanded="false">Control de caja</a>
                <ul class="collapse list-unstyled 
                <?php
                if (
                    $_GET['c'] == 'egreso' ||
                    $_GET['c'] == 'ingreso' ||
                    $_GET['c'] == 'deuda'  ||
                    $_GET['c'] == 'metodo'  ||
                    $_GET['c'] == 'acreedor'
                )
                    echo " in";
                ?>
                " id="cajaSubmenu">
                    <li <?php if ($_GET['c'] == 'ingreso' && !isset($_GET['a'])) echo "class='active'"; ?>>
                        <a href="?c=ingreso">Ingresos</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'deuda') echo "class='active'"; ?>>
                        <a href="?c=deuda">Deudores</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'acreedor') echo "class='active'"; ?>>
                        <a href="?c=acreedor">Acreedores</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#ventaSubmenu" data-toggle="collapse" aria-expanded="false">Ventas</a>
                <ul class="collapse list-unstyled
                    <?php
                    if (
                        $_GET['c'] == 'venta' ||
                        $_GET['c'] == 'venta_tmp' ||
                        $_GET['c'] == 'devolucion_ventas'
                    )
                        echo " in";
                    ?>
                     " id="ventaSubmenu">
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'venta') echo "class='active'"; ?>>
                        <a href="?c=venta">Ventas</a>
                    </li>
                    
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 'listardia') echo "class='active'"; ?>>
                        <a href="?c=venta&a=listardia">Ventas del día</a>
                    </li>
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'venta_tmp' && !isset($_GET['a'])) echo "class='active'"; ?>>
                        <a href="?c=venta_tmp">+ Nueva venta</a>
                    </li>

                </ul>
            </li>
           
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'venta_tmp') echo "class='active'"; ?>>
                <a href="?c=venta_tmp">
                    <?php if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) : ?>
                        + Nuevo Presupuesto (F2)
                    <?php else : ?>
                        Apertura de Caja
                    <?php endif ?>
                </a>
            </li>
        <?php } ?>
        <?php //NIVEL 3 VENDEDOR 
        ?>
        <?php if ($_SESSION['nivel'] == 3) { ?>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'cliente') echo "class='active'"; ?>>
                <a href="?c=cliente">Clientes</a>
            </li>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'presupuesto') echo "class='active'"; ?>>
                <a href="?c=presupuesto">Presupuesto</a>
            </li>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'presupuesto_tmp' && !isset($_GET['a'])) echo "class='active'"; ?>>
                <a href="?c=presupuesto_tmp">+ Nuevo presupuesto</a>
            </li>
        <?php }

        //NIVEL 1 ADMINISTRADOR 
        ?>

        <?php if ($_SESSION['nivel'] == 1) { ?>



            <li>
                <a href="#cajasSubmenu" data-toggle="collapse" aria-expanded="false">Cajas</a>
                <ul class="collapse list-unstyled 
            <?php
            if (
                $_GET['c'] == 'caja' ||
                $_GET['c'] == 'movimientoCaja'
            )
                echo " in";
            ?>
            " id="cajasSubmenu">
                    <li <?php if ($_GET['c'] == 'caja') echo "class='active'"; ?>>
                        <a href="?c=caja">Vista</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'movimientoCaja') echo "class='active'"; ?>>
                        <a href="?c=caja&a=movimientoCaja">Movimientos</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'egreso' && $_GET['a'] == 'extraccion') echo "class='active'"; ?>>
                        <a href="?c=egreso&a=extraccion">Extracción</a>
                    </li>
                </ul>
            </li>

            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'usuario') echo "class='active'"; ?>>
                <a href="?c=usuario">Usuarios</a>
            </li>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'cliente') echo "class='active'"; ?>>
                <a href="?c=cliente">Personas</a>
            </li>


            <li>
                <a href="#productoSubmenu" data-toggle="collapse" aria-expanded="false">Productos</a>
                <ul class="collapse list-unstyled 
            <?php
            if (
                $_GET['c'] == 'producto' ||
                $_GET['c'] == 'categoria' ||
                $_GET['c'] == 'devolucion' ||
                $_GET['c'] == 'devolucion_tmp' ||
                $_GET['c'] == 'inventario'
            )
                echo " in";
            ?>
            " id="productoSubmenu">
                    <li <?php if ($_GET['c'] == 'producto') echo "class='active'"; ?>>
                        <a href="?c=producto">Productos</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'producto' && $_GET['a'] == 'servicios') echo "class='active'"; ?>>
                        <a href="?c=producto&a=servicios">Servicios</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'categoria') echo "class='active'"; ?>>
                        <a href="?c=categoria">Categorías</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'marca') echo "class='active'"; ?>>
                        <a href="?c=marca">Marcas</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'categoria') echo "class='active'"; ?>>
                        <a href="?c=devolucion">Ajustes de stock</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'inventario') echo "class='active'"; ?>>
                        <a href="?c=cierre_inventario&a=Cierreinventario">Inventario</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#cajaSubmenu" data-toggle="collapse" aria-expanded="false">Control de caja</a>
                <ul class="collapse list-unstyled 
                <?php
                if (
                    $_GET['c'] == 'egreso' ||
                    $_GET['c'] == 'ingreso' ||
                    $_GET['c'] == 'deuda'  ||
                    $_GET['c'] == 'metodo'  ||
                    $_GET['c'] == 'acreedor'
                )
                    echo " in";
                ?>
                " id="cajaSubmenu">
                    <li <?php if ($_GET['c'] == 'ingreso' && !isset($_GET['a'])) echo "class='active'"; ?>>
                        <a href="?c=ingreso">Ingresos</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'egreso') echo "class='active'"; ?>>
                        <a href="?c=egreso">Egresos</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'deuda') echo "class='active'"; ?>>
                        <a href="?c=deuda">Deudores</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'acreedor') echo "class='active'"; ?>>
                        <a href="?c=acreedor">Acreedores</a>
                    </li>
                    <?php if (($_SESSION['nivel'] == 1)) : ?>
                        <li <?php if (isset($_GET['a']) && $_GET['a'] == 'balance') echo "class='active'"; ?>>
                            <a href="?c=ingreso&a=balance">Balance</a>
                        </li>
                    <?php endif; ?>
                    <li <?php if ($_GET['c'] == 'metodo') echo "class='active'"; ?>>
                        <a href="?c=metodo">Metodos de pago</a>
                    </li>
                    <!-- <li <?php //if(isset($_GET['a']) && $_GET['a']=='EstadoResultado') echo "class='active'"; 
                                ?>>
                    <a href="?c=venta&a=EstadoResultado">Estado de Resultado</a>
                </li>-->
                </ul>
            </li>

            <li>
                <a href="#compraSubmenu" data-toggle="collapse" aria-expanded="false">Compras</a>
                <ul class="collapse list-unstyled 
            <?php
            if (
                $_GET['c'] == 'compra' ||
                $_GET['c'] == 'compra_tmp'
            )
                echo " in";
            ?>
            " id="compraSubmenu">
                    <li <?php if (!isset($_GET['a']) && $_GET['c'] == 'compra') echo "class='active'"; ?>>
                        <a href="?c=compra">Compras</a>
                    </li>
                    <li <?php if (!isset($_GET['a']) && $_GET['c'] == 'devolucion_compras') echo "class='active'"; ?>>
                        <a href="?c=devolucion_compras">Devoluciones</a>
                    </li>
                    <!--<li class='active'><a href="#">Ventas no finalizadas</a></li>-->
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 'listardia') echo "class='active'"; ?>>
                        <a href="?c=compra&a=listardia">Compras del día</a>
                    </li>
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'compra_tmp') echo "class='active'"; ?>>
                        <a href="?c=compra_tmp">+ Nueva compra</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#presupuestoSubmenu" data-toggle="collapse" aria-expanded="false">Presupuesto</a>
                <ul class="collapse list-unstyled 
                <?php
                if (
                    $_GET['c'] == 'presupuesto'
                )
                    echo " in";
                ?>
                " id="presupuestoSubmenu">
                    <li <?php if ($_GET['c'] == 'ingreso' && !isset($_GET['a'])) echo "class='active'"; ?>>
                        <a href="?c=presupuesto">Presupuestos</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'egreso') echo "class='active'"; ?>>
                        <a href="?c=presupuesto&a=ListarAnulado">Presupuestos Anulados</a>
                    </li>
                </ul>
            </li>
            <!--<li <?php //if (isset($_GET['c']) && $_GET['c'] == 'presupuesto') echo "class='active'"; 
                    ?>>-->
            <!--    <a href="?c=presupuesto">Presupuesto</a>-->
            <!--</li>-->
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'presupuesto_tmp' && !isset($_GET['a'])) echo "class='active'"; ?>>
                <a href="?c=presupuesto_tmp">+ Nuevo presupuesto</a>
            </li>
            <li>
                <a href="#ventaSubmenu" data-toggle="collapse" aria-expanded="false">Ventas</a>
                <ul class="collapse list-unstyled
            <?php
            if (
                $_GET['c'] == 'venta' ||
                $_GET['c'] == 'venta_tmp' ||
                $_GET['c'] == 'devolucion_ventas'
            )
                echo " in";
            ?>
            " id="ventaSubmenu">
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'venta') echo "class='active'"; ?>>
                        <a href="?c=venta">Ventas</a>
                    </li>
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'devolucion_ventas') echo "class='active'"; ?>>
                        <a href="?c=devolucion_ventas">Devoluciones</a>
                    </li>
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'cierre') echo "class='active'"; ?>>
                        <a href="?c=cierre">Sesiones</a>
                    </li>
                    <!--<li class='active'><a href="#">Ventas no finalizadas</a></li>-->
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 'listardia') echo "class='active'"; ?>>
                        <a href="?c=venta&a=listardia">Ventas del día</a>
                    </li>
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'venta_tmp' && !isset($_GET['a'])) echo "class='active'"; ?>>
                        <a href="?c=venta_tmp">+ Nueva venta</a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="#cierreSubmenu" data-toggle="collapse" aria-expanded="false">Cierres</a>
                <ul class="collapse list-unstyled
            <?php
            if ($_GET['c'] == 'cierre')
                echo " in";
            ?>
            " id="cierreSubmenu">
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'cierre' && !isset($_GET['a'])) echo "class='active'"; ?>>
                        <a href="?c=cierre">Sesiones</a>
                    </li>
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 'activas') echo "class='active'"; ?>>
                        <a href="?c=cierre&a=activas">Sesiones activas</a>
                    </li>
                </ul>
            </li>

            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'venta_tmp') echo "class='active'"; ?>>
                <a href="?c=venta_tmp">
                    <?php if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) : ?>
                        + Nueva venta (F2)
                    <?php else : ?>
                        Apertura de Caja
                    <?php endif ?>
                </a>
            </li>

        <?php } ?>


        <?php //NIVEL 4 ESPECIAL 
        ?>

        <?php if ($_SESSION['nivel'] == 4) { ?>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'caja') echo "class='active'"; ?>>
                <a href="?c=caja">Cajas</a>
            </li>
            <li <?php if ($_GET['c'] == 'egreso' && $_GET['a'] == 'extraccion') echo "class='active'"; ?>>
                        <a href="?c=egreso&a=extraccion">Extracción</a>
                    </li>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'cliente') echo "class='active'"; ?>>
                <a href="?c=cliente">Personas</a>
            </li>
            
            <li>
                <a href="#productoSubmenu" data-toggle="collapse" aria-expanded="false">Productos</a>
                <ul class="collapse list-unstyled 
                        <?php
                        if (
                            $_GET['c'] == 'producto' ||
                            $_GET['c'] == 'categoria' ||
                            $_GET['c'] == 'devolucion' ||
                            $_GET['c'] == 'devolucion_tmp' ||
                            $_GET['c'] == 'inventario'
                        )
                            echo " in";
                        ?>
                        " id="productoSubmenu">
                    <li <?php if ($_GET['c'] == 'producto') echo "class='active'"; ?>>
                        <a href="?c=producto">Productos</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'producto' && $_GET['a'] == 'servicios') echo "class='active'"; ?>>
                        <a href="?c=producto&a=servicios">Servicios</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'categoria') echo "class='active'"; ?>>
                        <a href="?c=categoria">Categorías</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'marca') echo "class='active'"; ?>>
                        <a href="?c=marca">Marcas</a>
                    </li>



                </ul>
            </li>
            <li>
                <a href="#cajaSubmenu" data-toggle="collapse" aria-expanded="false">Control de caja</a>
                <ul class="collapse list-unstyled 
                            <?php
                            if (
                                $_GET['c'] == 'egreso' ||
                                $_GET['c'] == 'ingreso' ||
                                $_GET['c'] == 'deuda'  ||
                                $_GET['c'] == 'metodo'  ||
                                $_GET['c'] == 'acreedor'
                            )
                                echo " in";
                            ?>
                            " id="cajaSubmenu">
                    <li <?php if ($_GET['c'] == 'ingreso' && !isset($_GET['a'])) echo "class='active'"; ?>>
                        <a href="?c=ingreso">Ingresos</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'egreso') echo "class='active'"; ?>>
                        <a href="?c=egreso">Egresos</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'deuda') echo "class='active'"; ?>>
                        <a href="?c=deuda">Deudores</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'acreedor') echo "class='active'"; ?>>
                        <a href="?c=acreedor">Acreedores</a>
                    </li>
                    <li <?php if ($_GET['c'] == 'metodo') echo "class='active'"; ?>>
                        <a href="?c=metodo">Metodos de pago</a>
                    </li>
                    <!-- <li <?php //if(isset($_GET['a']) && $_GET['a']=='EstadoResultado') echo "class='active'"; 
                                ?>>
                                <a href="?c=venta&a=EstadoResultado">Estado de Resultado</a>
                            </li>-->
                </ul>
            </li>

            <li>
                <a href="#compraSubmenu" data-toggle="collapse" aria-expanded="false">Compras</a>
                <ul class="collapse list-unstyled 
                        <?php
                        if (
                            $_GET['c'] == 'compra' ||
                            $_GET['c'] == 'compra_tmp'
                        )
                            echo " in";
                        ?>
                        " id="compraSubmenu">
                    <li <?php if (!isset($_GET['a']) && $_GET['c'] == 'compra') echo "class='active'"; ?>>
                        <a href="?c=compra">Compras</a>
                    </li>
                    <!--<li class='active'><a href="#">Ventas no finalizadas</a></li>-->
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 'listardia') echo "class='active'"; ?>>
                        <a href="?c=compra&a=listardia">Compras del día</a>
                    </li>
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'compra_tmp') echo "class='active'"; ?>>
                        <a href="?c=compra_tmp">+ Nueva compra</a>
                    </li>
                </ul>
            </li>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'presupuesto') echo "class='active'"; ?>>
                <a href="?c=presupuesto">Presupuesto</a>
            </li>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'presupuesto_tmp' && !isset($_GET['a'])) echo "class='active'"; ?>>
                <a href="?c=presupuesto_tmp">+ Nuevo presupuesto</a>
            </li>
            <li>
                <a href="#ventaSubmenu" data-toggle="collapse" aria-expanded="false">Ventas</a>
                <ul class="collapse list-unstyled
                        <?php
                        if (
                            $_GET['c'] == 'venta' ||
                            $_GET['c'] == 'venta_tmp' ||
                            $_GET['c'] == 'devolucion_ventas'
                        )
                            echo " in";
                        ?>
                        " id="ventaSubmenu">
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'venta') echo "class='active'"; ?>>
                        <a href="?c=venta">Ventas</a>
                    </li>
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'devolucion_ventas') echo "class='active'"; ?>>
                        <a href="?c=devolucion_ventas">Devoluciones</a>
                    </li>
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'cierre') echo "class='active'"; ?>>
                        <a href="?c=cierre">Sesiones</a>
                    </li>
                    <!--<li class='active'><a href="#">Ventas no finalizadas</a></li>-->
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 'listardia') echo "class='active'"; ?>>
                        <a href="?c=venta&a=listardia">Ventas del día</a>
                    </li>
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'venta_tmp' && !isset($_GET['a'])) echo "class='active'"; ?>>
                        <a href="?c=venta_tmp">+ Nueva venta</a>
                    </li>

                </ul>
            </li>

            <li>
                <a href="#cierreSubmenu" data-toggle="collapse" aria-expanded="false">Cierres</a>
                <ul class="collapse list-unstyled
                        <?php
                        if ($_GET['c'] == 'cierre')
                            echo " in";
                        ?>
                        " id="cierreSubmenu">
                    <li <?php if (isset($_GET['c']) && $_GET['c'] == 'cierre' && !isset($_GET['a'])) echo "class='active'"; ?>>
                        <a href="?c=cierre">Sesiones</a>
                    </li>
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 'activas') echo "class='active'"; ?>>
                        <a href="?c=cierre&a=activas">Sesiones activas</a>
                    </li>
                </ul>
            </li>
            <li <?php if (isset($_GET['c']) && $_GET['c'] == 'venta_tmp') echo "class='active'"; ?>>
                <a href="?c=venta_tmp">
                    <?php if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) : ?>
                        + Nueva venta (F2)
                    <?php else : ?>
                        Apertura de Caja
                    <?php endif ?>
                </a>
            </li>

        <?php } ?>

    </ul>

    <ul class="list-unstyled CTAs">
        <li><a href="https://trinitytech.com.py" class="download">&copy;TRINITY TECH <?php echo date("Y") ?></a></li>
    </ul>
</nav>