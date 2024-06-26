class LoadingSpinner {
  constructor (size = 50, color = '#ffc21f') {
    this.spinner = null
    this.color = color
    this.size = size
    this.hasRendered = false
  }

  render () {
    this.spinner = document.createElement('div')
    this.spinner.innerHTML = `
      <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 50 50"
            class="spinner-svg"
            style="width: ${this.size}px; height: ${this.size}px;"
          >
            <circle
              class="path"
              cx="25"
              cy="25"
              r="20"
              fill="none"
              stroke-width="5"
              stroke-linecap="round"
              stroke="${this.color}"
            ></circle>
          </svg>
    `
    this.hasRendered = true

    return this.spinner
  }

  instance () {
    this.hasRendered = false

    return this.spinner
  }

  rendered () {
    return this.hasRendered
  }
}

export default LoadingSpinner
