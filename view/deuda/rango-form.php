<form method="post">
		    	<input type="hidden" name="c" value="deuda">
		    	<input type="hidden" name="a" value="Clientepdf">
		    	<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
		    	<br>
		    	<h4>Deuda por rango de fecha</h4>
		        <div class="form-group">
                        <label>Desde</label>
                        <input type="date" min="2020-11-01" max="<?php echo date("Y-m-d") ?>" name="desde" class="form-control">
                </div>
		    	<div class="form-group">
                        <label>Hasta</label>
                        <input type="date" min="2020-11-01" max="<?php echo date("Y-m-d") ?>" name="hasta" class="form-control">
                </div>
		    	<div class="form-group">
		        	<center><input type="submit" value="Generar" class="btn btn-primary"></center>
		    	</div>
			
          	</form>
          	