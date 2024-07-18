import Swal from 'sweetalert2'
import { post } from '../../codebase/api'
import Button from '../../codebase/components/button'
import { clearErrors, clearForm, showErrors, showSuccess } from '../../codebase/utils'

class pageImport {
  static bindForms () {
    const form = document.getElementById('form-order')
    const loading = document.getElementById('loading')
    const message = document.getElementById('message')
    const orderContainer = document.getElementById('import-order-container')
    const productContainer = document.getElementById('import-product-container')
    const btnOrder = new Button('Importar', null, 'btn btn-primary w-25', 'submit', 'form-order')
    const btnProduct = new Button('Importar', null, 'btn btn-primary w-25')

    if (form) {
      orderContainer.appendChild(btnOrder.render())
      productContainer.appendChild(btnProduct.render())
      form.addEventListener('submit', async (event) => {
        event.preventDefault()

        btnOrder.setLoading(true)
        loading.classList.remove('d-none')

        const formData = new FormData(form)

        try {
          const res = await post('/dashboard/import', formData)

          if (res.success) {
            loading.classList.add('d-none')
            form.reset()
            clearErrors(message)
            btnOrder.setLoading(false)
            Swal.fire({
              icon: 'success',
              title: 'Sucesso',
              text: 'Arquivo importado com sucesso.'
            })
          }
        } catch (error) {
          loading.classList.add('d-none')
          btnOrder.setLoading(false)
          showErrors(message, error.data)
        }
      })
    }

    btnProduct.setOnClick(async () => {
      btnProduct.setLoading(true)

      try {
        const res = await post('/api/import/product')

        if (res.success) {
          btnProduct.setLoading(false)
          Swal.fire({
            icon: 'success',
            title: 'Sucesso',
            text: res.message
          })
        }
      } catch (error) {
        btnProduct.setLoading(false)
        showErrors(message, error.data)
      }
    })
  }

  static init () {
    this.bindForms()
  }
}

window.Codebase.onLoad(() => pageImport.init())
