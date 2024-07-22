import { Modal as BootstrapModal } from 'bootstrap'
class Modal {
  constructor (id, title = '', content = '', size = 'modal-lg') {
    this.id = id
    this.title = title
    this.content = content
    this.size = size
    this.modal = null // Armazenar a referência do modal
  }

  render () {
    if (!this.modal) {
      this.modal = document.createElement('div')
      this.modal.classList.add('modal', 'fade')
      this.modal.id = this.id
      this.modal.setAttribute('data-bs-backdrop', 'static')
      this.modal.setAttribute('tabindex', '-1')
      this.modal.setAttribute('role', 'dialog')
      this.modal.setAttribute('aria-labelledby', `${this.id}Label`)
      this.modal.setAttribute('aria-hidden', 'true')

      this.modal.innerHTML = `
          <div class="modal-dialog ${this.size}" role="document">
            <div class="modal-content">
              <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                  <h3 class="block-title">${this.title}</h3>
                </div>
                <div class="block-content">
                  ${this.content}
                </div>
              </div>
            </div>
          </div>
        `
    }

    return this.modal
  }

  setTitle (title) {
    this.title = title
    if (this.modal) {
      this.modal.querySelector('.block-title').textContent = this.title
    }
  }

  setContent (content) {
    this.content = content
    if (this.modal) {
      this.modal.querySelector('.block-content').innerHTML = this.content
    }
  }

  appendContent (element) {
    if (this.modal) {
      this.modal.querySelector('.block-content').appendChild(element)
    }
  }

  clearContent () {
    this.content = ''
    if (this.modal) {
      this.modal.querySelector('.block-content').innerHTML = ''
    }
  }

  setSize (size) {
    this.size = size
    if (this.modal) {
      const dialog = this.modal.querySelector('.modal-dialog')
      dialog.className = `modal-dialog ${this.size}`
    }
  }

  show () {
    const modalInstance = new BootstrapModal(this.modal)
    modalInstance.show()
  }

  hide () {
    const modalInstance = BootstrapModal.getInstance(this.modal)
    if (modalInstance) {
      modalInstance.hide()
      document.body.removeChild(this.modal)
    }
  }
}

export default Modal
