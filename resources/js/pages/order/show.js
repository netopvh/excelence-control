import { Modal } from 'bootstrap'
import { post, delete as del } from '../../codebase/api'
import Helpers from '../../codebase/modules/helpers'

import DataTable from 'datatables.net-bs5'
import 'datatables.net-bs5/css/dataTables.bootstrap5.css'
import 'datatables.net-responsive-bs5'
import { isValidURL } from '../../codebase/utils'
import Button from '../../codebase/components/button'
import Swal from 'sweetalert2'

class pageShowOrder {
  static productsModal = null

  static initPage () {
    const orderId = document.querySelector('meta[name="order-id"]').getAttribute('content')

    const formPreview = document.getElementById('upload-preview')

    if (formPreview) {
      formPreview.addEventListener('submit', async function (e) {
        e.preventDefault()

        try {
          const loadingPreview = document.getElementById('loading-preview')
          loadingPreview.classList.remove('d-none')

          const response = await post('/dashboard/order/' + orderId + '/upload/preview', new FormData(this), {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })

          loadingPreview.classList.add('d-none')

          document.getElementById('upload-preview').classList.add('d-none')
          const fileContainer = document.getElementById('preview-container')
          fileContainer.innerHTML = ''
          fileContainer.innerHTML = `
          <img src="${response.fileUrl}" alt="Pré-visualização" class="img-fluid max-w-25" />
          `
        } catch (error) {
          console.log(error)
          const loadingPreview = document.getElementById('loading-preview')
          loadingPreview.classList.add('d-none')
          document.getElementById('error-msg').innerHTML = error.responseJSON.message
        }
      })
    }

    const formDesign = document.getElementById('upload-design')
    if (formDesign) {
      formDesign.addEventListener('submit', async function (e) {
        e.preventDefault()

        try {
          const loadingDesign = document.getElementById('loading-design')
          loadingDesign.classList.remove('d-none')

          const btnUpload = document.getElementById('btn-upload')
          if (btnUpload) {
            btnUpload.setAttribute('disabled', 'disabled')
          }

          const response = await post(`/api/order/${orderId}/design`, new FormData(this), {
            'Content-Type': 'multipart/form-data'
          })

          loadingDesign.classList.add('d-none')

          // document.getElementById('upload-design').classList.add('d-none')
          // const fileContainer = document.getElementById('design-container')
          // fileContainer.innerHTML = ''
          // fileContainer.innerHTML = `
          // <div class="mt-5">
          //     <a href="${response.fileUrl}"
          //         target="_blank" class="btn btn-primary">
          //         <i class="fa fa-fw fa-download text-white me-1"></i>
          //         <span class="d-sm-inline">Baixar Arquivo</span>
          //     </a>
          //     <form
          //         action="/dashboard/order/${orderId}/remove/design"
          //         method="POST" class="mt-3">
          //         <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
          //         <button type="submit" class="btn btn-danger"><i
          //                 class="fa fa-fw fa-trash"></i>Excluir Arquivo</button>
          //     </form>
          // </div>
          //  <div class="preview-images mt-4">
          //     ${response.previewFiles.map(file => `<img src="${file}" alt="Pré-visualização" class="img-fluid max-w-25" />`).join('')}
          // </div>
          // `

          Swal.fire({
            icon: 'success',
            title: 'Arquivo enviado com sucesso',
            showConfirmButton: true
          }).then((res) => {
            if (res.isConfirmed) {
              window.location.reload()
            }
          })
        } catch (err) {
        // Oculta o indicador de carregamento em caso de erro
          const loadingDesign = document.getElementById('loading-design')
          loadingDesign.classList.add('d-none')

          const btnUpload = document.getElementById('btn-upload')
          if (btnUpload) {
            btnUpload.removeAttribute('disabled')
          }

          // Tratamento de erros
          const errorMsg = document.getElementById('error-msg')
          errorMsg.innerHTML = `
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>${err.data.message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        `
        }
      })
    }

    const btnDeleteDesign = document.getElementById('btn-delete-design')
    if (btnDeleteDesign) {
      btnDeleteDesign.addEventListener('click', async function () {
        const productId = this.dataset.id

        Swal.fire({
          icon: 'question',
          title: 'Tem certeza que deseja excluir o arquivo de design?',
          showConfirmButton: true,
          showCancelButton: true,
          confirmButtonText: 'Sim, excluir!',
          cancelButtonText: 'Cancelar'
        }).then(async (result) => {
          if (result.isConfirmed) {
            console.log(productId)
            console.log(orderId)
            const res = await post(`/api/order/${orderId}/design/${productId}`)

            if (res.success) {
              window.location.reload()
            }
          }
        })
      })
    }

    const dropdownStatus = document.querySelectorAll('.dropdown-item.status')
    dropdownStatus.forEach(dropdown => {
      dropdown.addEventListener('click', async function () {
        const status = this.dataset.value
        const dropDown = document.getElementById('status-dropdown')

        dropDown.innerHTML = `
        <div class="spinner-border spinner-border-sm text-white" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      `

        try {
          const response = await post(`/dashboard/order/${orderId}/update/status`, { status })

          dropDown.innerHTML = `
          <i class="fa fa-fw fa-chevron-down text-white me-1"></i>
          <span class="d-sm-inline">${response.status}</span>
        `

          Helpers.run('jq-alert', {
            icon: 'success',
            title: response.message,
            showConfirmButton: true,
            timer: 1500
          })
        } catch (error) {
          console.error(error)

          dropDown.innerHTML = `
          <i class="fa fa-fw fa-chevron-down text-white me-1"></i>
          <span class="d-sm-inline">Error</span>
        `
        }
      })
    })

    const dropdownDesign = document.querySelectorAll('.dropdown-item.designer')
    dropdownDesign.forEach(dropdown => {
      dropdown.addEventListener('click', async function () {
        const designer = this.dataset.value
        const dropDown = document.getElementById('designer-dropdown')

        dropDown.innerHTML = `
        <div class="spinner-border spinner-border-sm text-white" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      `

        try {
          const response = await post(`/dashboard/order/${orderId}/update/designer`, { designer })

          dropDown.innerHTML = `
          <i class="fa fa-fw fa-chevron-down text-white me-1"></i>
          <span class="d-sm-inline">${response.employee.name}</span>
        `

          Helpers.run('jq-alert', {
            icon: 'success',
            title: response.message,
            showConfirmButton: true,
            timer: 1500
          })
        } catch (error) {
          console.error(error)
          dropDown.innerHTML = `
          <i class="fa fa-fw fa-chevron-down text-white me-1"></i>
          <span class="d-sm-inline">Error</span>
        `
        }
      })
    })

    // jQuery('.dropdown-item.arrived').click(function () {
    //   const status = jQuery(this).data('value')
    //   jQuery.ajax({
    //     url: url + '/dashboard/order/' + orderId + '/update/arrived',
    //     method: 'POST',
    //     data: {
    //       _token: jQuery('meta[name="csrf-token"]').attr('content'),
    //       arrived: status
    //     },
    //     dataType: 'JSON',
    //     beforeSend: function () {
    //       jQuery('#arrived-dropdown').html(
    //         '<div class="spinner-border spinner-border-sm text-white" role="status">' +
    //                   '<span class="visually-hidden">Loading...</span>' +
    //                   '</div>'
    //       )
    //     },
    //     success: function (data) {
    //       location.reload()
    //     },
    //     error: function (data) {
    //       console.log(data)
    //       // $('#status-dropdown').html(
    //       //     '<i class="fa fa-fw fa-chevron-down text-white me-1"></i>' +
    //       //     '<span class="d-none d-sm-inline">Alterar Status</span>'
    //       // );
    //     }
    //   })
    // })

    const dropdownStep = document.querySelectorAll('.dropdown-item.step')
    dropdownStep.forEach(dropdown => {
      dropdown.addEventListener('click', async function () {
        const step = this.dataset.value
        const dropDown = document.getElementById('step-dropdown')

        dropDown.innerHTML = `
        <div class="spinner-border spinner-border-sm text-white" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      `

        try {
          const response = await post(`/dashboard/order/${orderId}/update/step`, { step })

          dropDown.innerHTML = `
          <i class="fa fa-fw fa-chevron-down text-white me-1"></i>
          <span class="d-sm-inline">${response.step}</span>
        `

          Helpers.run('jq-alert', {
            icon: 'success',
            title: response.message,
            showConfirmButton: true,
            timer: 1500
          })
        } catch (error) {
          console.error(error)

          dropDown.innerHTML = `
          <i class="fa fa-fw fa-chevron-down text-white me-1"></i>
          <span class="d-sm-inline">Error</span>
        `
        }
      })
    })
  }

