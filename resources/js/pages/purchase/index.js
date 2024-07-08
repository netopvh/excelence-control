import DataTable from 'datatables.net-bs5'
import { convertDateToISO, getParameterByName, isValidURL, skeletonLoading } from '../../codebase/utils'
import 'datatables.net-responsive-bs5'
import 'datatables.net-bs5/css/dataTables.bootstrap5.css'
import { Modal } from 'bootstrap'
import { get, post } from '../../codebase/api'
import Swal from 'sweetalert2'
import Button from '../../codebase/components/button'
// import $ from 'jquery'

class pagePurchase {
  static purchaseModal = null
  static purchaseProductModal = null

  static initDataTables () {
    const format = (d) => {
      return (
        `<table class='table table-bordered table-hover sub-table' data-order-id='${d.id}'>
        <tr>
          <td class='text-uppercase fw-bold'>Produto</td>
          <td class='text-uppercase fw-bold'>Quantidade</td>
          <td class='text-uppercase fw-bold'>Estoque</td>
          <td class='text-uppercase fw-bold'>Status</td>
          <td class='text-uppercase fw-bold'>Fornecedor</td>
          <td class='text-uppercase fw-bold'>Observação</td>
        </tr>` +
        d.order_products.map((item, index) => {
          return `
          <tr class='sub-table-row' data-index='${index}' data-id='${item.id}' data-order-id='${d.id}'>
            <td class='fw-bold'>${item.product.name}</td>
            <td>${item.qtd}</td>
            <td>${item.in_stock === 'yes' ? '<span class="badge bg-success">Sim</span>' : item.in_stock === 'no' ? '<span class="badge bg-warning">Não</span>' : item.in_stock === 'partial' ? '<span class="badge bg-info">Parcial</span>' : '-'}</td>
            <td>${item.was_bought === 'Y' ? '<span class="badge bg-success">Comprado</span>' : '<span class="badge bg-warning">Não Comprado</span>'}</td>
            <td>${!item.supplier ? '-' : isValidURL(item.supplier) ? `<a href="${item.supplier}" class="btn btn-sm btn-primary" target="_blank">Abrir Link</a>` : item.supplier}</td>
            <td>${item.obs ? item.obs : '-'}</td>
          </tr>`
        }).join('') +
        '</table>'
      )
    }

    const detailRows = []

    const toggleChildRow = (tr, row, rowId, idx) => {
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

    const showModal = async (id) => {
      if (!pagePurchase.purchaseModal) {
        pagePurchase.purchaseModal = new Modal(document.getElementById('purchaseModal'))
      }

      const purchaseModal = pagePurchase.purchaseModal

      const modalBody = document.getElementById('purchaseModal').querySelector('.block-content')

      purchaseModal.show()

      modalBody.innerHTML = ''
      modalBody.appendChild(skeletonLoading(3, 5))

      try {
        const response = await get(`/api/purchase/${id}/show`, {
          user_id: document.querySelector('meta[name="user"]').content
        })

        if (response) {
          modalBody.innerHTML = `
          <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title fw-bold">
                    Informações do Cliente
                </h3>
            </div>
            <div class="block-content">
                <div class="row items-push">
                    <div class="col-md-12">
                        <div class="block block-rounded h-100 mb-0">
                            <div class="block-content fs-md">
                                <div class="fw-bold mb-1">${response.customer.name}</div>
                                <address>
                                    <i class="fa fa-phone me-1"></i>
                                    ${response.customer.phone ? response.customer.phone : 'Não cadastrado'}<br>
                                    <i class="far fa-envelope me-1"></i> <a
                                        href="javascript:void(0)">${response.customer.email ? response.customer.email : 'Não cadastrado'}</a>
                                </address>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title fw-bold">
                    Produtos do Pedido
                </h3>
            </div>
            <div class="block-content">
                <div class="row items-push">
                    <div class="col-md-12">${response.order_products.some(product => product.in_stock === 'partial') ? '<span class="fw-bold"><span class="text-danger">ATENÇÃO</span>: O Pedido contém produtos em quantidade parcial, favor olhar a <span class="text-uppercase">observação</span>.</span>' : ''}</div>
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

          const tablePurchaseEl = document.querySelector('.list-purchase')

          if (tablePurchaseEl) {
            const tablePurchase = new DataTable(tablePurchaseEl, {
              ajax: {
                url: `/api/purchase/${id}/items`,
                type: 'GET'
              },
              searching: false,
              paging: false,
              processing: true,
              serverSide: true,
              language: {
                url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json'
              },
              columns: [
                { data: 'product.name' },
                { data: 'qtd' },
                { data: 'supplier' },
                { data: 'link', render: (data) => (data ? `<a href="${data}" class="btn btn-sm btn-primary" target="_blank">Abrir Link</a>` : '-') },
                { data: 'arrival_date', render: (data) => (data || '-') },
                { data: 'arrived', render: function (data) { return !data ? '-' : data === 'Y' ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' } },
                { data: 'obs' }
              ]
            })

            tablePurchaseEl.addEventListener('dblclick', async (event) => {
              const tr = event.target.closest('tr')
              if (tr) {
                const rowData = tablePurchase.row(tr).data()

                if (rowData) {
                  if (!pagePurchase.purchaseProductModal) {
                    pagePurchase.purchaseProductModal = new Modal(document.getElementById('purchaseProductModal'))
                  }

                  const purchaseProductModal = pagePurchase.purchaseProductModal
                  const modalProductBody = document.getElementById('purchaseProductModal').querySelector('.block-content')

                  purchaseModal.hide()
                  purchaseProductModal.show()

                  modalProductBody.innerHTML = ''
                  modalProductBody.appendChild(skeletonLoading(3, 3))

                  const res = await get(`/api/purchase/${id}/product/${rowData.id}/show`)

                  if (res.success) {
                    modalProductBody.innerHTML = ''
                    modalProductBody.innerHTML = `
                    <form id="updatePurchaseProductForm" action="">
                    <input type="hidden" name="order_id" value="${id}" />
                      <input type="hidden" name="order_product_id" value="${rowData.id}" />
                      <div class="mb-3">
                        <label for="arrived" class="form-label">Chegou:</label>
                        <select name="arrived" class="form-control" id="arrived">
                          ${['N', 'Y'].map((key) => `<option value="${key}" ${key === res.data.arrived ? 'selected' : ''}>${key === 'Y' ? 'Sim' : 'Não'}</option>`)}
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="was_bought" class="form-label">Status do Item:</label>
                        <select name="was_bought" class="form-control" id="was_bought">
                          ${['N', 'Y'].map((key) => `<option value="${key}" ${key === res.data.was_bought ? 'selected' : ''}>${key === 'Y' ? 'Comprado' : 'Não Comprado'}</option>`)}
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="arrival_date" class="form-label">Previsão de Entrega:</label>
                        <input type="date" class="js-datepicker form-control" name="arrival_date" id="arrival_date" value="${res.data.arrival_date ? convertDateToISO(res.data.arrival_date) : ''}" />
                      </div>
                      <div class="d-flex gap-2 mb-4">
                        <div class="col-12 col-md-6" id="btn-submit-container">
                        </div>
                        <div class="col-12 col-md-6" id="btn-cancel-container">
                        </div>
                      </div>
                    </form>
                  `
                  }

                  const btnSubmit = new Button('Salvar', null, 'btn btn-primary w-100', 'submit')
                  const btnCancel = new Button('Cancelar', null, 'btn btn-danger w-100')

                  document.getElementById('btn-submit-container').appendChild(btnSubmit.render())
                  document.getElementById('btn-cancel-container').appendChild(btnCancel.render())

                  const form = document.getElementById('updatePurchaseProductForm')
                  form.addEventListener('submit', async function (event) {
                    event.preventDefault()

                    btnSubmit.setLoading(true)

                    const data = {
                      arrived: form.querySelector('select[name="arrived"]').value,
                      arrival_date: form.querySelector('input[name="arrival_date"]').value,
                      was_bought: form.querySelector('select[name="was_bought"]').value
                    }

                    const orderId = form.querySelector('input[name="order_id"]').value
                    const orderProductId = form.querySelector('input[name="order_product_id"]').value

                    try {
                      const res = await post(`/api/purchase/${orderId}/product/${orderProductId}`, data)

                      if (res) {
                        btnSubmit.setLoading(false)
                        tablePurchase.draw()
                        table.draw()
                      }

                      purchaseProductModal.hide()
                      purchaseModal.show()
                    } catch (error) {
                      btnSubmit.setLoading(false)
                      console.error(error)
                    }
                  })

                  btnCancel.setOnClick(function () {
                    purchaseProductModal.hide()
                    purchaseModal.show()
                  })
                }
              }
            })
          }
        }
      } catch (error) {
        console.error(error)
      }
    }

    const validateModal = async (id) => {
      try {
        // Fazer a chamada para verificar se o usuário já deu ciência de visualização
        const response = await get(`/api/purchase/${id}/viewed`, {
          user_id: document.querySelector('meta[name="user"]').content
        })

        if (!response.exists) {
        // Se o usuário ainda não deu ciência de visualização, exibir o SweetAlert
          const result = await Swal.fire({
            title: 'Aviso',
            text: 'Você deu ciência de visualização deste pedido.',
            icon: 'info',
            confirmButtonText: 'OK'
          })

          if (result.isConfirmed) {
            // Fazer a chamada para marcar a visualização
            await post(`/api/purchase/${id}/view`, {
              user_id: document.querySelector('meta[name="user"]').content
            })

            // Agora abrir a tabela filha
            showModal(id)
          }
        } else {
          // Se o usuário já deu ciência de visualização, apenas abrir a tabela filha
          showModal(id)
        }
      } catch (error) {
        console.error('Erro ao verificar ou marcar visualização:', error)
      }
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
      // language: {
      //   url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json'
      // },
      drawCallback: function () {
        const api = this.api()
        api.rows().every(function () {
          const data = this.data()
          document.querySelector(`#show-order-${data.id}`).addEventListener('click', () => {
            validateModal(data.id)
          })
          return true
        })
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

    tableOrders.addEventListener('click', async (event) => {
      if (event.target.closest('td.dt-control')) {
        const tr = event.target.closest('tr')
        const row = table.row(tr)
        const rowId = tr.getAttribute('id')
        const idx = detailRows.indexOf(rowId)

        try {
          // Fazer a chamada para verificar se o usuário já deu ciência de visualização
          const response = await get(`/api/purchase/${rowId}/viewed`, {
            user_id: document.querySelector('meta[name="user"]').content
          })

          if (!response.exists) {
          // Se o usuário ainda não deu ciência de visualização, exibir o SweetAlert
            const result = await Swal.fire({
              title: 'Aviso',
              text: 'Você deu ciência de visualização deste pedido.',
              icon: 'info',
              confirmButtonText: 'OK'
            })

            if (result.isConfirmed) {
              // Fazer a chamada para marcar a visualização
              await post(`/api/purchase/${rowId}/view`, {
                user_id: document.querySelector('meta[name="user"]').content
              })

              // Agora abrir a tabela filha
              toggleChildRow(tr, row, rowId, idx)
            }
          } else {
            // Se o usuário já deu ciência de visualização, apenas abrir a tabela filha
            toggleChildRow(tr, row, rowId, idx)
          }
        } catch (error) {
          console.error('Erro ao verificar ou marcar visualização:', error)
        }
      }
    })

    tableOrders.addEventListener('dblclick', async (event) => {
      const tr = event.target.closest('tr')

      if (tr && tr.classList.contains('sub-table-row')) {
        const orderId = tr.getAttribute('data-order-id')
        const productId = tr.getAttribute('data-id')
        console.log(orderId, productId)
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
    const step = getParameterByName('step')
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
  }

  static init () {
    this.initDataTables()
    this.checkStatusOnUrl()
  }
}

window.Codebase.onLoad(() => pagePurchase.init())
window.Codebase.helpersOnLoad(['js-datepicker'])
