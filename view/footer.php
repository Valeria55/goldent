 </div>
 </div>
 </div>
 <script src="assets/admin/js/datatable.js"> </script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
 <script src="https://trinitytech.com.py/common/plugins/hotkeys-js%403.7.3/hotkeys.min.js"></script>
 <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script> -->
 <!-- boo -->
 <script src="plugins/selectpicker/bootstrap-select.min.js"> </script>
 <!--<script src="https://unpkg.com/hotkeys-js/dist/hotkeys.min.js"></script>-->
 <script type="text/javascript">
   hotkeys('f2, f4, ctrl+b', function(event, handler) {
     switch (handler.key) {
       case 'f2':
         location.href = "?c=venta_tmp";
         break;
       case 'f4':
         $("#finalizarModal").modal("show");
         $("#finalizar_venta").focus();
         break;
       case 'ctrl+b':
         alert('you pressed ctrl+b!');
         break;
       default:
         alert(event);
     }
   });
 </script>
 <script src="view/ajax.js"> </script>

 <script type="text/javascript">
   $(document).ready(function() {
     $('#sidebarCollapse').on('click', function() {
       $('#sidebar').toggleClass('active');
       $(this).toggleClass('active');
     });
   });
 </script>
 <script type="text/javascript">
   $('.delete').on("click", function(e) {
     e.preventDefault();
     Swal.fire({
       title: '¿Estás seguro?',
       text: "No se pueder revertir!",
       icon: 'warning',
       showCancelButton: true,
       confirmButtonColor: '#3085d6',
       cancelButtonColor: '#d33',
       confirmButtonText: 'Si, deseo eliminar!'
     }).then((result) => {
       if (result.value) {

         window.location.href = $(this).attr('href');

       }
     })
   });
 </script>



 </body>

 </html>