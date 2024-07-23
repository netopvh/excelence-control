import DataTable from 'datatables.net-bs5'
import { convertDateToISO, convertToDatetimeLocal, delParameterByName, formatDate, getParameterByName, isValidURL, skeletonLoading } from '../../codebase/utils'
import 'datatables.net-responsive-bs5'
import 'datatables.net-bs5/css/dataTables.bootstrap5.css'
import { Tooltip } from 'bootstrap'
import Dialog from '../../codebase/components/modal'
import { get, post, put } from '../../codebase/api'
import Swal from 'sweetalert2'
import Button from '../../codebase/components/button'
import { tableIntl } from '../../codebase/constants'

class PagePurchase {
  static tablePurchases = null
  static modalPurchase = null
  static modalPurchaseItem = null
  static detailRows = []

  static init () {
    this.initDataTables()
    this.initFilters()
    this.checkStatusOnUrl()
  }

  static initDataTables () {
    const tableOrders = document.querySelector('.list-latest')
    this.tablePurchases = new DataTable(tableOrders, {
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
        url: '/api/purchase',
        type: 'GET',
        data: (d) => {
          d.status = document.querySelector('#filterByStatus').value
          d.month = document.querySelector('#filterByMonth').value
          d.type = document.querySelector('#filterByType').value
          d.late = getParameterByName('type') ?? null
        }
      },
      columns: this.getColumns(),
      language: tableIntl,
      rowCallback: (row, data) => {
        $(row).addClass('bg-success')
      },
      drawCallback: () => {
        this.drawCallback()
      }
    })

    tableOrders.addEventListener('click', async (event) => {
      if (event.target.closest('td.dt-control')) {
        const tr = event.target.closest('tr')
        const row = this.tablePurchases.row(tr)
        const rowId = tr.getAttribute('id')
        const idx = this.detailRows.indexOf(rowId)
        await this.handleTableClick(tr, row, rowId, idx)
      }
    })

    tableOrders.addEventListener('dblclick', async (event) => {
      const tr = event.target.closest('tr')
      if (tr && tr.classList.contains('sub-table-row')) {
        const orderId = tr.getAttribute('data-order-id')
        const productId = tr.getAttribute('data-id')
        await this.showPurchaseItemModal(orderId, productId)
      }
    })

