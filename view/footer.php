 </div>
 </div>
 </div>
 <script src="assets/admin/js/datatable.js"> </script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
 <script src="plugins/hotkeys-js@3.7.3/hotkeys.min.js"></script>
 <!-- <script src="https://unpkg.com/hotkeys-js/dist/hotkeys.min.js"></script> -->
 <!-- Fancybox -->
 <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
 <script type="text/javascript">
   hotkeys('f2, f4, ctrl+b', function(event, handler) {
     switch (handler.key) {
       case 'f2':
         location.href = "?c=venta_tmp";
         break;
       case 'f4':
         $("#finalizarModal").modal("show");
         $("#finalizar-step").modal("show");
         $("#id_cliente").focus();
         break;
       
       case 'ctrl+b':
         alert('you pressed ctrl+b!');
         break;
       default:
         alert(event);
     }
   });

   hotkeys('f2, f4, ctrl+b', function(event, handler) {
     switch (handler.key) {
       case 'f2':
         location.href = "?c=venta_tmp";
         break;
       case 'f4':
         $("#finalizar-step").modal("show");
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
   //para que funcionen todas las caracteristicas, usar el mismo import del script de este proyecto, y especificar colores de iconos
   const Toast = Swal.mixin({
     toast: true,
     position: 'top-right',
     iconColor: 'white',
     customClass: 'swal-wide',
     showConfirmButton: false,
     timer: 3000,
     timerProgressBar: true,
     willOpen: (toast) => {
       toast.addEventListener('mouseenter', Swal.stopTimer)
       toast.addEventListener('mouseleave', Swal.resumeTimer)
     }
   });
 </script>



 </body>

 </html>