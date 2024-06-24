import DataTable from 'datatables.net-bs5'
import { getParameterByName } from '../../codebase/utils'
import 'datatables.net-bs5/css/dataTables.bootstrap5.css'
import 'datatables.net-responsive-bs5'
import { Modal } from 'bootstrap'
import { get, post } from '../../codebase/api'
// import $ from 'jquery'

class pageOrder {
  static statusModal = null

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
            <td>${item.supplier ? item.supplier : '-'}</td>
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
        url: '/dashboard/order/list',
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
      // language: {
      //   url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json'
      // },
      initComplete: function () {
        console.log('Table initialized')
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

    tableOrders.addEventListener('dblclick', async (event) => {
      // Encontra o elemento tr mais próximo, que representa a linha da tabela
      const tr = event.target.closest('tr')

      // Verifica se encontrou a linha (tr)
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
          // Preenche o conteúdo do modal com os dados da linha clicada
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
              <div>
                <label for="employee_id" class="form-label">Vendedor:</label>
                <select name="employee_id" class="form-control" id="employee_id">
                  ${employeeOptions.map((employee) => `<option value="${employee.id}" ${employee.id === rowData.employee_id ? 'selected' : ''}>${employee.name}</option>`)}
                </select>
              </div>
              <div class="my-4">
                <button type="submit" class="btn btn-primary">Salvar</button>
              </div>
            </form>
          `
          detalhesModal.show()

          const form = document.getElementById('updateStatusForm')
          form.addEventListener('submit', async function (event) {
            event.preventDefault()

            const data = {
              step: form.querySelector('select[name="step"]').value,
              status: form.querySelector('select[name="status"]').value,
              employee_id: form.querySelector('select[name="employee_id"]').value
            }

            const orderId = form.querySelector('input[name="order_id"]').value

            const res = await post(`/api/order/${orderId}/store`, data)

            if (res.success) {
              form.reset()
              detalhesModal.hide()
              table.draw()
            }
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