    this.tablePurchases.on('draw', () => {
      this.detailRows.forEach((id) => {
        const escapedId = id.replace(/^(\d)/, '\\3$1 ')
        document.querySelectorAll(`#${escapedId} td.dt-control`).forEach(element => element.click())
      })
    })
  }

  static getColumns () {
    return [
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
        orderable: false,
        render: (data) => data || '-'
      },
      {
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        render: (data) => `
        <button type="button" class="btn btn-sm btn-primary" data-id="${data}" id="show-order-${data}">
          <i class="fa fa-eye"></i>
        </button>
        `
      }
    ]
  }

  static drawCallback () {
    this.tablePurchases.rows().every(function () {
      const data = this.data()
      document.querySelector(`#show-order-${data.id}`).addEventListener('click', () => {
        PagePurchase.validateAndShowModal(data.id)
      })
      return true
    })
  }

  static async handleTableClick (tr, row, rowId, idx) {
    if (await this.validateModal(rowId)) {
      this.toggleChildRow(tr, row, rowId, idx)
    }
  }

  static toggleChildRow (tr, row, rowId, idx) {
    if (row.child.isShown()) {
      tr.classList.remove('details')
      row.child.hide()
      this.detailRows.splice(idx, 1)
    } else {
      tr.classList.add('details')
      row.child(this.formatRow(row.data())).show()
      if (idx === -1) {
        this.detailRows.push(rowId)
      }
    }
  }

  static formatRow (d) {
    const hasPurchase = d.order_products.some(item => item.purchase_date !== null)
    return `
      <table class='table table-bordered table-hover sub-table' data-order-id='${d.id}'>
        <tr>
          <td class='text-uppercase fw-bold'>Produto</td>
          <td class='text-uppercase fw-bold'>Quantidade</td>
          <td class='text-uppercase fw-bold'>Estoque</td>
          <td class='text-uppercase fw-bold'>Status</td>
          ${hasPurchase ? '<td class="text-uppercase fw-bold">Data Compra</td>' : ''}
          <td class='text-uppercase fw-bold'>Fornecedor</td>
          <td class='text-uppercase fw-bold'>Observação</td>
        </tr>
        ${d.order_products.map((item, index) => `
          <tr class='sub-table-row' data-index='${index}' data-id='${item.id}' data-order-id='${d.id}'>
            <td class='fw-bold'>${item.product.name}</td>
            <td>${item.qtd}</td>
            <td>${item.in_stock === 'yes' ? '<span class="badge bg-success">Sim</span>' : item.in_stock === 'no' ? '<span class="badge bg-warning">Não</span>' : item.in_stock === 'partial' ? '<span class="badge bg-info">Parcial</span>' : '-'}</td>
            <td>${item.was_bought === 'Y' ? '<span class="badge bg-success">Comprado</span>' : '<span class="badge bg-warning">Não Comprado</span>'}</td>
            ${hasPurchase ? `<td>${formatDate(item.purchase_date)}</td>` : ''}
            <td>${!item.supplier ? '-' : isValidURL(item.supplier) ? `<a href="${item.supplier}" class="btn btn-sm btn-primary" target="_blank">Abrir Link</a>` : item.supplier}</td>
            <td>${item.obs ? item.obs : '-'}</td>
          </tr>`).join('')}
      </table>
    `
  }

  static async validateModal (id) {
    try {
      const response = await get(`/api/purchase/${id}/viewed`, {
        user_id: document.querySelector('meta[name="user"]').content
      })

      if (!response.exists) {
        const result = await Swal.fire({
          title: 'Aviso',
          text: 'Você deu ciência de visualização deste pedido.',
          icon: 'info',
          confirmButtonText: 'OK'
        })

        if (result.isConfirmed) {
          await post(`/api/purchase/${id}/view`, {
            user_id: document.querySelector('meta[name="user"]').content
          })
          return true
        }
      } else {
        return true
      }
    } catch (error) {
      console.error('Erro ao verificar ou marcar visualização:', error)
      return false
    }
  }

  static async validateAndShowModal (id) {
    if (await this.validateModal(id)) {
      this.showPurchaseModal(id)
    }
  }

  static async showPurchaseModal (id) {
    if (!this.modalPurchase) {
      this.modalPurchase = new Dialog('purchaseModal', 'Detalhes do Pedido', '', 'modal-xl')
    }

    document.body.appendChild(this.modalPurchase.render())
    this.modalPurchase.show()
    this.modalPurchase.clearContent()
    this.modalPurchase.appendContent(skeletonLoading(3, 5))

    try {
      const response = await get(`/api/purchase/${id}/show`)
      if (response.success) {
        this.modalPurchase.setContent(this.buildModalContent(response.data))
        this.initPurchaseTable(response.data.id)
        this.buildBtnModal()
      }
    } catch (error) {
      console.error(error)
    }
  }

  static buildModalContent (data) {
    return `
      <div class="row">
        ${this.buildOrderInfo(data)}
        ${this.buildCustomerInfo(data)}
      </div>
      ${this.buildProductsTable(data)}
      <div id="buttons-container" class="mt-2 mb-4"></div>
    `
  }

  static buildOrderInfo (data) {
    return `
      <div class="col-12 col-md-6">
        <div class="block block-rounded">
          <div class="block-header block-header-default">
            <h3 class="block-title fw-bold">Informações do Pedido</h3>
          </div>
          <div class="block-content">
            <div class="row items-push">
              <div class="col-md-12">
                <div class="block block-rounded h-100 mb-0">
                  <div class="mb-1"><span class="fw-bold">Número do Pedido: </span> ${data.number}</div>
                  <div class="mb-1"><span class="fw-bold">Data de Emissão: </span> ${formatDate(data.date, false)}</div>
                  <div><span class="fw-bold">Data de Entrega: </span> ${formatDate(data.delivery_date, false)}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `
  }

  static buildCustomerInfo (data) {
    return `
      <div class="col-12 col-md-6">
        <div class="block block-rounded">
          <div class="block-header block-header-default">
            <h3 class="block-title fw-bold">Informações do Cliente</h3>
          </div>
          <div class="block-content">
            <div class="row items-push">
              <div class="col-md-12">
                <div class="block block-rounded h-100 mb-0">
                  <div class="block-content fs-md">
                    <div class="fw-bold mb-1">${data.customer.name}</div>
                    <address>
                      <i class="fa fa-phone me-1"></i> ${data.customer.phone ? data.customer.phone : 'Não cadastrado'}<br>
                      <i class="far fa-envelope me-1"></i> <a href="javascript:void(0)">${data.customer.email ? data.customer.email : 'Não cadastrado'}</a>
                    </address>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `
  }

  static buildProductsTable (data) {
    return `
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title fw-bold">Produtos do Pedido</h3>
        </div>
        <div class="block-content py-0">
          <div class="row">
            <div class="col-md-12">${data.order_products.some(product => product.in_stock === 'partial') ? '<span class="fw-bold"><span class="text-danger">ATENÇÃO</span>: O Pedido contém produtos em quantidade parcial, favor olhar a <span class="text-uppercase">observação</span>.</span>' : ''}</div>
            <div class="col-md-12">
              <table class="table table-bordered table-striped table-vcenter list-purchase">
                <thead>
                  <tr>
                    <th class="text-center">Nome</th>
                    <th class="text-center">Quantidade</th>
                    <th class="text-center">Fornecedor</th>
                    <th class="text-center">Link</th>
                    <th class="text-center">Previsão</th>
                    <th class="text-center">Chegou</th>
                    <th class="text-center">Observação</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    `
  }

  static buildBtnModal () {
    const btnContainer = document.getElementById('buttons-container')
    const btnClose = new Button('Fechar', null, 'btn btn-danger w-25')

    btnClose.setOnClick(() => {
      this.modalPurchase.hide()
    })

    btnContainer.appendChild(btnClose.render())
  }

  static async initPurchaseTable (id) {
    const tablePurchaseItemsEl = document.querySelector('.list-purchase')
    if (tablePurchaseItemsEl) {
      new DataTable(tablePurchaseItemsEl, {
        ajax: {
          url: `/api/purchase/${id}/items`,
          type: 'GET'
        },
        searching: false,
        paging: false,
        processing: true,
        serverSide: true,
        language: tableIntl,
        columns: [
          { data: 'product.name' },
          { data: 'qtd' },
          { data: 'supplier' },
          { data: 'link', render: (data) => (data ? `<a href="${data}" class="btn btn-sm btn-primary" target="_blank">Abrir Link</a>` : '-') },
          { data: 'arrival_date', render: (data) => (data || '-') },
          { data: 'arrived', render: (data) => data === 'Y' ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' },
          { data: 'obs' }
        ]
      })
    }
  }

  static async showPurchaseItemModal (orderId, productId) {
    if (!this.modalPurchaseItem) {
      this.modalPurchaseItem = new Dialog('purchaseProductModal', 'Status da Compra do Item', '', 'modal-md')
    }
    document.body.appendChild(this.modalPurchaseItem.render())
    this.modalPurchaseItem.show()
    this.modalPurchaseItem.clearContent()
    this.modalPurchaseItem.appendContent(skeletonLoading(3, 5))

    try {
      const res = await get(`/api/order/${orderId}/item/${productId}`)
      if (res.success) {
        this.modalPurchaseItem.setContent(this.buildPurchaseItemForm(res.data, productId))
        this.initPurchaseItemForm(res.data)
      }
    } catch (error) {
      console.error(error)
    }
  }

  static buildPurchaseItemForm (data, productId) {
    return `
      <form id="updatePurchaseItemForm" action="">
        <input type="hidden" name="order_id" value="${data.order_id}" />
        <input type="hidden" name="order_product_id" value="${productId}" />
        <div id="errors-container"></div>
        <div class="mb-3">
          <label for="was_bought" class="form-label">Status do Item:</label>
          <select name="was_bought" class="form-control" id="was_bought">
            ${['N', 'Y'].map((key) => `<option value="${key}" ${key === data.was_bought ? 'selected' : ''}>${key === 'Y' ? 'Comprado' : 'Não Comprado'}</option>`)}
          </select>
        </div>
        <div class="mb-3 d-none" id="purchase-date-container">
          <label for="purchase_date" class="form-label">Data da Compra:</label>
          <input type="datetime-local" class="form-control" name="purchase_date" id="purchase_date" value="${data.purchase_date ? convertToDatetimeLocal(data.purchase_date) : ''}" />
        </div>
        <div class="mb-3 d-none" id="arrival-date-container">
          <label for="arrival_date" class="form-label">Previsão de Entrega:</label>
          <input type="date" class="form-control" name="arrival_date" id="arrival_date" value="${data.arrival_date ? convertToDatetimeLocal(data.arrival_date, false) : ''}" />
        </div>
        <div class="mb-3 d-none" id="arrived-container">
          <label for="arrived" class="form-label">Chegou:</label>
          <select name="arrived" class="form-control" id="arrived">
            ${['N', 'Y'].map((key) => `<option value="${key}" ${key === data.arrived ? 'selected' : ''}>${key === 'Y' ? 'Sim' : 'Não'}</option>`)}
          </select>
        </div>
        <div class="d-flex gap-2 mb-4">
          <div class="col-12 col-md-6" id="btn-submit-container"></div>
          <div class="col-12 col-md-6" id="btn-cancel-container"></div>
        </div>
      </form>
    `
  }

  static initPurchaseItemForm (data) {
    const form = document.querySelector('#updatePurchaseItemForm')
    const errorsContainer = document.getElementById('errors-container')
    const btnSubmit = new Button('Salvar', null, 'btn btn-primary w-100', 'submit')
    const btnCancel = new Button('Cancelar', null, 'btn btn-danger w-100')
    const purchaseDateContainer = document.getElementById('purchase-date-container')
    const arrivalDateContainer = document.getElementById('arrival-date-container')
    const arrivedContainer = document.getElementById('arrived-container')
    const selectWasBought = document.getElementById('was_bought')

    form.querySelector('#btn-submit-container').appendChild(btnSubmit.render())
    form.querySelector('#btn-cancel-container').appendChild(btnCancel.render())

    form.addEventListener('submit', async function (event) {
      event.preventDefault()
      btnSubmit.setLoading(true)
      const data = {
        arrived: form.querySelector('select[name="arrived"]').value,
        arrival_date: form.querySelector('input[name="arrival_date"]').value,
        purchase_date: form.querySelector('input[name="purchase_date"]').value,
        was_bought: form.querySelector('select[name="was_bought"]').value,
        user_id: document.querySelector('meta[name="user"]').content
      }
      const orderId = form.querySelector('input[name="order_id"]').value
      const orderProductId = form.querySelector('input[name="order_product_id"]').value
      try {
        const res = await post(`/api/purchase/${orderId}/product/${orderProductId}`, data)
        if (res.success) {
          btnSubmit.setLoading(false)
          this.modalPurchaseItem.hide()
          this.tablePurchases.draw()
        }
      } catch (error) {
        btnSubmit.setLoading(false)
        console.error(error)
      }
    }.bind(this))

    if (data.was_bought === 'Y') {
      purchaseDateContainer.classList.remove('d-none')
      arrivalDateContainer.classList.remove('d-none')
      arrivedContainer.classList.remove('d-none')
    }

    selectWasBought.addEventListener('change', () => {
      if (selectWasBought.value === 'Y') {
        purchaseDateContainer.classList.remove('d-none')
        arrivalDateContainer.classList.remove('d-none')
        arrivedContainer.classList.remove('d-none')
      } else {
        purchaseDateContainer.classList.add('d-none')
        arrivalDateContainer.classList.add('d-none')
        arrivedContainer.classList.add('d-none')
      }
    })

    btnCancel.setOnClick(() => {
      form.reset()
      this.modalPurchaseItem.hide()
    })
  }

  static initFilters () {
    const table = this.tablePurchases

    document.querySelector('#filterByStatus').addEventListener('change', () => {
      table.draw()
    })

    document.querySelector('#filterByMonth').addEventListener('change', () => {
      table.draw()
    })

    document.querySelector('#filterByType').addEventListener('change', () => {
      table.draw()
    })

    document.querySelector('#btnCleanFilters').addEventListener('click', () => {
      document.querySelector('#filterByStatus').value = 'N'
      document.querySelector('#filterByMonth').value = 'all'
      document.querySelector('#filterByType').value = 'all'
      delParameterByName('type')
      table.draw()
    })
  }

  static checkStatusOnUrl () {
    const type = getParameterByName('type')
    if (type) {
      const statusElement = document.querySelector('#filterByStatus')
      statusElement.value = 'all'
      const event = new Event('change')
      statusElement.dispatchEvent(event)
    }
  }
}

window.Codebase.onLoad(() => PagePurchase.init())
window.Codebase.helpersOnLoad(['js-datepicker'])
