import DataTable from 'datatables.net-bs5'
import { getParameterByName, isValidURL } from '../../codebase/utils'
import 'datatables.net-responsive-bs5'
import { Modal } from 'bootstrap'
import { get, post } from '../../codebase/api'
// import $ from 'jquery'

class pagePurchase {
  static initDataTables () {
    const stepOptions = { in_design: 'Design e Artes', in_production: 'Produção', finished: 'Concluído', shipping: 'Entrega', pickup: 'Retirada', cancelled: 'Cancelado' }
    const statusOptions = { approved: 'Aprovado', waiting_approval: 'Aguard. Aprov.', waiting_design: 'Aguard. Arte' }

    const format = (d) => {
      return (
        `<table class='table table-bordered table-hover'><tr>
          <td class='text-uppercase fw-bold'>Produto</td>
          <td class='text-uppercase fw-bold'>Quantidade</td>
          <td class='text-uppercase fw-bold'>Estoque</td>
          <td class='text-uppercase fw-bold'>Fornecedor</td>
          <td class='text-uppercase fw-bold'>Observação</td>
        </tr>` +
        d.order_products.map((item) => {
          return `<tr>
            <td class='fw-bold'>${item.product.name}</td>
            <td>${item.qtd}</td>
            <td>${item.in_stock === 'yes' ? '<span class="badge bg-success">Sim</span>' : item.in_stock === 'no' ? '<span class="badge bg-warning">Não</span>' : item.in_stock === 'partial' ? '<span class="badge bg-info">Parcial</span>' : '-'}</td>
            <td>${!item.supplier ? '-' : isValidURL(item.supplier) ? `<a href="${item.supplier}" class="btn btn-sm btn-primary" target="_blank">Abrir Link</a>` : item.supplier}</td>
            <td>${item.obs ? item.obs : '-'}</td>
          </tr>`
        }).join('') +
        '</table>'
      )
    }

    const tableOrders = document.querySelector('.list-latest')
    const table = new DataTable(tableOrders, {
      serverSide: true,
      processing: true,
      paging: true,
      pageLength: 50,
      lengthMenu: [[5, 10, 20, 40, 50, 80, 100], [5, 10, 20, 40, 50, 80, 100]],
      autoWidth: false,
      dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
           "<'row'<'col-sm-12'tr>>" +
           "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
      ajax: {
        url: '/api/purchase',
        type: 'GET',
        data: (d) => {
          d.status = document.querySelector('#filterByStatus').value
          d.month = document.querySelector('#filterByMonth').value
          d.from = document.querySelector('#from').value
          d.to = document.querySelector('#to').value
        }
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
        { data: 'delivery_date', name: 'delivery_date' },
        {
          data: 'employee.name',
          name: 'employee.name',
          render: (data, type, row) => {
            return data || '-'
          }
        },
        { data: 'action', name: 'action', orderable: false, searchable: false }
      ],
      language: {
        url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json'
      }
    })

    document.querySelector('#filterByStatus').addEventListener('change', () => {
      table.draw()
    })

    document.querySelector('#filterByMonth').addEventListener('change', () => {
      table.draw()
    })

    document.querySelector('#btnCleanFilters').addEventListener('click', () => {
      document.querySelector('#filterByStatus').value = 'all'
      document.querySelector('#filterByMonth').value = 'all'
      document.querySelector('#from').value = ''
      document.querySelector('#to').value = ''
      table.draw()
    })

    document.querySelector('#from').addEventListener('change', () => {
      table.draw()
    })

    document.querySelector('#to').addEventListener('change', () => {
      table.draw()
    })

    const detailRows = []

    tableOrders.addEventListener('click', (event) => {
      if (event.target.closest('td.dt-control')) {
        const tr = event.target.closest('tr')
        const row = table.row(tr)
        const rowId = tr.getAttribute('id')
        const idx = detailRows.indexOf(rowId)

        if (row.child.isShown()) {
          tr.classList.remove('details')
          row.child.hide()
          detailRows.splice(idx, 1)
        } else {
          tr.classList.add('details')
          row.child(format(row.data())).show()
          if (idx === -1) {
            detailRows.push(rowId)
          }
        }
      }
    })

    table.on('draw', () => {
      detailRows.forEach((id) => {
        document.querySelector(`#${id} td.dt-control`).click()
      })
    })
  }

  static checkStatusOnUrl () {
    const status = getParameterByName('status')
    if (status) {
      const statusElement = document.querySelector('#filterByStatus')
      statusElement.value = status
      const event = new Event('change')
      statusElement.dispatchEvent(event)
    }
  }

  static init () {
    this.initDataTables()
    this.checkStatusOnUrl()
  }
}

window.Codebase.onLoad(() => pagePurchase.init())
window.Codebase.helpersOnLoad(['jq-datepicker'])
