function getParameterByName (name) {
  name = name.replace(/[\[\]]/g, '\\$&')
  const url = window.location.href
  const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)')
  const results = regex.exec(url)
  if (!results) return null
  if (!results[2]) return ''
  return decodeURIComponent(results[2].replace(/\+/g, ' '))
}

function delParameterByName (name) {
  const url = window.location.href
  const urlParams = new URLSearchParams(url.split('?')[1])
  urlParams.delete(name)
  const newUrl = window.location.protocol + '//' + window.location.host + window.location.pathname + '?' + urlParams.toString()
  window.history.pushState({ path: newUrl }, '', newUrl)
}

function clearForm (form) {
  const inputs = form.querySelectorAll('input, select, textarea')

  inputs.forEach(input => {
    switch (input.type) {
      case 'text':
      case 'password':
      case 'textarea':
      case 'email':
      case 'url':
      case 'tel':
      case 'number':
        input.value = ''
        break
      case 'checkbox':
      case 'radio':
        input.checked = false
        break
      case 'select-one':
      case 'select-multiple':
        input.selectedIndex = -1
        break
      default:
        break
    }
  })
}

function focusElement (element) {
  if (element) {
    if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.contentEditable === 'true') {
      // Verifica se o elemento é um input, textarea ou é editável
      element.focus()
    } else {
      // Se não for um input, textarea ou editável, tenta focar no primeiro input dentro dele
      const input = element.querySelector('input, textarea, [contenteditable=true]')
      if (input) {
        input.focus()
      }
    }
  }
}

function isValidURL (string) {
  try {
    new URL(string)
    return true
  } catch (_) {
    return false
  }
}

function convertDateToISO (dateString) {
  // Dividir a string de data original
  const [day, month, year] = dateString.split('/')

  // Criar a nova string no formato Y-m-d
  const isoDateString = `${year}-${month}-${day}`

  return isoDateString
}

function convertDateTimeToISO (dateTimeString) {
  // Create a new Date object from the API date string
  const date = new Date(dateTimeString)

  // Format components
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0') // Months are 0-based
  const day = String(date.getDate()).padStart(2, '0')
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  const seconds = String(date.getSeconds()).padStart(2, '0')
  const milliseconds = String(date.getMilliseconds()).padStart(3, '0')

  // Combine them into the required format: yyyy-MM-ddThh:mm:ss.SSS
  const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}.${milliseconds}`

  return formattedDate
}

function convertToDatetimeLocal (dateStr, withTime = true) {
  // Criar um objeto Date a partir da string de data ISO
  const date = new Date(dateStr)

  // Obter os componentes da data
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')

  // Obter os componentes da hora
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  const seconds = String(date.getSeconds()).padStart(2, '0')

  // Combinar no formato compatível com datetime-local
  if (withTime) {
    return `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`
  }

  return `${year}-${month}-${day}`
}

function formatDate (isoDateStr, withTime = true) {
  const date = new Date(isoDateStr)

  // Extrair as partes da data
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0') // Os meses são baseados em zero
  const year = date.getFullYear()
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')

  // Combinar no formato desejado
  if (withTime) {
    return `${day}/${month}/${year} ${hours}:${minutes}`
  }

  return `${day}/${month}/${year}`
}

function showSuccess (el, data) {
  const alertDiv = document.createElement('div')
  alertDiv.className = 'alert alert-success mx-4 alert-dismissible fade show'
  alertDiv.setAttribute('role', 'alert')
  alertDiv.textContent = data.message
  el.appendChild(alertDiv)
}

function showErrors (el, data) {
  // Remove qualquer conteúdo anterior do elemento
  el.innerHTML = ''

  // Cria o elemento de alerta com as classes Bootstrap
  const alertDiv = document.createElement('div')
  alertDiv.className = 'alert alert-danger'
  alertDiv.setAttribute('role', 'alert')

  // Cria um contêiner flexível
  const flexDiv = document.createElement('div')
  flexDiv.className = 'd-flex'

  // Adiciona um elemento div para a mensagem principal
  const mainMessageDiv = document.createElement('div')
  const mainMessageStrong = document.createElement('strong')
  mainMessageStrong.textContent = 'Erro ao cadastrar:'
  mainMessageDiv.appendChild(mainMessageStrong)
  flexDiv.appendChild(mainMessageDiv)

  // Adiciona a mensagem principal ao alertDiv
  alertDiv.appendChild(flexDiv)

  // Cria um novo elemento ul para os erros específicos
  const errorList = document.createElement('ul')

  // Itera sobre os erros específicos e adiciona cada um como um li na ul
  for (const key in data.errors) {
    if (data.errors.hasOwnProperty(key)) {
      data.errors[key].forEach(errorMsg => {
        const errorItem = document.createElement('li')
        errorItem.textContent = errorMsg
        errorList.appendChild(errorItem)
      })
    }
  }

  // Adiciona a ul ao alertDiv
  alertDiv.appendChild(errorList)

  // Adiciona o alertDiv ao elemento
  el.appendChild(alertDiv)

  el.classList.remove('d-none')
  el.classList.add('d-block')
}

function clearErrors (el) {
  el.innerHTML = ''
  el.classList.remove('d-none')
  el.classList.add('d-none')
}

function getRoleName (role) {
  switch (role) {
    case 'admin':
      return 'Administração'
    case 'financeiro':
      return 'Financeiro'
    case 'vendas':
      return 'Vendas'
    case 'design':
      return 'Design e Arte'
    case 'producao':
      return 'Produção'
    default:
      return 'Nenhum'
  }
}

function skeletonLoading (paragraph = 1, lines = 5, margin = 'mb-3') {
  const skeletonContainer = document.createElement('div')
  skeletonContainer.classList.add(margin)

  for (let i = 0; i < paragraph; i++) {
    const paragraphElement = document.createElement('p')
    paragraphElement.classList.add('card-text', 'placeholder-glow')
    for (let j = 0; j < lines; j++) {
      const lineElement = document.createElement('span')
      lineElement.classList.add('placeholder', 'col-12', 'placeholder-lg')
      paragraphElement.appendChild(lineElement)
    }
    skeletonContainer.appendChild(paragraphElement)
  }
  return skeletonContainer
}

function getTomorrowDate () {
  // Crie uma nova data com a data atual
  const today = new Date()

  // Adicione um dia
  today.setDate(today.getDate() + 1)

  // Obtenha os componentes da data
  const year = today.getFullYear()
  const month = (today.getMonth() + 1).toString().padStart(2, '0') // O mês é base 0
  const day = today.getDate().toString().padStart(2, '0')

  // Formate a data no formato Y-m-d
  return `${year}-${month}-${day}`
}

export { getParameterByName, delParameterByName, clearForm, focusElement, isValidURL, convertDateToISO, convertDateTimeToISO, convertToDatetimeLocal, formatDate, showErrors, showSuccess, clearErrors, getRoleName, skeletonLoading, getTomorrowDate }
