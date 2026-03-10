
<style>
	/* Permite ver todo el contenido del modal con scroll interno */
	#detallesModal {
		overflow-y: auto;
	}
	#detallesModal .modal-dialog {
		margin-top: 10px;
		margin-bottom: 10px;
	}
	#detallesModal .modal-content {
		max-height: calc(100vh - 20px);
		overflow: hidden;
	}
	#detallesModal .modal-body {
		max-height: calc(100vh - 40px);
		overflow-y: auto;
		-webkit-overflow-scrolling: touch;
	}
</style>

<div id="detallesModal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="detallesModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-body" id="modal-detalles">
          	</div>
      	</div>
	</div>
</div>