import DataTable from 'datatables.net-bs5'
import { formatDate, getParameterByName, getTomorrowDate, isValidURL } from '../../codebase/utils'
import 'datatables.net-responsive-bs5'
import 'datatables.net-bs5/css/dataTables.bootstrap5.css'
import { Modal } from 'bootstrap'
import { get, post, delete as del, put } from '../../codebase/api'
import Button from '../../codebase/components/button'
import Swal from 'sweetalert2'
import { tableIntl } from '../../codebase/constants'
// import $ from 'jquery'

class pageOrder {
  static statusModal = null
  static tableOrders = null

  static initDataTables () {
    const stepOptions = { in_design: 'Design e Artes', in_production: 'Produção', finished: 'Concluído', shipping: 'Entrega', pickup: 'Retirada', cancelled: 'Cancelado' }

    const format = (d) => {
      const hasPurchase = d.order_products.some(item => item.purchase_date !== null)

      const subTableHtml = `
      <table class='table table-bordered table-hover sub-table' data-order-id='${d.id}'>
        <tr>
          <td class='text-uppercase fw-bold'>Produto</td>
          <td class='text-uppercase fw-bold'>Quantidade</td>
          <td class='text-uppercase fw-bold'>Status</td>
          <td class='text-uppercase fw-bold'>Situação</td>
          <td class='text-uppercase fw-bold'>Etapa</td>
          ${hasPurchase ? '<td class="text-uppercase fw-bold">Data da Compra</td>' : ''}
          <td class='text-uppercase fw-bold'>Fornecedor</td>
          <td class='text-uppercase fw-bold'>Observação</td>
        </tr>
        ${d.order_products.map((item, index) => `
          <tr class='sub-table-row' data-index='${index}' data-id='${item.id}' data-order-id='${d.id}'>
            <td class='fw-bold'>${item.product.name}</td>
            <td>${item.qtd}</td>
            <td>${item.status === 'approved' ? '<span class="badge bg-success">Aprovado</span>' : item.status === 'waiting_approval' ? '<span class="badge bg-warning">Aguard. Aprov.</span>' : '<span class="badge bg-info">Aguard. Arte</span> '}</td>
            <td>${item.was_bought === 'Y' ? '<span class="badge bg-success">Comprado</span>' : (item.was_bought === 'N') && (item.in_stock === 'no' || item.in_stock === 'partial') ? '<span class="badge bg-warning">Não Comprado</span>' : '-'}</td>
            <td>${stepOptions[item.step] ? `<span class="badge bg-info">${stepOptions[item.step]}</span>` : '-'}</td>
            ${hasPurchase ? `<td>${formatDate(item.purchase_date)}</td>` : ''}
            <td>${!item.supplier ? '-' : isValidURL(item.supplier) ? `<a href="${item.supplier}" class="btn btn-sm btn-primary" target="_blank">Abrir Link</a>` : item.supplier}</td>
            <td>${item.obs ? item.obs : '-'}</td>
          </tr>`).join('')}
      </table>
    `

      return subTableHtml
    }

    const tableOrders = document.querySelector('.list-latest')
    this.tableOrders = new DataTable(tableOrders, {
      serverSide: true,
      processing: true,
      paging: true,
      pageLength: 50,
      lengthMenu: [[5, 10, 20, 40, 50, 80, 100], [5, 10, 20, 40, 50, 80, 100]],
      autoWidth: false,
      dom: "<'row mb-2'<'col-12 col-md-6'l><'col-12 col-md-6'f>>" +
           "<'row'<'col-sm-12'tr>>" +
           "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
      ajax: {
        url: '/dashboard/order/list',
        type: 'GET',
        data: (d) => {
          d.status = document.querySelector('#filterByStatus').value
          d.month = document.querySelector('#filterByMonth').value
          d.step = document.querySelector('#filterByStep').value
          d.type = document.getElementById('filterType').value
          d.date = document.getElementById('filterDate').value
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
        {
          data: 'order_products',
          name: 'order_products',
          orderable: false,
          render: function (data, type, row, meta) {
            const inProduction = []
            const inDesign = []
            const finished = []
            const shipping = []
            const pickUp = []
            data.forEach((item, index) => {
              if (item.step === 'in_production') {
                inProduction.push(item)
              } else if (item.step === 'in_design') {
                inDesign.push(item)
              } else if (item.step === 'finished') {
                finished.push(item)
              } else if (item.step === 'shipping') {
                shipping.push(item)
              } else if (item.step === 'píckup') {
                pickUp.push(item)
              }
            })

            const html = `
              ${inProduction.length > 0 ? `<span class="badge bg-warning">${inProduction.length} - Em Produção</span>` : ''}
              ${inDesign.length > 0 ? `<span class="badge bg-info">${inDesign.length} - Design e Artes</span>` : ''}
              ${finished.length > 0 ? `<span class="badge bg-success">${finished.length} - Concluído</span>` : ''}
              ${shipping.length > 0 ? `<span class="badge bg-secondary">${shipping.length} - Entrega</span>` : ''}
              ${pickUp.length > 0 ? `<span class="badge bg-primary">${pickUp.length} - Retirada</span>` : ''}
            `

            return html
          }
        },
        {
          data: 'employee.name',
          name: 'employee.name',
          orderable: false,
          render: (data, type, row) => {
            return data || '-'
          }
        },
        { data: 'delivery_date', name: 'delivery_date' },
        {
          data: 'action',
          name: 'action',
          orderable: false,
          searchable: false
        }
      ],
      language: tableIntl,
      drawCallback: function () {
        const api = this.api()

        api.rows().every(function () {
          const data = this.data()
          document.querySelector(`#delete-order-${data.id}`).addEventListener('click', () => pageOrder.deleteOrder(data.id))
          return true
        })
      }
    })

    const table = this.tableOrders

    document.querySelector('#filterByStatus').addEventListener('change', () => {
      table.draw()
    })

    document.querySelector('#filterByStep').addEventListener('change', () => {
      table.draw()
    })

    document.querySelector('#filterByMonth').addEventListener('change', () => {
      table.draw()
    })

    document.querySelector('#filterType').addEventListener('change', () => {
      if (document.getElementById('filterDate').value !== '') {
        console.log('date')
        table.draw()
      }
    })

    document.getElementById('filterDate').addEventListener('change', () => {
      if (document.getElementById('filterType').value === '') {
        Swal.fire({
          icon: 'info',
          title: 'Atenção',
          text: 'Selecione o tipo de data',
          confirmButtonText: 'OK'
        })
      } else {
        table.draw()
      }
    })

    document.querySelector('#btnCleanFilters').addEventListener('click', () => {
      document.querySelector('#filterByStatus').value = 'all'
      document.querySelector('#filterByMonth').value = 'all'
      document.querySelector('#filterByStep').value = 'all'
      document.getElementById('filterType').value = ''
      document.getElementById('filterDate').value = ''
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
        const escapedId = id.replace(/^(\d)/, '\\3$1 ')
        document.querySelectorAll(`#${escapedId} td.dt-control`).forEach(element => element.click())
      })
    })

