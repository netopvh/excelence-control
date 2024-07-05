import DataTable from 'datatables.net-bs5'
import { getParameterByName, isValidURL } from '../../codebase/utils'
import 'datatables.net-responsive-bs5'
import 'datatables.net-bs5/css/dataTables.bootstrap5.css'
import { Modal } from 'bootstrap'
import { get, post } from '../../codebase/api'
import Button from '../../codebase/components/button'
import Swal from 'sweetalert2'
// import $ from 'jquery'

class pageOrder {
  static statusModal = null

  static initDataTables () {
    const stepOptions = { in_design: 'Design e Artes', in_production: 'Produção', finished: 'Concluído', shipping: 'Entrega', pickup: 'Retirada', cancelled: 'Cancelado' }
    const statusOptions = { approved: 'Aprovado', waiting_approval: 'Aguard. Aprov.', waiting_design: 'Aguard. Arte' }

    const format = (d) => {
      const subTableHtml = `
      <table class='table table-bordered table-hover sub-table' data-order-id='${d.id}'>
        <tr>
          <td class='text-uppercase fw-bold'>Produto</td>
          <td class='text-uppercase fw-bold'>Quantidade</td>
          <td class='text-uppercase fw-bold'>Status</td>
          <td class='text-uppercase fw-bold'>Fornecedor</td>
          <td class='text-uppercase fw-bold'>Observação</td>
        </tr>
        ${d.order_products.map((item, index) => `
          <tr class='sub-table-row' data-index='${index}' data-id='${item.id}' data-order-id='${d.id}'>
            <td class='fw-bold'>${item.product.name}</td>
            <td>${item.qtd}</td>
            <td>${item.was_bought === 'Y' ? '<span class="badge bg-success">Comprado</span>' : '-'}</td>
            <td>${!item.supplier ? '-' : isValidURL(item.supplier) ? `<a href="${item.supplier}" class="btn btn-sm btn-primary" target="_blank">Abrir Link</a>` : item.supplier}</td>
            <td>${item.obs ? item.obs : '-'}</td>
          </tr>`).join('')}
      </table>
    `

      return subTableHtml
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
        url: '/dashboard/order/list',
        type: 'GET',
        data: (d) => {
          d.status = document.querySelector('#filterByStatus').value
          d.month = document.querySelector('#filterByMonth').value
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
          data: 'step',
          name: 'step',
          type: 'select',
          render: function (data, type, row, meta) {
            if (data == null || !(data in stepOptions)) return '<span class="badge bg-secondary">Não definido</span>'
            if (data === 'in_production') return '<span class="badge bg-warning">' + stepOptions[data] + '</span>'
            if (data === 'in_design') return '<span class="badge bg-corporate">' + stepOptions[data] + '</span>'
            if (data === 'finished') return '<span class="badge bg-success">' + stepOptions[data] + '</span>'
            if (data === 'shipping') return '<span class="badge bg-earth">' + stepOptions[data] + '</span>'
            if (data === 'píckup') return '<span class="badge bg-elegance">' + stepOptions[data] + '</span>'
            return stepOptions[data]
          }
        },
        {
          data: 'employee.name',
          name: 'employee.name',
          render: (data, type, row) => {
            return data || '-'
          }
        },
        {
          data: 'status',
          name: 'status',
          type: 'select',
          render: function (data, type, row, meta) {
            if (data == null || !(data in statusOptions)) return '<span class="badge bg-secondary">Não definido</span>'
            if (data === 'approved') return '<span class="badge bg-success">' + statusOptions[data] + '</span>'
            if (data === 'waiting_approval') return '<span class="badge bg-warning">' + statusOptions[data] + '</span>'
            if (data === 'waiting_design') return '<span class="badge bg-corporate">' + statusOptions[data] + '</span>'
            return stepOptions[data]
          }
        },
        { data: 'delivery_date', name: 'delivery_date' },
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

    document.querySelector('#filterType').addEventListener('change', () => {
      if (document.getElementById('filterDate').value !== '') {
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
        document.querySelector(`#${id} td.dt-control`).click()
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
          <strong>Status</strong>: ${res.data.was_bought === 'Y' ? '<span class="badge bg-success">Comprado</span>' : '-'} <br><br>
          <strong>Previsão de Entrega</strong>: ${res.data.arrival_date ? res.data.arrival_date : '-'}
          `
        })
        return
      }

      // Verifica se a linha clicada não pertence à subtabela
      if (tr) {
        // Obtém os dados da linha utilizando DataTable
        const rowData = table.row(tr).data()

        // Verifica se os dados foram encontrados
        if (rowData) {
          // Aqui você pode abrir o modal e preencher com os dados da linha clicada
          // Supondo que o modal tenha o ID 'detalhesModal'
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
                <label for="step" class="form-label">Etapa:</label>
                <select name="step" class="form-control">
                  <option value="">Não definido</option>
                  ${Object.keys(stepOptions).map((key) => `<option value="${key}" ${key === rowData.step ? 'selected' : ''}>${stepOptions[key]}</option>`)}
                </select>
              </div>
              <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select name="status" class="form-control">
                  ${Object.keys(statusOptions).map((key) => `<option value="${key}" ${key === rowData.status ? 'selected' : ''}>${statusOptions[key]}</option>`)}
                </select>
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

            const data = {
              step: form.querySelector('select[name="step"]').value,
              status: form.querySelector('select[name="status"]').value,
              employee_id: form.querySelector('select[name="employee_id"]').value
            }

            const orderId = form.querySelector('input[name="order_id"]').value

            const res = await post(`/api/order/${orderId}/store`, data)

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

window.Codebase.onLoad(() => pageOrder.init())
window.Codebase.helpersOnLoad(['jq-datepicker'])
