import { Modal } from 'bootstrap'
import { get, patch } from '../../codebase/api'
import { skeletonLoading } from '../../codebase/utils'

class pageShowKanban {
  static modalOrderInfo = null

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
    cardDiv.addEventListener('click', () => this.showOrderInfo(card))
    return cardDiv
  }

  static async showOrderInfo (order) {
    const modal = document.getElementById('orderInfoModal')

    if (!this.modalOrderInfo) {
      this.modalOrderInfo = new Modal(document.getElementById('orderInfoModal'))
    }

    const modalBody = modal.querySelector('.block-content')

    this.modalOrderInfo.show()

    modalBody.innerHTML = ''
    modalBody.appendChild(skeletonLoading(3, 3))

    const response = await get(`/api/order/${order.id}`)

    if (response.success) {
      modalBody.innerHTML = `
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
                                <div class="fw-bold mb-1">${response.data.customer.name}</div>
                                <address>
                                    <i class="fa fa-phone me-1"></i>
                                    ${response.data.customer.phone ? response.data.customer.phone : 'Não cadastrado'}<br>
                                    <i class="far fa-envelope me-1"></i> <a
                                        href="javascript:void(0)">${response.data.customer.email ? response.data.customer.email : 'Não cadastrado'}</a>
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
                <table class="table table-bordered table-striped table-vcenter">
                  <thead>
                    <tr>
                      <th class="text-uppercase fw-bold">Nome</th>
                      <th class="text-uppercase fw-bold">Quantidade</th>
                      <th class="text-uppercase fw-bold">Fornecedor</th>
                      <th class="text-uppercase fw-bold">Observação</th>
                      <th class="text-uppercase fw-bold">Status</th>
                      <td class="text-uppercase fw-bold">Arte</td>
                    </tr>
                  </thead>
                  <tbody>
                  ${response.data.order_products.map(item => `
                    <tr>
                      <td>${item.product.name}</td>
                      <td>${item.qtd}</td>
                      <td>${item.supplier ? item.supplier : '-'}</td>
                      <td>${item.obs ? item.obs : '-'}</td>
                      <td><span class="badge bg-success">Aprovado</span></td>
                      <td><a href="#" class="btn btn-sm btn-primary">Visualizar</a></td>
                    </tr>
                    `).join('')}
                  </tbody>
                </table>
            </div>
          </div>
        </div>
      `
    }
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
      const newStatus = newColumn.id.replace('-cards', '')
      newColumn.appendChild(cardElement)

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
