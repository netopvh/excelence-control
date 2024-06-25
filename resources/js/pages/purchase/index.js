import DataTable from 'datatables.net-bs5'
import { getParameterByName, isValidURL } from '../../codebase/utils'
import 'datatables.net-responsive-bs5'
import { Modal } from 'bootstrap'
import { get, post } from '../../codebase/api'
import Swal from 'sweetalert2'
// import $ from 'jquery'

class pagePurchase {
  static purchaseModal = null

  static initDataTables () {
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

      try {
        const response = await get(`/api/purchase/${id}/show`, {
          user_id: document.querySelector('meta[name="user"]').content
        })

        if (response) {
          const modalBody = document.getElementById('purchaseModal').querySelector('.block-content')
          modalBody.innerHTML = `
          <div class="block block-rounded">
            <div class="block-content block-content-full">
              <fieldset class="border px-2 pb-2 mb-2">
                <legend class="float-none w-auto h5">Ações</legend>
                <div class="row">
                  <div class="col-12 col-md-3">
                    <span class="fw-bold">Status dos Produtos:</span>
                    <div class="dropdown">
                      <button type="button" class="btn btn-warning dropdown-toggle w-100" id="status-dropdown"
                          data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="fa fa-fw fa-chevron-down text-white me-1"></i>
                          <span class="d-sm-inline">${response.status}</span>
                      </button>
                      <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-default-primary">

                      </div>
                  </div>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
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
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th class="text-center">Nome</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-center">Fornecedor</th>
                                    <th class="text-center">Link</th>
                                    <th class="text-center">Observação</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${response.order_products.map((item) => {
                                  return `<tr>
                                    <td class="fw-bold">${item.product.name}</td>
                                    <td>${item.qtd}</td>
                                    <td>${item.supplier ? item.supplier : '-'}</td>
                                    <td>${item.link ? item.link : '-'}</td>
                                    <td>${item.obs ? item.obs : '-'}</td>
                                    </tr>`
                                })}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
          </div>
          `

          purchaseModal.show()
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
            return `<button type="button" class="btn btn-sm btn-success" data-id="${data}" id="show-order-${data}">Ver</button>`
          }
        }
      ],
      language: {
        url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json'
      },
      initComplete: function (settings, json) {
        json.data.forEach((item) => {
          document.querySelector(`#show-order-${item.id}`).addEventListener('click', () => {
            validateModal(item.id)
          })
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
