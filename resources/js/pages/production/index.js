import DataTable from 'datatables.net-bs5'
import 'datatables.net-responsive-bs5'
import 'datatables.net-bs5/css/dataTables.bootstrap5.css'
import { tableIntl } from '../../codebase/constants'
import { convertToDatetimeLocal, formatDate, showErrors, skeletonLoading } from '../../codebase/utils'
import { Tooltip } from 'bootstrap'
import Dialog from '../../codebase/components/modal'
import { get, post, put } from '../../codebase/api'
import Button from '../../codebase/components/button'
import Swal from 'sweetalert2'

class PageProduction {
  static tableProduction = null
  static modalProductionItem = null
  static modalProduction = null
  static modalProductionSection = null
  static modalStep = null
  static detailRows = []
  static stepOptions = { in_design: 'Design e Artes', in_production: 'Produção', finished: 'Concluído', shipping: 'Entrega', pickup: 'Retirada', cancelled: 'Cancelado' }

  static init () {
    this.initDataTables()
  }

  static initDataTables () {
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
      columnDefs: this.getColumnDefs(),
      columns: this.getColumns(),
      language: tableIntl,
      order: [[1, 'desc']],
      drawCallback: function () {
        PageProduction.drawCallback(this)
      },
      initComplete: function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(tooltip => {
          new Tooltip(tooltip)
        })
      }
    })

    document.querySelector('#filterByStep').addEventListener('change', () => {
      this.tableProduction.draw()
    })

    tableProduction.addEventListener('click', (event) => {
      this.handleTableClick(event)
    })

    tableProduction.addEventListener('dblclick', (event) => {
      this.handleTableDoubleClick(event)
    })

    this.tableProduction.on('draw', () => {
      this.detailRows.forEach((id) => {
        const escapedId = id.replace(/^(\d)/, '\\3$1 ')
        document.querySelectorAll(`#${escapedId} td.dt-control`).forEach(element => element.click())
      })
    })
  }

  static getColumnDefs () {
    return [
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
    ]
  }

  static getColumns () {
    return [
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
        render: (data) => data || '-'
      },
      {
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        className: 'text-center',
        render: (data) => {
          return `<div class="btn-group">
          <button type="button" class="btn btn-md btn-success" data-id="${data}" id="show-order-${data}" title="Visualizar" data-bs-toggle="tooltip"><i class="fa fa-eye"></i></button>
          <button type="button" class="btn btn-md btn-warning" data-id="${data}" id="show-step-${data}" title="Alterar Etapa" data-bs-toggle="tooltip"><i class="fa fa-right-left"></i></button>
          <button type="button" class="btn btn-md btn-info" data-id="${data}" id="show-section-${data}" title="Alterar Setor" data-bs-toggle="tooltip"><i class="fa fa-people-carry-box"></i></button>
        </div>
        `
        }
      }
    ]
  }

  static drawCallback (el) {
    const api = el.api()
    api.rows().every(function () {
      const data = this.data()

      document.querySelector(`#show-order-${data.id}`).addEventListener('click', async () => {
        if (await PageProduction.validateModal(data.id)) {
          PageProduction.showModal(data.id)
        }
      })

      document.querySelector(`#show-section-${data.id}`).addEventListener('click', async () => {
        if (await PageProduction.validateModal(data.id)) {
          PageProduction.showSectionModal(data.id)
        }
      })

      document.querySelector(`#show-step-${data.id}`).addEventListener('click', async () => {
        if (await PageProduction.validateModal(data.id)) {
          PageProduction.showStepModal(data)
        }
      })

      return true
    })
  }

  static async handleTableClick (event) {
    const tr = event.target.closest('tr')
    if (event.target.closest('td.dt-control')) {
      if (await this.validateModal(tr.getAttribute('id'))) {
        this.toggleChildRow(tr)
      }
    }
  }

  static handleTableDoubleClick (event) {
    const tr = event.target.closest('tr')
    if (tr && tr.classList.contains('sub-table-row')) {
      const orderId = tr.getAttribute('data-order-id')
      const productId = tr.getAttribute('data-id')
      const wasBought = tr.getAttribute('data-bought')
      if (wasBought === 'Y') {
        this.showProductionItemModal(orderId, productId)
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Este item não pode ser modificado!'
        })
      }
    }
  }

  static toggleChildRow (tr) {
    const row = this.tableProduction.row(tr)
    const rowId = tr.getAttribute('id')
    const idx = this.detailRows.indexOf(rowId)
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

  static async validateModal (id) {
    try {
      const response = await get(`/api/production/${id}/viewed`, {
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
          await post(`/api/production/${id}/view`, {
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

  static async showModal (id) {
    if (!this.modalProduction) {
      this.modalProduction = new Dialog('productionModal', 'Detalhes do Pedido em Produção', '', 'modal-xl')
    }
    document.body.appendChild(this.modalProduction.render())
    this.modalProduction.show()
    this.modalProduction.clearContent()
    this.modalProduction.appendContent(skeletonLoading(3, 5))
    try {
      const response = await get(`/api/production/${id}/show`)
      if (response.success) {
        this.modalProduction.setContent(this.buildModalContent(response.data))
        this.initTablePurchase(response.data.id)
        this.buildBtnModal()
      }
    } catch (error) {
      console.error(error)
    }
  }

  static buildModalContent (data) {
    const orderProducts = data.order_products
    const colSize = orderProducts.length === 1 ? 6 : Math.max(12 / orderProducts.length, 3)
    return `
      <div class="row">
        ${this.buildOrderInfo(data)}
        ${this.buildCustomerInfo(data)}
      </div>
      ${this.buildProductsTable(data, colSize)}
      ${this.buildDesignFiles(data, colSize)}
      <div id="buttons-container" class="mt-2 mb-4"></div>
    `
  }

  static buildBtnModal () {
    const btnContainer = document.getElementById('buttons-container')
    const btnClose = new Button('Fechar', null, 'btn btn-danger w-25')

    btnClose.setOnClick(() => {
      this.modalProduction.hide()
    })

    btnContainer.appendChild(btnClose.render())
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

  static buildProductsTable (data, colSize) {
    return `
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title fw-bold">Produtos do Pedido</h3>
        </div>
        <div class="block-content py-0">
          <div class="row">
            <div class="col-md-12">${data.order_products.some(product => product.in_stock === 'partial') ? '<span class="fw-bold"><span class="text-danger">ATENÇÃO</span>: O Pedido contém produtos em quantidade parcial, favor olhar a <span class="text-uppercase">observação</span>.</span>' : ''}</div>
            <div class="col-md-12">
              <table class="table table-bordered table-striped table-vcenter list-production-items">
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

  static buildDesignFiles (data, colSize) {
    return `
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title fw-bold">Arquivos de Design</h3>
        </div>
        <div class="block-content">
          <div class="row items-push">
            ${data.order_products.map((item) => this.buildDesignFile(item, colSize)).join('')}
          </div>
        </div>
      </div>
    `
  }

  static buildDesignFile (item, colSize) {
    return `
      <div class="col-12 col-md-${colSize}">
        <div class="card mb-3">
          <div class="card-body text-center">
            <h5 class="card-title">${item.product.name}</h5>
            <div>
              ${this.getPreviewImages(item)}
              ${item.design_file ? this.getDownloadButton(item.design_file) : `<img src="${item.noimage}" alt="Pré-visualização" class="img-fluid" />`}
            </div>
          </div>
        </div>
      </div>
    `
  }

  static getPreviewImages (item) {
    if (Array.isArray(item.preview) && item.preview.length > 0) {
      return item.preview.map((file) => `<div class="preview-image"><img src="${file}" alt="Pré-visualização" class="max-w-25 img-fluid"></div>`).join('')
    }
    if (item.design_file && item.preview) {
      return `<img src="${item.design_file}" alt="Pré-visualização" class="img-fluid" />`
    }
    return ''
  }

  static getDownloadButton (designFile) {
    return `
      <div class="btn-group">
        <a href="${designFile}" class="btn btn-success mt-2" target="_blank">Baixar Arquivo</a>
      </div>
    `
  }

  static async initTablePurchase (id) {
    const tableProductionItemsEl = document.querySelector('.list-production-items')
    if (tableProductionItemsEl) {
      const tableProductionItems = new DataTable(tableProductionItemsEl, {
        ajax: {
          url: `/api/purchase/${id}/items`,
          type: 'GET'
        },
        searching: false,
        paging: false,
        processing: true,
        serverSide: true,
        language: tableIntl,
        columns: this.getPurchaseColumns()
      })
    }
  }

  static async showSectionModal (id) {
    if (!this.modalProductionSection) {
      this.modalProductionSection = new Dialog('productionSectionModal', 'Definição Setor da Produção', '', 'modal-xl')
    }
    document.body.appendChild(this.modalProductionSection.render())
    this.modalProductionSection.show()
    this.modalProductionSection.clearContent()
    this.modalProductionSection.appendContent(skeletonLoading(3, 5))

    try {
      const [sectorsResponse, responsablesResponse, sectionResponse] = await Promise.all([
        get('/api/production/sectors'),
        get('/api/production/responsables'),
        get(`/api/production/${id}/show`)
      ])

      if (sectionResponse.success) {
        this.modalProductionSection.setContent(this.buildSectionForm(sectionResponse.data, sectorsResponse.data, responsablesResponse.data))
        this.initProductionSectionForm(sectionResponse.data)
      }
    } catch (error) {
      console.error(error)
    }
  }

  static buildSectionForm (data, sectors, responsables) {
    return `
      <form id="updateProductionSectionForm">
        <input type="hidden" name="order_id" value="${data.id}" />
        <div class="mb-4">
          <table class="table table-bordered table-striped list-production-items">
            <thead>
              <tr>
                <th class="text-center">Produto</th>
                <th class="text-center">Setor de Produção</th>
                <th class="text-center">Responsável</th
              </tr>
            </thead>
            <tbody>
            ${data.order_products.map((item, index) => `
              <tr>
                <td>
                  ${item.product.name}
                </td>
                <td>
                  ${item.in_stock === 'yes' || (item.was_bought === 'Y' && item.arrived === 'Y')
                  ? `
                  <input type="hidden" name="order_products[${index}][id]" value="${item.id}">
                  <select name="order_products[${index}][sector]" class="form-control">
                    <option value="">Não definido</option>
                    ${sectors.map((key) => `<option value="${key.id}" ${key.id === item.sector_id ? 'selected' : ''}>${key.name}</option>`).join('')}
                  </select>
                  `
                  : 'Produto não comprado ou não chegou'}
                </td>
                <td>
                ${item.in_stock === 'yes' || (item.was_bought === 'Y' && item.arrived === 'Y')
                  ? `
                  <select name="order_products[${index}][responsable]" class="form-control">
                    <option value="">Não definido</option>
                    ${responsables.map((key) => `<option value="${key.id}" ${key.id === item.responsable_id ? 'selected' : ''}>${key.name}</option>`).join('')}
                  </select>
                  `
                  : 'Produto não comprado ou não chegou'}
                </td>
              </tr>
            `).join('')}
            </tbody>
          </table>
        </div>
        <div class="d-flex gap-2 mb-4">
          <div class="col-12 col-md-2" id="btn-submit-container"></div>
          <div class="col-12 col-md-2" id="btn-cancel-container"></div>
        </div>
      </form>
    `
  }

  static initProductionSectionForm (data) {
    const form = document.querySelector('#updateProductionSectionForm')

    const btnCancel = new Button('Cancelar', null, 'btn btn-danger w-100')
    const btnSubmit = new Button('Salvar', null, 'btn btn-primary w-100', 'submit')

    form.querySelector('#btn-submit-container').appendChild(btnSubmit.render())
    form.querySelector('#btn-cancel-container').appendChild(btnCancel.render())

    form.addEventListener('submit', async (e) => {
      e.preventDefault()

      btnSubmit.setLoading(true)
      const formData = new FormData(form)
      const orderId = form.querySelector('input[name="order_id"]').value
      const data = this.serializeFormData(formData)

      try {
        const response = await post(`/api/production/${orderId}/sector`, data)
        if (response.success) {
          btnSubmit.setLoading(false)
          this.modalProductionSection.hide()
          Swal.fire({
            icon: 'success',
            title: 'Setor da produção definido com sucesso'
          })
        } else {
          showErrors(response)
        }
        btnSubmit.setLoading(false)
      } catch (error) {
        console.error(error)
      }
    })

    btnCancel.setOnClick(() => {
      this.modalProductionSection.hide()
    })
  }

  static getPurchaseColumns () {
    return [
      { data: 'product.name' },
      { data: 'qtd' },
      { data: 'supplier' },
      { data: 'link', render: (data) => data ? `<a href="${data}" class="btn btn-sm btn-primary" target="_blank">Abrir Link</a>` : '-' },
      { data: 'arrival_date', render: (data) => data || '-' },
      { data: 'arrived', render: (data) => data === 'Y' ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não' },
      { data: 'obs' }
    ]
  }

  static async showProductionItemModal (orderId, productId) {
    if (!this.modalProductionItem) {
      this.modalProductionItem = new Dialog('productionItemModal', 'Status da Compra do Item', '', 'modal-md')
    }

    document.body.appendChild(this.modalProductionItem.render())
    this.modalProductionItem.show()
    this.modalProductionItem.clearContent()
    this.modalProductionItem.appendContent(skeletonLoading(3, 5))

    try {
      const res = await get(`/api/order/${orderId}/item/${productId}`)
      if (res.success) {
        this.modalProductionItem.setContent(this.buildProductionItemForm(res.data, productId))
        this.initProductionItemForm(res.data)
      }
    } catch (error) {
      showErrors(error.data)
      console.error(error)
    }
  }

  static buildProductionItemForm (data, productId) {
    return `
      <form id="updateProductionItemForm" action="">
        <input type="hidden" name="order_id" value="${data.order_id}" />
        <input type="hidden" name="order_product_id" value="${productId}" />
        <div id="errors-container"></div>
        <div class="mb-3">
          <label for="arrived" class="form-label">Chegou:</label>
          <select name="arrived" class="form-control" id="arrived">
            ${['N', 'Y'].map((key) => `<option value="${key}" ${key === data.arrived ? 'selected' : ''}>${key === 'Y' ? 'Sim' : 'Não'}</option>`)}
          </select>
        </div>
        <div class="mb-3 d-none" id="delivered-date-container">
          <label for="delivered_date" class="form-label">Data de Chegada:</label>
          <input type="datetime-local" class="form-control" name="delivered_date" id="delivered_date" value="${data.delivered_date ? convertToDatetimeLocal(data.delivered_date) : ''}" />
        </div>
        <div class="d-flex gap-2 mb-4">
          <div class="col-12 col-md-6" id="btn-submit-container"></div>
          <div class="col-12 col-md-6" id="btn-cancel-container"></div>
        </div>
      </form>
    `
  }

  static initProductionItemForm (data) {
    const form = document.querySelector('#updateProductionItemForm')
    const errorsContainer = document.getElementById('errors-container')
    const btnSubmit = new Button('Salvar', null, 'btn btn-primary w-100', 'submit')
    const btnCancel = new Button('Cancelar', null, 'btn btn-danger w-100')
    const deliveredDateContainer = document.getElementById('delivered-date-container')
    const selectArrived = document.getElementById('arrived')

    form.querySelector('#btn-submit-container').appendChild(btnSubmit.render())
    form.querySelector('#btn-cancel-container').appendChild(btnCancel.render())

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
          this.tableProduction.draw()
          this.modalProductionItem.hide()
        }
      } catch (error) {
        btnSubmit.setLoading(false)
        showErrors(errorsContainer, error.data)
      }
    }.bind(this))

    if (data.arrived === 'Y') {
      deliveredDateContainer.classList.remove('d-none')
    }

    selectArrived.addEventListener('change', () => {
      if (selectArrived.value === 'Y') {
        deliveredDateContainer.classList.remove('d-none')
      } else {
        deliveredDateContainer.classList.add('d-none')
      }
    })

    btnCancel.setOnClick(() => {
      form.reset()
      this.modalProductionItem.hide()
    })
  }

  static async showStepModal (data) {
    const modal = document.getElementById('productionProductModal')
    if (data) {
      if (!this.modalStep) {
        this.modalStep = new Dialog('productionStepModal', 'Informações da Etapa do Pedido', '', 'modal-lg')
      }
      document.body.appendChild(this.modalStep.render())
      this.modalStep.setContent(this.buildStepForm(data))
      this.initStepForm()
      this.modalStep.show()
    }
  }

  static buildStepForm (rowData) {
    return `
      <form id="updateStepForm" action="">
        <input type="hidden" name="order_id" value="${rowData.id}" />
        <div class="mb-4">
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
                      ${Object.keys(this.stepOptions).map((key) => `<option value="${key}" ${key === item.step ? 'selected' : ''}>${this.stepOptions[key]}</option>`).join('')}
                    </select>
                  </td>
                </tr>`).join('')}
            </tbody>
          </table>
        </div>
        <div class="d-flex gap-2 mb-4">
          <div class="col-12 col-md-3" id="btn-submit-container"></div>
          <div class="col-12 col-md-3" id="btn-cancel-container"></div>
        </div>
      </form>
    `
  }

  static initStepForm () {
    const form = document.querySelector('#updateStepForm')
    const btnSubmit = new Button('Salvar', null, 'btn btn-primary w-100', 'submit')
    const btnCancel = new Button('Cancelar', null, 'btn btn-danger w-100')

    form.querySelector('#btn-submit-container').appendChild(btnSubmit.render())
    form.querySelector('#btn-cancel-container').appendChild(btnCancel.render())

    form.addEventListener('submit', async function (event) {
      event.preventDefault()
      btnSubmit.setLoading(true)
      const formData = new FormData(form)
      const orderId = form.querySelector('input[name="order_id"]').value
      const data = this.serializeFormData(formData)
      const res = await put(`/api/order/${orderId}`, data)
      if (res.success) {
        btnSubmit.setLoading(false)
        form.reset()
        this.modalStep.hide()
        this.tableProduction.draw()
      }
    }.bind(this))

    btnCancel.setOnClick(() => {
      form.reset()
      this.modalStep.hide()
    })
  }

  static serializeFormData (formData) {
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
    return data
  }

  static formatRow (d) {
    const hasPurchase = d.order_products.some(item => item.purchase_date !== null)
    const hasDelivered = d.order_products.some(item => item.delivered_date !== null)
    return `
      <table class='table table-bordered table-hover sub-table' data-order-id='${d.id}'>
        <tr>
          <td class='text-uppercase fw-bold'>Produto</td>
          <td class='text-uppercase fw-bold'>Quantidade</td>
          <td class='text-uppercase fw-bold'>Status</td>
          ${hasPurchase ? '<td class="text-uppercase fw-bold">Data Compra</td>' : ''}
          ${hasPurchase ? '<td class="text-uppercase fw-bold">Previsão Entrega</td>' : ''}
          ${hasPurchase ? '<td class="text-uppercase fw-bold">Chegou</td>' : ''}
          ${hasDelivered ? '<td class="text-uppercase fw-bold">Data Chegada</td>' : ''}
        </tr>
        ${d.order_products.map((item, index) => `
          <tr class='sub-table-row' data-index='${index}' data-id='${item.id}' data-order-id='${d.id}' data-bought='${item.was_bought}'>
            <td class='fw-bold'>${item.product.name}</td>
            <td>${item.qtd}</td>
            <td>${item.in_stock === 'yes' ? '-' : item.was_bought === 'Y' ? '<span class="badge bg-success">Comprado</span>' : '<span class="badge bg-warning">Não Comprado</span>'}</td>
            ${hasPurchase ? `<td>${formatDate(item.purchase_date)}</td>` : ''}
            ${hasPurchase ? `<td>${formatDate(item.arrival_date, false)}</td>` : ''}
            ${hasPurchase ? `<td>${item.arrived === 'Y' ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-warning">Não'}</td>` : ''}
            ${hasDelivered ? `<td>${formatDate(item.delivered_date)}</td>` : ''}
          </tr>`).join('')}
      </table>
    `
  }
}

window.Codebase.onLoad(() => PageProduction.init())
