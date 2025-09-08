<nav class="navbar navbar-default">
    <div class="container-fluid">

	    <div class="navbar-header">
	        <button type="button" id="sidebarCollapse" class="navbar-btn">
	            <span></span>
	            <span></span>
	            <span></span>
	        </button>
	    </div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		    <ul class="nav navbar-nav navbar-right">
		        <li><a href='?c=usuario&a=password' ><span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo $_SESSION['username'] ?></a></li>
		        <li><a href="login.php">Cerrar sesión</a></li>
		    </ul>
		</div>

    </div>
</nav>