import { get, post } from '../../codebase/api'
import Helpers from '../../codebase/modules/helpers'
import { Modal } from 'bootstrap'
import { clearForm, focusElement } from '../../codebase/utils'
import Swal from 'sweetalert2'
import LoadingSpinner from '../../codebase/components/loading'
import SearchComplete from '../../codebase/components/search-complete'

class pageCreateOrder {
  static customerModal = null
  static spinner = null

  static initCompleteInput () {
    const inputElement = document.getElementById('create-autoload')
    const searchAutoComplete = new SearchComplete(
      'Cliente',
      'Informe o nome do cliente',
      'Listar todos os Clientes'
    )

    inputElement.append(searchAutoComplete.render())
  }

  static initAutoComplete () {
    const inputElement = document.getElementById('autocomplete-input')
    const suggestionsContainer = document.getElementById('autocomplete-suggestions')

    inputElement.addEventListener('input', function () {
      clearTimeout(this.debounceTimer)

      const query = this.value

      if (query.length < 2) {
        suggestionsContainer.innerHTML = ''
        return
      }

      this.debounceTimer = setTimeout(async () => {
        pageCreateOrder.ajaxCall(query)
      }, 400)
    })

    inputElement.addEventListener('focus', async function () {
      const query = this.value

      if (query.length < 2) {
        suggestionsContainer.innerHTML = ''
        suggestionsContainer.classList.add('d-none')
        return
      }

      pageCreateOrder.ajaxCall(query)
    })
  }

  static async ajaxCall (query) {
    const suggestionsContainer = document.getElementById('autocomplete-suggestions')
    suggestionsContainer.innerHTML = ''

    const nomeCodificado = encodeURIComponent(query)
    const loadAutocomplete = document.getElementById('autocomplete-loading')

    if (!pageCreateOrder.spinner) {
      pageCreateOrder.spinner = new LoadingSpinner(20, '#000000')
    }

    if (!pageCreateOrder.spinner.rendered()) {
      loadAutocomplete.appendChild(pageCreateOrder.spinner.render())
    }

    try {
      const res = await get('/dashboard/customer/filter', { search: nomeCodificado })

      if (res) {
        if (pageCreateOrder.spinner.rendered()) {
          loadAutocomplete.removeChild(pageCreateOrder.spinner.instance())
        }
      }

      const suggestions = res.data.slice(0, 6)
      suggestionsContainer.classList.remove('d-none')
      suggestionsContainer.innerHTML = ''

      if (suggestions.length === 0) {
        const noResultElement = document.createElement('div')
        noResultElement.classList.add('autocomplete-suggestion')
        noResultElement.innerHTML = `
          <div>
            <small class="muted">Cliente "<strong>${query}</strong>" não está cadastrado.</small>
            <br/>
            <h5>
              <small class="muted">
                <i class="fas fa-plus"></i> Cadastrar "<strong>${query}</strong>" como novo cliente.
              </small>
            </h5>
          </div>
        `
        noResultElement.addEventListener('click', () => pageCreateOrder.cadastrarNovoCliente(query))
        suggestionsContainer.appendChild(noResultElement)
      } else {
        suggestions.forEach(suggestion => {
          const suggestionElement = document.createElement('div')
          suggestionElement.classList.add('autocomplete-suggestion')
          suggestionElement.innerHTML = `
            <div><strong>${suggestion.name}</strong></div>
            <div><span class="informacoes-cliente"><i class="fas fa-envelope"></i> ${suggestion.email || 'Não informado'}</span></div>
            <div><span class="informacoes-cliente"><i class="fas fa-phone"></i> ${suggestion.phone || 'Não informado'}</span></div>
            <div class="divider"></div>
          `

          suggestionElement.addEventListener('click', () => {
            pageCreateOrder.selecionarCliente(suggestion)
            suggestionsContainer.innerHTML = ''
          })
          suggestionsContainer.appendChild(suggestionElement)
        })
      }
    } catch (error) {
      console.error(error)
    }
  }

  static async mostrarListagem () {
    document.querySelector('.listar-clientes').addEventListener('click', async () => {
      this.ajaxCall('')
    })
  }

  static cadastrarNovoCliente (nome) {
    if (!pageCreateOrder.customerModal) {
      pageCreateOrder.customerModal = new Modal(document.getElementById('customer-modal'))
    }
    const customerModal = pageCreateOrder.customerModal
    document.getElementById('autocomplete-suggestions').classList.add('d-none')
    customerModal.show()
    document.getElementById('customer-name').value = nome
  }

