import DataTable from 'datatables.net-bs5'
import 'datatables.net-responsive-bs5'
import 'datatables.net-bs5/css/dataTables.bootstrap5.css'
import { tableIntl } from '../../codebase/constants'
import { convertToDatetimeLocal, formatDate, showErrors, skeletonLoading } from '../../codebase/utils'
import { Modal } from 'bootstrap'
import { get, post } from '../../codebase/api'
import Button from '../../codebase/components/button'

class pageProduction {
  static tableProduction = null
  static modalProductionItem = null

  static initDataTables () {
    const detailRows = []

    const format = (d) => {
      const hasPurchase = d.order_products.some(item => item.purchase_date !== null)
      const hasDelivered = d.order_products.some(item => item.delivered_date !== null)

      return (
        `<table class='table table-bordered table-hover sub-table' data-order-id='${d.id}'>
        <tr>
          <td class='text-uppercase fw-bold'>Produto</td>
          <td class='text-uppercase fw-bold'>Quantidade</td>
          <td class='text-uppercase fw-bold'>Status</td>
          ${hasPurchase ? '<td class="text-uppercase fw-bold">Data Compra</td>' : ''}
          ${hasPurchase ? '<td class="text-uppercase fw-bold">Previsão Entrega</td>' : ''}
          ${hasPurchase ? '<td class="text-uppercase fw-bold">Chegou</td>' : ''}
          ${hasDelivered ? '<td class="text-uppercase fw-bold">Data Chegada</td>' : ''}
        </tr>` +
        d.order_products.map((item, index) => {
          return `
          <tr class='sub-table-row' data-index='${index}' data-id='${item.id}' data-order-id='${d.id}'>
            <td class='fw-bold'>${item.product.name}</td>
            <td>${item.qtd}</td>
            <td>${item.was_bought === 'Y' ? '<span class="badge bg-success">Comprado</span>' : '<span class="badge bg-warning">Não Comprado</span>'}</td>
            ${hasPurchase ? `<td>${formatDate(item.purchase_date)}</td>` : ''}
            ${hasPurchase ? `<td>${formatDate(item.arrival_date, false)}</td>` : ''}
            ${hasPurchase ? `<td>${item.arrived === 'Y' ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-warning">Não</span>'}</td>` : ''}
            ${hasDelivered ? `<td>${formatDate(item.delivered_date)}</td>` : ''}
          </tr>`
        }).join('') +
        '</table>'
      )
    }

    const tableProduction = document.querySelector('.list-production')
    this.tableProduction = new DataTable(tableProduction, {
      serverSide: true,
      processing: true,
      paging: true,
      pageLength: 50,
      lengthMenu: [[5, 10, 20, 40, 50, 80, 100], [5, 10, 20, 40, 50, 80, 100]],
      autoWidth: false,
      dom: "<'row mb-2'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
           "<'row'<'col-sm-12'tr>>" +
           "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
      ajax: {
        url: '/api/production',
        type: 'GET',
        data: (d) => {
          d.step = document.querySelector('#filterByStep').value
        }
      },
      columnDefs: [
        {
          targets: 0,
          className: 'dt-control',
          searchable: false,
          orderable: false,
          data: null,
          defaultContent: ''
        },
        { targets: 1, data: 'date', name: 'date', title: 'Data' },
        { targets: 2, data: 'number', name: 'number', title: 'Pedido' },
        { targets: 3, data: 'customer.name', name: 'customer.name', title: 'Cliente' },
        { targets: 4, data: 'delivery_date', name: 'delivery_date', title: 'Entrega do Pedido' },
        { targets: 5, data: 'employee.name', name: 'employee.name', title: 'Vendedor' },
        { targets: 6, data: 'action', name: 'action', title: 'Ações' }
      ],
      columns: [
        {
          className: 'dt-control',
          orderable: false,
          data: null,
          defaultContent: ''
        },
        {
          data: 'date'
        },
        { data: 'number', name: 'number' },
        { data: 'customer.name', name: 'customer.name' },
        { data: 'delivery_date', name: 'delivery_date' },
        {
          data: 'employee.name',
          name: 'employee.name',
          orderable: false,
          render: (data, type, row) => {
            return data || '-'
          }
        },
        {
          data: 'action',
          name: 'action',
          orderable: false,
          searchable: false,
          render: (data) => {
            return `<button type="button" class="btn btn-sm btn-primary" data-id="${data}" id="show-order-${data}">
           <i class="fa fa-eye"></i>
          </button>`
          }
        }
      ],
      language: tableIntl,
      order: [[1, 'desc']]
    })

    const table = this.tableProduction

    document.querySelector('#filterByStep').addEventListener('change', () => {
      table.draw()
    })

    tableProduction.addEventListener('click', (event) => {
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

    tableProduction.addEventListener('dblclick', async (event) => {
      const tr = event.target.closest('tr')

      if (tr && tr.classList.contains('sub-table-row')) {
        const orderId = tr.getAttribute('data-order-id')
        const productId = tr.getAttribute('data-id')
        const modalEl = document.getElementById('productionProductModal')

        if (!pageProduction.modalProductionItem) {
          pageProduction.modalProductionItem = new Modal(modalEl)
        }

        const modalTitle = modalEl.querySelector('.block-title')
        const modalBody = modalEl.querySelector('.block-content')

        modalTitle.innerHTML = 'Informações da Pedido'

        pageProduction.modalProductionItem.show()

        modalBody.innerHTML = ''
        modalBody.appendChild(skeletonLoading(3, 5))

        try {
          const res = await get(`/api/order/${orderId}/item/${productId}`)

          if (res.success) {
            modalBody.innerHTML = `
              <form id="updateProductionItemForm" action="">
                <input type="hidden" name="order_id" value="${orderId}" />
                <input type="hidden" name="order_product_id" value="${productId}" />
                <div class="mb-3">
                  <label for="arrived" class="form-label">Chegou:</label>
                  <select name="arrived" class="form-control" id="arrived">
                    ${['N', 'Y'].map((key) => `<option value="${key}" ${key === res.data.arrived ? 'selected' : ''}>${key === 'Y' ? 'Sim' : 'Não'}</option>`)}
                  </select>
                </div>
                <div class="mb-3 d-none" id="delivered-date-container">
                  <label for="delivered_date" class="form-label">Data de Chegada:</label>
                  <input type="datetime-local" class="form-control" name="delivered_date" id="delivered_date" value="${res.data.delivered_date ? convertToDatetimeLocal(res.data.delivered_date) : ''}" />
                </div>
                <div class="d-flex gap-2 mb-4">
                  <div class="col-12 col-md-6" id="btn-submit-container">
                  </div>
                  <div class="col-12 col-md-6" id="btn-cancel-container">
                  </div>
                </div>
              </form>
            `

            const btnSubmit = new Button('Salvar', null, 'btn btn-primary w-100', 'submit')
            const btnCancel = new Button('Cancelar', null, 'btn btn-danger w-100')
            const deliveredDateContainer = document.getElementById('delivered-date-container')
            const selectArrived = document.getElementById('arrived')
            const deliveredDate = document.getElementById('delivered_date')

            document.getElementById('btn-submit-container').appendChild(btnSubmit.render())
            document.getElementById('btn-cancel-container').appendChild(btnCancel.render())

            const form = document.getElementById('updateProductionItemForm')
            form.addEventListener('submit', async function (event) {
              event.preventDefault()

              btnSubmit.setLoading(true)

              const data = {
                arrived: form.querySelector('select[name="arrived"]').value,
                delivered_date: form.querySelector('input[name="delivered_date"]').value,
                user_id: document.querySelector('meta[name="user"]').content
              }

              const orderId = form.querySelector('input[name="order_id"]').value
              const orderProductId = form.querySelector('input[name="order_product_id"]').value

              try {
                const res = await post(`/api/production/${orderId}/item/${orderProductId}`, data)

                if (res.success) {
                  btnSubmit.setLoading(false)
                  table.draw()
                }

                pageProduction.modalProductionItem.hide()
              } catch (error) {
                console.log(error)
                showErrors(error.data)
              }
            })

            if (selectArrived.value === 'Y') {
              deliveredDateContainer.classList.remove('d-none')
            }

            selectArrived.addEventListener('change', () => {
              if (selectArrived.value === 'Y') {
                deliveredDateContainer.classList.remove('d-none')
              } else {
                deliveredDateContainer.classList.add('d-none')
              }
            })

            btnCancel.setOnClick(function () {
              pageProduction.modalProductionItem.hide()
            })
          }
        } catch (error) {
          console.log(error)
        }
      }
    })

    table.on('draw', () => {
      detailRows.forEach((id) => {
        const escapedId = id.replace(/^(\d)/, '\\3$1 ')
        document.querySelectorAll(`#${escapedId} td.dt-control`).forEach(element => element.click())
      })
    })
  }

  static init () {
    this.initDataTables()
  }
}

window.Codebase.onLoad(() => pageProduction.init())