  static initProductsTable () {
    const orderId = document.querySelector('meta[name="order-id"]').getAttribute('content')

    const tableElement = document.getElementById('table-products')
    const table = new DataTable(tableElement, {
      paging: false,
      searching: false,
      serverSide: true,
      processing: true,
      ajax: {
        url: '/api/order/products/' + orderId,
        type: 'GET'
      },
      columns: [
        { data: 'product.name', name: 'product.name', width: '30%' },
        { data: 'qtd', name: 'qtd', width: '10%' },
        { data: 'in_stock', name: 'in_stock', width: '10%' },
        {
          data: 'supplier',
          name: 'supplier',
          width: '17%',
          render: function (data) {
            return data || '-'
          }
        },
        {
          data: 'link',
          name: 'link',
          render: function (data) {
            if (data === null) {
              return '-'
            }

            if (isValidURL(data)) {
              return `<a href="${data}" class="btn btn-sm btn-primary" target="_blank">Abrir Link</a>`
            }

            return data
          }
        },
        { data: 'obs', name: 'obs', width: '23%', render: function (data) { return data || '-' } }
      ],
      language: {
        url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json'
      }
    })

    tableElement.addEventListener('dblclick', (event) => {
      const tr = event.target.closest('tr')

      if (tr) {
        const rowData = table.row(tr).data()

        if (rowData) {
          if (!pageShowOrder.productsModal) {
            pageShowOrder.productsModal = new Modal(document.getElementById('productsModal'))
          }

          const productsModal = pageShowOrder.productsModal

          const modalTitle = document.getElementById('productsModal').querySelector('.block-title')
          const modalBody = document.getElementById('productsModal').querySelector('.block-content')

          modalTitle.textContent = 'Alterar informações do pedido'
          modalBody.innerHTML = `
                <form id="updateProductsForm" action="">
                  <input type="hidden" name="order_id" value="${rowData.order_id}" />
                  <div class="mb-3">
                    <label for="supplier" class="form-label">Fornecedor:</label>
                    <input type="text" name="supplier" class="form-control" value="${rowData.supplier ? rowData.supplier : ''}" />
                  </div>
                  <div class="mb-3">
                    <label for="link" class="form-label">Link:</label>
                    <input type="text" name="link" class="form-control" value="${rowData.link ? rowData.link : ''}" />
                  </div>
                  <div class="mb-3">
                    <label for="obs" class="form-label">Observação:</label>
                    <input type="text" name="obs" class="form-control" value="${rowData.obs ? rowData.obs : ''}" />
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

          productsModal.show()

          const form = document.getElementById('updateProductsForm')
          form.addEventListener('submit', async function (event) {
            event.preventDefault()

            btnSubmit.setLoading(true)

            const data = {
              id: rowData.id,
              supplier: form.querySelector('input[name="supplier"]').value,
              link: form.querySelector('input[name="link"]').value,
              obs: form.querySelector('input[name="obs"]').value
            }

            const orderId = form.querySelector('input[name="order_id"]').value

            const res = await post(`/api/order/${orderId}/info`, data)

            if (res.success) {
              btnSubmit.setLoading(false)
              form.reset()
              productsModal.hide()
              table.draw()
            }
          })

          btnCancel.setOnClick(() => {
            form.reset()
            productsModal.hide()
          })
        }
      }
    })

    window.addEventListener('resize', () => {
      table.columns.adjust()
      table.responsive.recalc()
    })
  }

  static init () {
    this.initPage()
    this.initProductsTable()
  }
}

window.Codebase.onLoad(() => pageShowOrder.init())
