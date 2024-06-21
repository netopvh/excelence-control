import { get, patch } from '../../codebase/api'

class pageShowKanban {
  static initPage () {
    get('/dashboard/order/list-kanban')
      .then(data => {
        data.forEach(card => {
          const cardElement = this.createCardElement(card)
          const columnCards = document.getElementById(`${card.step}-cards`)
          if (columnCards) {
            columnCards.insertBefore(cardElement, columnCards.firstChild)
          } else {
            console.error(`Column with ID ${card.step}-cards not found.`)
          }
        })
      })
      .catch(error => console.error('Error fetching cards:', error))

    // Attach event listeners to columns
    const columns = document.querySelectorAll('.kanban-column')
    columns.forEach(column => {
      column.addEventListener('drop', this.handleDrop.bind(this))
      column.addEventListener('dragover', this.handleDragOver)
    })

    const filters = document.querySelectorAll('.kanban-filter')
    filters.forEach(filter => {
      filter.addEventListener('input', this.handleFilterInput.bind(this))
    })
  }

  static createCardElement (card) {
    const cardDiv = document.createElement('div')
    cardDiv.className = 'kanban-card'
    cardDiv.id = `card-${card.id}`
    cardDiv.draggable = true
    cardDiv.innerHTML =
          `<strong>N. do Pedido:</strong> #${card.number} <br> <strong>Data:</strong> ${card.date} <br> <strong>Cliente:</strong> ${card.customer}`
    cardDiv.addEventListener('dragstart', this.handleDragStart)
    return cardDiv
  }

  static handleDragStart (event) {
    event.dataTransfer.setData('text/plain', event.target.id)
  }

  static async handleDrop (event) {
    event.preventDefault()
    const cardId = event.dataTransfer.getData('text/plain')
    const cardElement = document.getElementById(cardId)
    const newColumn = event.target.closest('.kanban-column')

    if (newColumn) {
      const newStep = newColumn.id.replace('-cards', '')
      newColumn.insertBefore(cardElement, newColumn.firstChild)

      try {
        await patch(`/dashboard/order/list-kanban/${cardId.replace('card-', '')}`, {
          step: newStep
        })
      } catch (error) {
        console.error('Error updating card status:', error)
      }
    }
  }

  static handleDragOver (event) {
    event.preventDefault()
  }

  static handleFilterInput (event) {
    const columnId = event.target.dataset.column
    const filterType = event.target.dataset.filter
    const filterText = event.target.value.trim().toLowerCase()
    const cards = document.querySelectorAll(`#${columnId}-cards .kanban-card`)

    cards.forEach(card => {
      const cardInfo = card.textContent.toLowerCase()
      let shouldShow = true

      if (filterType === 'number') {
        const cardNumberMatch = cardInfo.match(/N\. do Pedido:\s*#(\d+)/i)
        if (cardNumberMatch && cardNumberMatch.length > 1) {
          const cardNumber = cardNumberMatch[1]
          shouldShow = cardNumber.includes(filterText)
        } else {
          shouldShow = false
        }
      } else if (filterType === 'customer') {
        const customerInfoMatch = cardInfo.match(/Cliente:\s*(.*)/i)
        if (customerInfoMatch && customerInfoMatch.length > 1) {
          const customerName = customerInfoMatch[1]
          shouldShow = customerName.includes(filterText)
        } else {
          shouldShow = false
        }
      }

      card.style.display = shouldShow ? 'block' : 'none'
    })
  }

  static init () {
    this.initPage()
  }
}

window.Codebase.onLoad(() => pageShowKanban.init())
window.Codebase.helpersOnLoad(['jq-appear'])
