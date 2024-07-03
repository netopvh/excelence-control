class Button {
  constructor (text, onClick = null, classname = '', type = 'button', form = null, loading = false) {
    this.text = text
    this.onClick = onClick
    this.loading = loading
    this.class = classname
    this.type = type
    this.button = null // Armazenar a referência do botão
    this.form = form
  }

  render () {
    if (!this.button) {
      this.button = document.createElement('button')
      this.button.textContent = this.text

      if (this.type) {
        this.button.type = this.type
      }

      if (this.class) {
        this.class.split(' ').forEach(cls => this.button.classList.add(cls))
      }

      if (this.form) {
        this.button.setAttribute('form', this.form)
      }

      if (this.onClick) {
        this.button.addEventListener('click', this.onClick)
      }
    }

    this.updateLoadingState()

    return this.button
  }

  updateLoadingState () {
    if (this.loading) {
      this.button.classList.add('disabled')
      this.button.disabled = true
      this.button.textContent = 'Processando...'
    } else {
      this.button.classList.remove('disabled')
      this.button.disabled = false
      this.button.textContent = this.text
    }
  }

  setLoading (loading) {
    this.loading = loading
    if (this.button) {
      this.updateLoadingState()
    }
  }

  setOnClick (onClick) {
    this.onClick = onClick
    if (this.button) {
      this.button.addEventListener('click', this.onClick)
    }
  }
}

export default Button
