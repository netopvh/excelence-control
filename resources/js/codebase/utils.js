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

export { getParameterByName, clearForm, focusElement, isValidURL }