  static selecionarCliente (cliente) {
    document.getElementById('group-autocomplete').classList.add('d-none')
    document.getElementById('listar-clientes-group').classList.add('d-none')
    document.getElementById('autocomplete-suggestions').classList.add('d-none')
    const clienteSelecionado = document.getElementById('cliente-selecionado')

    const divSelAutocomplete = document.createElement('div')
    divSelAutocomplete.id = `selecionado_autocomplete_${cliente.id}`

    const inputHidden = document.createElement('input')
    inputHidden.type = 'hidden'
    inputHidden.name = 'customer_id'
    inputHidden.value = cliente.id

    const spanConteudoSelAutocomplete = document.createElement('span')
    spanConteudoSelAutocomplete.classList.add('conteudo_selecionado_autocomplete')
    spanConteudoSelAutocomplete.innerHTML = `
      <div>
        <div class="row">
          <h5>${cliente.name}</h5>
        </div>
        <div class="row">
          <div class="col-12 col-md-6">
            <span class="informacoes-cliente"><i class="fas fa-phone icone-informacoes-cliente"></i> ${cliente.phone || 'Não informado'}</span>
          </div>
          <div class="col-12 col-md-6">
            <span class="informacoes-cliente"><i class="fas fa-envelope icone-informacoes-cliente"></i> ${cliente.email || 'Não informado'}</span>
          </div>
        </div>
      </div>
    `

    const btnAlterarCliente = document.createElement('button')
    btnAlterarCliente.classList.add('btn', 'btn-sm', 'btn-secondary', 'mt-2')
    btnAlterarCliente.type = 'button'
    btnAlterarCliente.innerHTML = '<i class="fa-solid fa-rotate"></i> Trocar cliente'
    btnAlterarCliente.addEventListener('click', () => pageCreateOrder.trocarCliente())

    divSelAutocomplete.append(inputHidden)
    divSelAutocomplete.append(spanConteudoSelAutocomplete)
    divSelAutocomplete.append(btnAlterarCliente)
    clienteSelecionado.append(divSelAutocomplete)
  }

  static trocarCliente () {
    document.getElementById('cliente-selecionado').innerHTML = ''
    document.getElementById('listar-clientes-group').classList.remove('d-none')
    document.getElementById('autocomplete-input').value = ''
    Helpers.run('jq-appear', 'group-autocomplete')
  }

  static salvarCliente () {
    const formCustomer = document.getElementById('form-customer')

    if (formCustomer) {
      formCustomer.addEventListener('submit', async function (event) {
        event.preventDefault()

        const errorContainer = document.getElementById('error-container')
        const errorMessage = document.getElementById('error-message')
        const inputComplete = document.getElementById('autocomplete-input')

        const formData = new FormData(this)
        const formObject = {}

        formData.forEach((value, key) => {
          formObject[key] = value
        })

        try {
          const res = await post('/api/customer', formObject, {
            headers: {
              'Content-Type': 'application/json'
            }
          })

          errorContainer.classList.remove('d-none')
          errorContainer.classList.add('d-none')
          clearForm(this)
          focusElement(inputComplete)

          Swal.fire({
            icon: 'success',
            title: res.message,
            showConfirmButton: true
          }).then(() => {
            if (pageCreateOrder.customerModal) {
              pageCreateOrder.customerModal.hide()
            }
          })
        } catch (err) {
          let errorList = '<ul>'
          for (const field in err.data.errors) {
            if (err.data.errors.hasOwnProperty(field)) {
              err.data.errors[field].forEach(error => {
                errorList += `<li>${error}</li>`
              })
            }
          }
          errorList += '</ul>'
          errorMessage.innerHTML = errorList
          errorContainer.classList.remove('d-none')
        }
      })
    }
  }

  static FechaClienteModal () {
    const btnCloseModal = document.getElementById('close-customer-modal')
    if (btnCloseModal) {
      btnCloseModal.addEventListener('click', function (event) {
        event.preventDefault()
        const formCustomer = document.getElementById('form-customer')
        const errorContainer = document.getElementById('error-container')

        if (formCustomer) {
          errorContainer.classList.remove('d-none')
          errorContainer.classList.add('d-none')
          clearForm(formCustomer)
        }

        if (pageCreateOrder.customerModal) {
          pageCreateOrder.customerModal.hide()
        }
      })
    }
  }

  static init () {
    // this.initCompleteInput()
    this.initAutoComplete()

    this.mostrarListagem()

    this.salvarCliente()

    this.FechaClienteModal()
  }
}

window.Codebase.onLoad(() => {
  pageCreateOrder.init()
})
window.Codebase.helpersOnLoad(['jq-appear', 'jq-datepicker'])
