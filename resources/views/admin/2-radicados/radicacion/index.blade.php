@extends("theme.$theme.layout")

@section('titulo')
Radicados
@endsection
@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet" type="text/css" />
<link href="{{asset("assets/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.css" rel="stylesheet" type="text/css" />
@endsection


@section('scripts')

<script src="{{asset("assets/pages/scripts/admin/usuario/crearuser.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
@include('admin.2-radicados.radicacion.tablas.tablaIndexRadicado')
@include('admin.2-radicados.radicacion.modal.modalRadicado')

@endsection



@section("scriptsPlugins")
<script src="{{asset("assets/$theme/plugins/datatables/jquery.dataTables.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/$theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/js/jquery-select2/select2.min.js")}}" type="text/javascript"></script>


<script src="https://cdn.datatables.net/plug-ins/1.10.20/api/sum().js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>

<script>
  $(document).ready(function() {

    //initiate dataTables plugin
    var myTable =
      $('#tradicado').DataTable({
        language: idioma_espanol,
        processing: true,
        lengthMenu: [
          [25, 50, 100, 500, -1],
          [25, 50, 100, 500, "Mostrar Todo"]
        ],
        processing: true,
        serverSide: true,
        aaSorting: [
          [1, "asc"]
        ],

        ajax: {
          url: "{{ route('radicados')}}",
        },
        columns: [{
            data: 'action',
            name: 'action',
            orderable: false
          },
          {
            data: 'cod_radi',
            name: 'cod_radi'
          },
          {
            data: 'observaciones',
            name: 'observaciones'
          },
          {
            data: 'pnombre',
            name: 'pnombre'
          },
          {
            data: 'papellido',
            name: 'papellido'
          },
          {
            data: 'estado_radi',
            name: 'estado_radi'
          }

        ],

        //Botones----------------------------------------------------------------------

        "dom": '<"row"<"col-xs-1 form-inline"><"col-md-4 form-inline"l><"col-md-5 form-inline"f><"col-md-3 form-inline"B>>rt<"row"<"col-md-8 form-inline"i> <"col-md-4 form-inline"p>>',


        buttons: [{

            extend: 'copyHtml5',
            titleAttr: 'Copiar Registros',
            title: "seguimiento",
            className: "btn  btn-outline-primary btn-sm"


          },
          {

            extend: 'excelHtml5',
            titleAttr: 'Exportar Excel',
            title: "seguimiento",
            className: "btn  btn-outline-success btn-sm"


          },
          {

            extend: 'csvHtml5',
            titleAttr: 'Exportar csv',
            className: "btn  btn-outline-warning btn-sm"
            //text: '<i class="fas fa-file-excel"></i>'

          },
          {

            extend: 'pdfHtml5',
            titleAttr: 'Exportar pdf',
            className: "btn  btn-outline-secondary btn-sm"


          }
        ],

      });

    $('#crear_radicado').click(function() {
      $('#form-general')[0].reset();
      $('.card-title').text('Agregar Nuevo Radicado');
      $('#action_button').val('Add');
      $('#action').val('Add');
      $('#form_result').html('');
      $('#modal-u').modal({
        backdrop: 'static',
        keyboard: false
      });
      $('#modal-u').modal('show');
    });

    $('#form-general').on('submit', function(event) {
      event.preventDefault();
      var url = '';
      var method = '';
      var text = '';

      if ($('#action').val() == 'Add') {
        text = "Estás por crear una Radicado"
        url = "{{route('guardar_radicado')}}";
        method = 'post';
      }
      if ($('#action').val() == 'Edit') {
        text = "Estás por actualizar una Radicado"
        var updateid = $('#hidden_id').val();
        url = "/radicados/" + updateid;
        method = 'put';
      }

      Swal.fire({
        title: "¿Estás seguro?",
        text: text,
        icon: "success",
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: 'Aceptar',
      }).then((result) => {
        if (result.value) {
          $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            dataType: "json",
            success: function(data) {
              var html = '';
              if (data.errors) {

                html =
                  '<div class="alert alert-danger alert-dismissible">' +
                  '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                  '<h5><i class="icon fas fa-ban"></i> Mensaje fidem</h5>';

                for (var count = 0; count < data.errors.length; count++) {
                  html += '<p>' + data.errors[count] + '<p>';
                }
                html += '</div>';
              }

              if (data.success == 'ok') {
                $('#form-general')[0].reset();
                $('#modal-u').modal('hide');
                $('#tradicado').DataTable().ajax.reload();
                Swal.fire({
                  icon: 'success',
                  title: 'Radicado creado correctamente',
                  showConfirmButton: false,
                  timer: 1500

                })


              } else if (data.success == 'ok1') {
                $('#form-general')[0].reset();
                $('#modal-u').modal('hide');
                $('#tradicado').DataTable().ajax.reload();
                Swal.fire({
                  icon: 'warning',
                  title: 'Radicado actualizado correctamente',
                  showConfirmButton: false,
                  timer: 1500

                })


              }
              $('#form_result').html(html)
            }


          });
        }
      });


    });

    // Edición de Radicado

    $(document).on('click', '.edit', function() {
      var id = $(this).attr('id');

      $.ajax({
        url: "/radicados/" + id + "/editar",
        dataType: "json",
        success: function(data) {
          $('#codigo').val(data.result.codigo);
          $('#nombre').val(data.result.nombre);
          $('#NIT').val(data.result.NIT);
          $('#color').val(data.result.color);
          $('#hidden_id').val(id);
          //$('.card-title').text('Editar Radicado');
          $('#action_button').val('Edit');
          $('#action').val('Edit');
          $('#modal-u').modal('show');
        }


      }).fail(function(jqXHR, textStatus, errorThrown) {

        if (jqXHR.status === 403) {

          Manteliviano.notificaciones('No tienes permisos para realizar esta accion', 'Sistema Historias Clínicas', 'warning');

        }
      });

    });


    // Función para abrir modal y agregar los Niveles, Tabla para consultar los niveles
    $(document).on('click', '.agregarnivel', function() {
      var id = $(this).attr('id');
      var nivel_idp2 = $(this).attr('id');

      if (nivel_idp2 != '') {
        $('#tniveles').DataTable().destroy();
        fill_datatable_f(nivel_idp2);
      }

      $.ajax({
        url: "/Radicado_niveles/" + id + "/editarn",
        dataType: "json",
        success: function(data) {
          $('#codigo_n').val(data.result.codigo);
          $('#nombre_n').val(data.result.nombre);
          $('#NIT_n').val(data.result.NIT);
          $('#2-radicadoss_id').val(id);
          // $('.card-title').text('Agregar Nivel');
          $('#action_button').val('Add');
          $('#action').val('Add');
          $('#modal-n').modal({
            backdrop: 'static',
            keyboard: false
          });
          $('#modal-n').modal('show');
        }

      }).fail(function(jqXHR, textStatus, errorThrown) {

        if (jqXHR.status === 403) {

          Manteliviano.notificaciones('No tienes permisos para realizar esta accion', 'Sistema Fidem', 'warning');
        }
      });

    });


   function limpiar_input_niveles() {

    $('#nivel').val('');
    $('#descripcion_nivel').val('');
    $('#regimen').val('');
    $('#tipo_recuperacion').val('');
    $('#afiliacion').val('');
    $('#servicio').val('');
    $('#vlr_copago').val('');
   }

  });


  var idioma_espanol = {
    "sProcessing": "Procesando...",
    "sLengthMenu": "Mostrar _MENU_ registros",
    "sZeroRecords": "No se encontraron resultados",
    "sEmptyTable": "Ningún dato disponible en esta tabla",
    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
    "sInfoPostFix": "",
    "sSearch": "Buscar:",
    "sUrl": "",
    "sInfoThousands": ",",
    "sLoadingRecords": "Cargando...",
    "oPaginate": {
      "sFirst": "Primero",
      "sLast": "Último",
      "sNext": "Siguiente",
      "sPrevious": "Anterior"
    },
    "oAria": {
      "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
      "sSortDescending": ": Activar para ordenar la columna de manera descendente"
    },
    "buttons": {
      "copy": "Copiar",
      "colvis": "Visibilidad"
    }
  }
</script>


@endsection
