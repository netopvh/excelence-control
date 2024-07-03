function getParameterByName (name) {
  name = name.replace(/[\[\]]/g, '\\$&')
  const url = window.location.href
  const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)')
  const results = regex.exec(url)
  if (!results) return null
  if (!results[2]) return ''
  return decodeURIComponent(results[2].replace(/\+/g, ' '))
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

export { getParameterByName, clearForm, focusElement, isValidURL, convertDateToISO, showErrors, clearErrors, getRoleName }
