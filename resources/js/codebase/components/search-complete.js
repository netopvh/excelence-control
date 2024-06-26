import LoadingSpinner from './loading'

class SearchComplete {
  constructor (title = '', placeholder = '', buttonTitle = '', ajaxAll = null) {
    this.title = title
    this.placeholder = placeholder
    this.buttonTitle = buttonTitle

    this.ajaxAll = ajaxAll
  }

  createInputSection () {
    const inputSectionContainer = document.createElement('div')
    inputSectionContainer.classList.add('row')
    inputSectionContainer.innerHTML = `
      <div class="col-md-6 col-xl-12">
        <label for="autocomplete-input" class="form-label text-uppercase">${this.title}:</label>
        <div class="input-group" id="group-autocomplete">
          <input type="text" class="form-control" id="autocomplete-input" placeholder="${this.placeholder}" autocomplete="off">
        </div>
        <div class="divider my-2"></div>
        <div id="autocomplete-suggestions" class="autocomplete-suggestions d-none"></div>
      </div>
    `

    return inputSectionContainer
  }

  createAutoLoadSection () {
    const loadingSpinner = new LoadingSpinner(20, '#000000')

    const autoLoadSectionContainer = document.createElement('div')
    autoLoadSectionContainer.id = 'autocomplete-loading'
    autoLoadSectionContainer.classList.add('d-none')
    autoLoadSectionContainer.appendChild(loadingSpinner.render())

    return autoLoadSectionContainer
  }

  createButtonListCustomersSection () {
    const listCustomersRow = document.createElement('div')
    listCustomersRow.classList.add('row')

    const listCustomersContainer = document.createElement('div')
    listCustomersContainer.classList.add('col-md-6', 'col-xl-12')

    const buttonListAll = document.createElement('a')
    buttonListAll.classList.add('muted', 'text-black', 'list-all')
    buttonListAll.href = 'javascript:void(0)'
    buttonListAll.innerHTML = `
      <span>
        <i class="fas fa-caret-down"></i>
      </span>
      ${this.buttonTitle}
    `
    if (this.ajaxAll) {
      buttonListAll.addEventListener('click', () => {
        const suggestionsContainer = document.getElementById('autocomplete-suggestions')
        suggestionsContainer.innerHTML = ''
        const loadAutocomplete = document.getElementById('autocomplete-loading')
        loadAutocomplete.classList.remove('d-none')
      })
    }

    listCustomersRow.appendChild(listCustomersContainer)
    listCustomersContainer.appendChild(buttonListAll)

    return listCustomersRow
  }

  createSelectedCustomerSection () {
    const selectedCustomerRow = document.createElement('div')
    selectedCustomerRow.classList.add('row')

    const selectedCustomerContainer = document.createElement('div')
    selectedCustomerContainer.classList.add('col-md-6', 'col-xl-12')
    selectedCustomerContainer.id = 'selected-customer'

    selectedCustomerRow.appendChild(selectedCustomerContainer)

    return selectedCustomerRow
  }

  render () {
    const autoloadContainer = document.createElement('div')
    autoloadContainer.classList.add('mb-3')

    autoloadContainer.appendChild(this.createInputSection())
    autoloadContainer.appendChild(this.createAutoLoadSection())
    autoloadContainer.appendChild(this.createButtonListCustomersSection())
    autoloadContainer.appendChild(this.createSelectedCustomerSection())

    return autoloadContainer
  }
}

export default SearchComplete
