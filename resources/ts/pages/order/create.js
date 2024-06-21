import { get } from '../../codebase/api'

class pageCreateOrder {
  static initAutoComplete () {
    document.getElementById('autocomplete-input').addEventListener('input', function () {
      clearTimeout(this.debounceTimer)

      const query = this.value

      if (query.length < 2) {
        document.getElementById('autocomplete-suggestions').innerHTML = ''
        return
      }

      this.debounceTimer = setTimeout(() => {
        const nomeCodificado = encodeURIComponent(query)

        get('/dashboard/customer/filter', {
          search: nomeCodificado
        })
          .then(res => {
            const suggestions = res.data.slice(0, 6)
            const suggestionsContainer = document.getElementById('autocomplete-suggestions')
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
              noResultElement.addEventListener('click', function () {
                pageCreateOrder.cadastrarNovoCliente(query)
              })
              suggestionsContainer.appendChild(noResultElement)
            } else {
              suggestions.forEach(suggestion => {
                const suggestionElement = document.createElement('div')
                suggestionElement.classList.add('autocomplete-suggestion')
                suggestionElement.innerHTML = `
                                  <div><strong>${suggestion.name}</strong></div>
                                  <div><span class="informacoes-cliente"><i class="fas fa-envelope"></i> ${suggestion.email ? suggestion.email : 'Não informado'}</span></div>
                                  <div><span class="informacoes-cliente"><i class="fas fa-phone"></i> ${suggestion.phone ? suggestion.phone : 'Não informado'}</span></div>
                                  <span class="bg-primary"></span>
                              `
                suggestionElement.addEventListener('click', function () {
                  pageCreateOrder.selecionarCliente(suggestion)
                  suggestionsContainer.innerHTML = ''
                })
                suggestionsContainer.appendChild(suggestionElement)
              })
            }
          })
          .catch(error => {
            console.error('Error fetching autocomplete suggestions:', error)
          })
      }, 300) // Delay de 300ms
    })

    document.getElementById('listar-clientes').addEventListener('click', function () {
      get('/dashboard/customer/filter')
        .then(res => {
          const suggestionsContainer = document.getElementById('autocomplete-suggestions')
          suggestionsContainer.innerHTML = ''
          res.data.forEach(suggestion => {
            const suggestionElement = document.createElement('div')
            suggestionElement.classList.add('autocomplete-suggestion')
            suggestionElement.innerHTML = `
                          <div><strong>${suggestion.name}</strong></div>
                          <div><span class="informacoes-cliente"><i class="fas fa-envelope"></i> ${suggestion.email ? suggestion.email : 'Não informado'}</span></div>
                          <div><span class="informacoes-cliente"><i class="fas fa-phone"></i> ${suggestion.phone ? suggestion.phone : 'Não informado'}</span></div>
                          <div class="divider"></div>
                      `
            suggestionElement.addEventListener('click', function () {
              pageCreateOrder.selecionarCliente(suggestion)
            })
            suggestionsContainer.appendChild(suggestionElement)
            suggestionsContainer.classList.remove('d-none')
          })
        })
        .catch(error => {
          console.error('Error fetching autocomplete suggestions:', error)
        })
    })
  }

  static cadastrarNovoCliente (nome) {
    // Implemente a lógica para cadastrar um novo cliente
    document.getElementById('autocomplete-suggestions').classList.add('d-none')
    $('#modal-normal').modal('show')
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
                  <h5>
                    ${cliente.name}
                  </h5>
              </div>
              <div class="row">
                  <div class="col-12 col-md-6">
                      <span class="informacoes-cliente"><i class="fas fa-phone icone-informacoes-cliente"></i> ${cliente.phone ? cliente.phone : 'Não informado'}</span>
                  </div>
                  <div class="col-12 col-md-6">
                      <span class="informacoes-cliente"><i class="fas fa-envelope icone-informacoes-cliente"></i> ${cliente.email ? cliente.email : 'Não informado'}</span>
                  </div>
              </div>
          </div>
      `

    const btnAlterarCliente = document.createElement('button')
    btnAlterarCliente.classList.add('btn', 'btn-sm', 'btn-secondary', 'mt-2')
    btnAlterarCliente.type = 'button'
    btnAlterarCliente.innerHTML = `
          <i class="fa-solid fa-rotate"></i> Trocar cliente
      `
    btnAlterarCliente.addEventListener('click', function () {
      pageCreateOrder.trocarCliente()
    })

    divSelAutocomplete.append(inputHidden)
    divSelAutocomplete.append(spanConteudoSelAutocomplete)
    divSelAutocomplete.append(btnAlterarCliente)
    clienteSelecionado.append(divSelAutocomplete)
  }

  static trocarCliente () {
    document.getElementById('cliente-selecionado').innerHTML = ''
    document.getElementById('group-autocomplete').classList.remove('d-none')
    document.getElementById('listar-clientes-group').classList.remove('d-none')
    document.getElementById('autocomplete-input').value = ''
  }

  static mostrarListagem (id) {
    alert(`Mostrar listagem de clientes com id: ${id}`)
    // Implemente a lógica para mostrar a listagem de clientes
  }

  static init () {
    this.initAutoComplete()
  }
}

Codebase.onLoad(() => { pageCreateOrder.init() })
Codebase.helpersOnLoad(['jq-appear'])
