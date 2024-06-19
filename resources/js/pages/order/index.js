import { getParameterByName } from "../../codebase/utils";

class pageOrder {

	static initDataTables() {


		// Override a few default classes
    jQuery.extend(jQuery.fn.dataTable.ext.classes, {
      sWrapper: "dataTables_wrapper dt-bootstrap5",
      sFilterInput: "form-control",
      sLengthSelect: "form-select"
    });

    // Override a few defaults
    jQuery.extend(true, jQuery.fn.dataTable.defaults, {
      language: {
        lengthMenu: "_MENU_",
        search: "_INPUT_",
        searchPlaceholder: "Pesquisar..",
        processing: "Processando...",
        info: "Página <strong>_PAGE_</strong> de <strong>_PAGES_</strong>",
        paginate: {
          first: '<i class="fa fa-angle-double-left"></i>',
          previous: '<i class="fa fa-angle-left"></i>',
          next: '<i class="fa fa-angle-right"></i>',
          last: '<i class="fa fa-angle-double-right"></i>'
        },
        emptyTable: "Nenhum registro encontrado",
        buttons: {
          copy: "Copiar",
          print: "Imprimir"
        },
        infoFiltered: "(Filtrados de _MAX_ registros)",
        infoEmpty: "Mostrando 0 a 0 de 0 registros",
      }
    });

    // Override buttons default classes
    jQuery.extend(true, jQuery.fn.DataTable.Buttons.defaults, {
      dom: {
        button: {
          className: 'btn btn-sm btn-primary'
        },
      }
    });

    function format(d) {
      return (
        `<table class='table table-bordered table-hover'><tr>
            <td class='text-uppercase'>Produto</td>
            <td class='text-uppercase'>Quantidade</td>
          </tr>` +
          d.order_products.map((item) => {
            return `<tr>
              <td class='fw-bold text-uppercase'>${item.name}</td>
              <td>${item.qtd}</td>
            </tr>`
          })
        + `</table>`
      );
    }

    // Init DataTable with Buttons
    const table = jQuery('.list-latest').DataTable({
      serverSide: true,
      processing: true,
      paging: true,
      pageLength: 50,
      lengthMenu: [[5, 10, 20, 40, 50, 80, 100], [5, 10, 20, 40, 50, 80, 100]],
      autoWidth: false,
      // buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
      //"<'row'<'col-sm-12'<'text-center bg-body-light py-2 mb-2'B>>>" +
      dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
      ajax: {
        url: '/dashboard/order/list',
        type: 'GET',
        data: function (d) {
          d.status = $('#filterByStatus option:selected').val();
          d.month = $('#filterByMonth option:selected').val();
          d.from = $('#from').val();
          d.to = $('#to').val();
        },
      },
      drawCallback: function (settings) {
      },
      columns: [
        {
          class: 'dt-control',
          orderable: false,
          data: null,
          defaultContent: ''
        },
        { data: 'date', name: 'date' },
        { data: 'number', name: 'number' },
        { data: 'customer.name', name: 'customer.name' },
        { data: 'step', name: 'step' },
        { data: 'employee.name', name: 'employee.name', render: function (data, type, row) {
          return data ? data : '-';
        }},
        { data: 'arrived', name: 'arrived' },
        { data: 'delivery_date', name: 'delivery_date' },
        { data: 'action', name: 'action', orderable: false, searchable: false}
      ],
    });

    jQuery('#filterByStatus').on('change', function () {
      table.draw();
    });

    jQuery('#filterByMonth').on('change', function () {
      table.draw();
    });

    jQuery('#btnCleanFilters').on('click', function () {
      $('#filterByStatus').val('all');
      $('#filterByMonth').val('all');
      $('#from').val('');
      $('#to').val('');
      table.draw();
    });

    jQuery('#from').on('change', function () {
      table.draw();
    });

    jQuery('#to').on('change', function () {
      table.draw();
    });


    const detailRows = [];

    table.on('click', 'tbody td.dt-control', function () {
      const tr = $(this).closest('tr');
      const row = table.row(tr);
      const idx = $.inArray(tr.attr('id'), detailRows);

      if (row.child.isShown()) {
        tr.removeClass('details');
        row.child.hide();

        detailRows.splice(idx, 1);
      } else {
        tr.addClass('details');
        row.child(format(row.data())).show();

        if (idx === -1) {
          detailRows.push(tr.attr('id'));
        }
      }
    });

    table.on('draw', function () {
      $.each(detailRows, function (i, id) {
        $('#' + id + ' td.dt-control').trigger('click');
      });
    });

	}

  static checkStatusOnUrl() {
    const status = getParameterByName('status');
    if (status) {
      jQuery('#filterByStatus').val(status).trigger('change');
    }
  }

	/*
	 * Init functionality
	 *
	 */
	static init() {
		this.initDataTables();
    this.checkStatusOnUrl();
	}
}

Codebase.onLoad(() => pageOrder.init());
Codebase.helpersOnLoad(['jq-datepicker']);
