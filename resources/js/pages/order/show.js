class pageShowOrder {

  static initPage() {

      const orderId = jQuery('meta[name="order-id"]').attr('content');
      const url = jQuery('meta[name="base-url"]').attr('content');

      jQuery('#upload-preview').submit(function(e) {
          e.preventDefault();

          $.ajax({
              url: url + '/dashboard/order/' + orderId + '/upload/preview',
              method: "POST",
              data: new FormData(this),
              dataType: 'JSON',
              contentType: false,
              cache: false,
              processData: false,
              beforeSend: function() {
                  $('#loading-preview').show();
                  $('#upload-preview').attr('disabled', 'disabled');
              },
              success: function(data) {
                  location.reload();
              },
              error: function(data) {
                  $('#loading-preview').hide();
                  $('#error-msg').html(
                      '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                      '<strong>' + data.responseJSON.message + '</strong>' +
                      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                      '</div>'
                  );
                  // $('#upload-preview')[0].reset();
                  // $('#upload-preview').attr('disabled', false);
              }
          })
      });

      $('#upload-design').submit(function(e) {
          e.preventDefault();
          $.ajax({
              url: url + '/dashboard/order/' + orderId + '/upload/design',
              method: "POST",
              data: new FormData(this),
              dataType: 'JSON',
              contentType: false,
              cache: false,
              processData: false,
              beforeSend: function() {
                  $('#loading-design').show();
                  $('#upload-design').attr('disabled', 'disabled');
              },
              success: function(data) {
                  location.reload();
              },
              error: function(data) {
                  $('#loading-design').hide();
                  $('#error-msg').html(
                      '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                      '<strong>' + data.responseJSON.message + '</strong>' +
                      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                      '</div>'
                  );
              }
          })
      });

      $('.dropdown-item.status').click(function() {
          var status = $(this).data('value');
          $.ajax({
              url: url + '/dashboard/order/' + orderId + '/update/status',
              method: "POST",
              data: {
                  _token: $('meta[name="csrf-token"]').attr('content'),
                  status: status
              },
              dataType: 'JSON',
              beforeSend: function() {
                  $('#status-dropdown').html(
                      '<div class="spinner-border spinner-border-sm text-white" role="status">' +
                      '<span class="visually-hidden">Loading...</span>' +
                      '</div>'
                  );
              },
              success: function(data) {
                  $('#status-dropdown').html('');
                  $('#status-dropdown').html(
                    '<i class="fa fa-fw fa-chevron-down text-white me-1"></i>' +
                    '<span class="d-none d-sm-inline">' + data.status +
                    '</span>'
                  );
                  Codebase.helpers('jq-notify', {
                    align: 'right',
                    from: 'top',
                    type: 'success',
                    icon: 'fa fa-info me-5',
                    message: data.message
                  });
              },
              error: function(data) {
                  console.log(data);
              }
          })
      });

      $('.dropdown-item.designer').click(function() {
          var designer = $(this).data('value');
          $.ajax({
              url: url + '/dashboard/order/' + orderId + '/update/designer',
              method: "POST",
              data: {
                  _token: $('meta[name="csrf-token"]').attr('content'),
                  designer: designer
              },
              dataType: 'JSON',
              beforeSend: function() {
                  $('#designer-dropdown').html(
                      '<div class="spinner-border spinner-border-sm text-white" role="status">' +
                      '<span class="visually-hidden">Loading...</span>' +
                      '</div>'
                  );
              },
              success: function(data) {
                  $('#designer-dropdown').html('');
                  $('#designer-dropdown').html(
                      '<i class="fa fa-fw fa-chevron-down text-white me-1"></i>' +
                      '<span class="d-none d-sm-inline">' + data.employee.name +
                      '</span>'
                  );
                  Codebase.helpers('jq-notify', {
                    align: 'right',
                    from: 'top',
                    type: 'success',
                    icon: 'fa fa-info me-5',
                    message: data.message
                });
              },
              error: function(data) {
                  console.log(data);
              }
          })
      });

      $('.dropdown-item.arrived').click(function() {
          var status = $(this).data('value');
          $.ajax({
              url: url + '/dashboard/order/' + orderId + '/update/arrived',
              method: "POST",
              data: {
                  _token: $('meta[name="csrf-token"]').attr('content'),
                  arrived: status
              },
              dataType: 'JSON',
              beforeSend: function() {
                  $('#arrived-dropdown').html(
                      '<div class="spinner-border spinner-border-sm text-white" role="status">' +
                      '<span class="visually-hidden">Loading...</span>' +
                      '</div>'
                  );
              },
              success: function(data) {
                  location.reload();
              },
              error: function(data) {
                  console.log(data);
                  // $('#status-dropdown').html(
                  //     '<i class="fa fa-fw fa-chevron-down text-white me-1"></i>' +
                  //     '<span class="d-none d-sm-inline">Alterar Status</span>'
                  // );
              }
          })
      });
  }

  static init() {
      this.initPage();
  }
}

Codebase.onLoad(() => pageShowOrder.init());
Codebase.helpersOnLoad(['jq-notify']);