    tableOrders.addEventListener('dblclick', async (event) => {
      const tr = event.target.closest('tr')

      // Verifica se a linha clicada pertence à subtabela
      if (tr && tr.classList.contains('sub-table-row')) {
        const orderId = tr.getAttribute('data-order-id')
        const productId = tr.getAttribute('data-id')

        const res = await get(`/api/purchase/${orderId}/product/${productId}/show`)
        Swal.fire({
          icon: 'info',
          title: 'Detalhes do item',
          html: `
    <strong>Status</strong>: ${res.data.in_stock === 'yes' ? '-' : res.data.was_bought === 'Y' ? '<span class="badge bg-success">Comprado</span>' : '<span class="badge bg-warning">Não Comprado</span>'} <br><br>
        ${res.data.was_bought === 'Y'
            ? `
                  <strong>Data da Compra</strong>: ${res.data.purchase_date ? res.data.purchase_date : '-'} <br><br>
                  <strong>Previsão de Entrega</strong>: ${res.data.arrival_date ? res.data.arrival_date : '-'} <br><br>
                  <strong>Produto Chegou?</strong>: ${res.data.arrived === 'Y' ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>'}<br><br>
                  ${res.data.arrived === 'Y' ? `<strong>Data da Chegada</strong>: ${res.data.delivered_date ? res.data.delivered_date : '-'}` : ''}
                `
            : ''}
              `
        })
        return
      }

      // Verifica se a linha clicada não pertence à subtabela
      if (tr) {
        const rowData = table.row(tr).data()

        if (rowData) {
          if (!pageOrder.statusModal) {
            pageOrder.statusModal = new Modal(document.getElementById('detalhesModal'))
          }

          let employeeOptions = []

          await get('/api/user/employees').then((response) => {
            employeeOptions = response
          })

          const detalhesModal = pageOrder.statusModal
          const modalTitle = document.getElementById('detalhesModal').querySelector('.block-title')
          const modalBody = document.getElementById('detalhesModal').querySelector('.block-content')

          modalTitle.textContent = `Alterar informações do pedido: #${rowData.number}`
          modalBody.innerHTML = `
            <form id="updateStatusForm" action="">
              <input type="hidden" name="order_id" value="${rowData.id}" />
              <div class="mb-3">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col">Produto</th>
                      <th scope="col">Etapa</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${rowData.order_products.map((item, index) => `
                    <tr>
                      <td>${item.product.name}</td>
                      <td>
                        <input type="hidden" name="order_products[${index}][id]" value="${item.id}">
                        <select name="order_products[${index}][step]" class="form-control">
                          <option value="">Não definido</option>
                          ${Object.keys(stepOptions).map((key) => `
                          <option value="${key}" ${key === item.step ? 'selected' : ''}>${stepOptions[key]}</option>`).join('')}
                        </select>
                      </td>
                    </tr>`)}
                  </tbody>
                </table>
              </div>
              <div class="mb-3">
                <label for="employee_id" class="form-label">Vendedor:</label>
                <select name="employee_id" class="form-control" id="employee_id">
                  ${employeeOptions.map((employee) => `<option value="${employee.id}" ${employee.id === rowData.employee_id ? 'selected' : ''}>${employee.name}</option>`)}
                </select>
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

          document.getElementById('btn-submit-container').appendChild(btnSubmit.render())
          document.getElementById('btn-cancel-container').appendChild(btnCancel.render())

          detalhesModal.show()

          const form = document.getElementById('updateStatusForm')
          form.addEventListener('submit', async function (event) {
            event.preventDefault()

            btnSubmit.setLoading(true)

            const form = event.target
            const formData = new FormData(form)
            const orderId = form.querySelector('input[name="order_id"]').value

            const data = {}

            formData.forEach((value, key) => {
              const keys = key.match(/[^[\]]+/g)
              if (keys.length > 1) {
                if (!data[keys[0]]) {
                  data[keys[0]] = []
                }
                if (!data[keys[0]][keys[1]]) {
                  data[keys[0]][keys[1]] = {}
                }
                data[keys[0]][keys[1]][keys[2]] = value
              } else {
                data[keys[0]] = value
              }
            })

            const res = await put(`/api/order/${orderId}`, data)

            if (res.success) {
              btnSubmit.setLoading(false)
              form.reset()
              detalhesModal.hide()
              table.draw()
            }
          })

          btnCancel.setOnClick(() => {
            form.reset()
            detalhesModal.hide()
          })
        }
      }
    })
  }

  static deleteOrder (id) {
    const table = this.tableOrders

    Swal.fire({
      title: 'Tem certeza que deseja excluir este pedido?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sim, excluir!',
      cancelButtonText: 'Cancelar'
    }).then(async (result) => {
      if (result.isConfirmed) {
        const res = await del(`/api/order/${id}`)

        if (res.success) {
          table.draw()
        }
      }
    })
  }

  static checkStatusOnUrl () {
    const status = getParameterByName('status')
    const step = getParameterByName('step')
    const type = getParameterByName('type')

    if (status) {
      const statusElement = document.querySelector('#filterByStatus')
      statusElement.value = status
      const event = new Event('change')
      statusElement.dispatchEvent(event)
    }

    if (step) {
      const stepElement = document.querySelector('#filterByStep')
      stepElement.value = step
      const event = new Event('change')
      stepElement.dispatchEvent(event)
    }

    if (type) {
      document.querySelector('#filterDate').value = getTomorrowDate()

      const typeElement = document.getElementById('filterType')
      typeElement.value = 'delivery_date'
      const event = new Event('change')
      typeElement.dispatchEvent(event)
    }
  }

  static init () {
    this.initDataTables()
    this.checkStatusOnUrl()
  }
}

window.Codebase.onLoad(() => pageOrder.init())
window.Codebase.helpersOnLoad(['jq-datepicker'])
