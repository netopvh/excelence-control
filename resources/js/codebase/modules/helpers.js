import { Popover, Tooltip } from 'bootstrap'
import { ClassicEditor, InlineEditor } from 'ckeditor5/src/index.js'
import flatpickr from 'flatpickr'
import Swal from 'sweetalert2'
import { Datepicker } from 'vanillajs-datepicker'

// Helpers
export default class Helpers {
  /*
   * Run helpers
   *
   */
  static run (helpers, options = {}) {
    const helperList = {
      // Bootstrap
      'bs-tooltip': () => this.bsTooltip(),
      'bs-popover': () => this.bsPopover(),

      // Codebase
      'cb-toggle-class': () => this.cbToggleClass(),
      'cb-year-copy': () => this.cbYearCopy(),
      'cb-ripple': () => this.cbRipple(),
      'cb-print': () => this.cbPrint(),
      'cb-table-tools-sections': () => this.cbTableToolsSections(),
      'cb-table-tools-checkable': () => this.cbTableToolsCheckable(),
      'js-ckeditor5': () => this.jsCkeditor5(),
      'js-flatpickr': () => this.jsFlatpickr(),
      'jq-appear': (el) => this.jqAppear(el),
      'jq-magnific-popup': () => this.jqMagnific(),
      'jq-datepicker': () => this.jqDatepicker(),
      'date-input': () => this.dateInput(),
      'jq-masked-inputs': () => this.jqMaskedInputs(),
      'jq-select2': () => this.jqSelect2(),
      'jq-notify': (options) => this.jqNotify(options),
      'jq-alert': (options) => this.jqAlert(options),
      'jq-maxlength': () => this.jqMaxlength(),
      'jq-rangeslider': () => this.jqRangeslider()
    }

    if (helpers instanceof Array) {
      for (const index in helpers) {
        if (helperList[helpers[index]]) {
          helperList[helpers[index]](options)
        }
      }
    } else {
      if (helperList[helpers]) {
        helperList[helpers](options)
      }
    }
  }

  /*
   ********************************************************************************************
   *
   * Init helpers for Bootstrap plugins
   *
   *********************************************************************************************
   */

  /*
   * Bootstrap Tooltip, for more examples you can check out https://getbootstrap.com/docs/5.3/components/tooltips/
   *
   * Helpers.run('bs-tooltip');
   *
   * Example usage:
   *
   * <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" title="Tooltip Text">Example</button> or
   * <button type="button" class="btn btn-primary js-bs-tooltip" title="Tooltip Text">Example</button>
   *
   */
  static bsTooltip () {
    const elements = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]:not(.js-bs-tooltip-enabled), .js-bs-tooltip:not(.js-bs-tooltip-enabled)'))

    window.helperBsTooltips = elements.map(el => {
      // Add .js-bs-tooltip-enabled class to tag it as activated
      el.classList.add('js-bs-tooltip-enabled')

      // Init Bootstrap Tooltip
      return new Tooltip(el, {
        container: el.dataset.bsContainer || '#page-container',
        animation: !!(el.dataset.bsAnimation && el.dataset.bsAnimation.toLowerCase() === 'true')
      })
    })
  }

  /*
   * Bootstrap Popover, for more examples you can check out https://getbootstrap.com/docs/5.3/components/popovers/
   *
   * Helpers.run('bs-popover');
   *
   * Example usage:
   *
   * <button type="button" class="btn btn-primary" data-bs-toggle="popover" title="Popover Title" data-bs-content="This is the content of the Popover">Example</button> or
   * <button type="button" class="btn btn-primary js-bs-popover" title="Popover Title" data-bs-content="This is the content of the Popover">Example</button>
   *
   */
  static bsPopover () {
    const elements = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]:not(.js-bs-popover-enabled), .js-bs-popover:not(.js-bs-popover-enabled)'))

    window.helperBsPopovers = elements.map(el => {
      // Add .js-bs-popover-enabled class to tag it as activated
      el.classList.add('js-bs-popover-enabled')

      // Init Bootstrap Popover
      return new Popover(el, {
        container: el.dataset.bsContainer || '#page-container',
        animation: !!(el.dataset.bsAnimation && el.dataset.bsAnimation.toLowerCase() === 'true'),
        trigger: el.dataset.bsTrigger || 'hover focus'
      })
    })
  }

  /*
   ********************************************************************************************
   *
   * JS helpers to add custom functionality
   *
   *********************************************************************************************
   */

  /*
   * Toggle class on element click
   *
   * Helpers.run('cb-toggle-class');
   *
   * Example usage (on button click, "exampleClass" class is toggled on the element with id "elementID"):
   *
   * <button type="button" class="btn btn-primary" data-toggle="class-toggle" data-target="#elementID" data-class="exampleClass">Toggle</button>
   *
   * or
   *
   * <button type="button" class="btn btn-primary js-class-toggle" data-target="#elementID" data-class="exampleClass">Toggle</button>
   *
   */
  static cbToggleClass () {
    const elements = document.querySelectorAll('[data-toggle="class-toggle"]:not(.js-class-toggle-enabled), .js-class-toggle:not(.js-class-toggle-enabled)')

    elements.forEach(el => {
      el.addEventListener('click', () => {
        // Add .js-class-toggle-enabled class to tag it as activated
        el.classList.add('js-class-toggle-enabled')

        // Get all classes
        const cssClasses = el.dataset.class ? el.dataset.class.split(' ') : false

        // Toggle class on target elements
        document.querySelectorAll(el.dataset.target).forEach(targetEl => {
          if (cssClasses) {
            cssClasses.forEach(cls => {
              targetEl.classList.toggle(cls)
            })
          }
        })
      })
    })
  }

  /*
   * Add the correct copyright year to an element
   *
   * Helpers.run('cb-year-copy');
   *
   * Example usage (it will get populated with current year if empty or will append it to specified year if needed):
   *
   * <span data-toggle="year-copy"></span> or
   * <span data-toggle="year-copy">2018</span>
   *
   */
  static cbYearCopy () {
    const elements = document.querySelectorAll('[data-toggle="year-copy"]:not(.js-year-copy-enabled)')

    elements.forEach(el => {
      const date = new Date()
      const currentYear = date.getFullYear()
      const baseYear = el.textContent || currentYear

      // Add .js-year-copy-enabled class to tag it as activated
      el.classList.add('js-year-copy-enabled')

      // Set the correct year
      el.textContent = (parseInt(baseYear) >= currentYear) ? currentYear : baseYear + '-' + currentYear.toString().substring(2, 4)
    })
  }

  /*
   * Ripple effect fuctionality
   *
   * Helpers.run('cb-ripple');
   *
   * Example usage:
   *
   * <button type="button" class="btn btn-primary" data-toggle="click-ripple">Click Me!</button>
   *
   */
  static cbRipple () {
    const elements = document.querySelectorAll('[data-toggle="click-ripple"]:not(.js-click-ripple-enabled)')

    elements.forEach(el => {
      // Add .js-click-ripple-enabled class to tag it as activated and init it
      el.classList.add('js-click-ripple-enabled')

      // Add custom CSS styles
      el.style.overflow = 'hidden'
      el.style.position = 'relative'
      el.style.zIndex = 1

      // On click create and render the ripple
      el.addEventListener('click', e => {
        const cssClass = 'click-ripple'
        let ripple = el.querySelector('.' + cssClass)
        let d
        let x = 0
        let y = 0

        // If the ripple element exists in this element, remove .animate class from ripple element..
        if (ripple) {
          ripple.classList.remove('animate')
        } else { // ..else add it
          const elChild = document.createElement('span')

          elChild.classList.add(cssClass)
          el.insertBefore(elChild, el.firstChild)
        }

        // Get the ripple element
        ripple = el.querySelector('.' + cssClass)

        // If the ripple element doesn't have dimensions, set them accordingly
        if ((getComputedStyle(ripple).height === '0px') || (getComputedStyle(ripple).width === '0px')) {
          d = Math.max(el.offsetWidth, el.offsetHeight)

          ripple.style.height = d + 'px'
          ripple.style.width = d + 'px'
        }

        // Get coordinates for our ripple element
        x = e.pageX - (el.getBoundingClientRect().left + window.scrollX) - parseFloat(getComputedStyle(ripple).width.replace('px', '')) / 2
        y = e.pageY - (el.getBoundingClientRect().top + window.scrollY) - parseFloat(getComputedStyle(ripple).height.replace('px', '')) / 2

        // Position the ripple element and add the class .animate to it
        ripple.style.top = y + 'px'
        ripple.style.left = x + 'px'
        ripple.classList.add('animate')
      })
    })
  }

  /*
   * Print Page functionality
   *
   * Helpers.run('cb-print');
   *
   */
  static cbPrint () {
    // Store all #page-container classes
    const lPage = document.getElementById('page-container')
    const pageCls = lPage.className

    console.log(pageCls)

    // Remove all classes from #page-container
    lPage.classList = ''

    // Print the page
    window.print()

    // Restore all #page-container classes
    lPage.classList = pageCls
  }

  /*
   * Table sections functionality
   *
   * Helpers.run('cb-table-tools-sections');
   *
   * Example usage:
   *
   * Please check out the Table Helpers page for complete markup examples
   *
   */
  static cbTableToolsSections () {
    const tables = document.querySelectorAll('.js-table-sections:not(.js-table-sections-enabled)')

    tables.forEach(table => {
      // Add .js-table-sections-enabled class to tag it as activated
      table.classList.add('js-table-sections-enabled')

      // When a row is clicked in tbody.js-table-sections-header
      table.querySelectorAll('.js-table-sections-header > tr').forEach(tr => {
        tr.addEventListener('click', e => {
          if (e.target.type !== 'checkbox' &&
            e.target.type !== 'button' &&
            e.target.tagName.toLowerCase() !== 'a' &&
            e.target.parentNode.nodeName.toLowerCase() !== 'a' &&
            e.target.parentNode.nodeName.toLowerCase() !== 'button' &&
            e.target.parentNode.nodeName.toLowerCase() !== 'label' &&
            !e.target.parentNode.classList.contains('custom-control')) {
            const tbody = tr.parentNode
            const tbodyAll = table.querySelectorAll('tbody')

            if (!tbody.classList.contains('show')) {
              if (tbodyAll) {
                tbodyAll.forEach(tbodyEl => {
                  tbodyEl.classList.remove('show')
                  tbodyEl.classList.remove('table-active')
                })
              }
            }

            tbody.classList.toggle('show')
            tbody.classList.toggle('table-active')
          }
        })
      })
    })
  }

  /*
   * Checkable table functionality
   *
   * Helpers.run('cb-table-tools-checkable');
   *
   * Example usage:
   *
   * Please check out the Table Helpers page for complete markup examples
   *
   */
  static cbTableToolsCheckable () {
    const tables = document.querySelectorAll('.js-table-checkable:not(.js-table-checkable-enabled)')

    tables.forEach(table => {
      // Add .js-table-checkable-enabled class to tag it as activated
      table.classList.add('js-table-checkable-enabled')

      // When a checkbox is clicked in thead
      table.querySelector('thead input[type=checkbox]').addEventListener('click', e => {
        // Check or uncheck all checkboxes in tbody
        table.querySelectorAll('tbody input[type=checkbox]').forEach(checkbox => {
          checkbox.checked = e.currentTarget.checked

          // Update Row classes
          this.tableToolscheckRow(checkbox, e.currentTarget.checked)
        })
      })

      // When a checkbox is clicked in tbody
      table.querySelectorAll('tbody input[type=checkbox], tbody input + label').forEach(checkbox => {
        checkbox.addEventListener('click', e => {
          const checkboxHead = table.querySelector('thead input[type=checkbox]')

          // Adjust checkbox in thead
          if (!checkbox.checked) {
            checkboxHead.checked = false
          } else {
            if (table.querySelectorAll('tbody input[type=checkbox]:checked').length === table.querySelectorAll('tbody input[type=checkbox]').length) {
              checkboxHead.checked = true
            }
          }

          // Update Row classes
          this.tableToolscheckRow(checkbox, checkbox.checked)
        })
      })

      // When a row is clicked in tbody
      table.querySelectorAll('tbody > tr').forEach(tr => {
        tr.addEventListener('click', e => {
          if (e.target.type !== 'checkbox' &&
            e.target.type !== 'button' &&
            e.target.tagName.toLowerCase() !== 'a' &&
            e.target.parentNode.nodeName.toLowerCase() !== 'a' &&
            e.target.parentNode.nodeName.toLowerCase() !== 'button' &&
            e.target.parentNode.nodeName.toLowerCase() !== 'label' &&
            !e.target.parentNode.classList.contains('custom-control')) {
            const checkboxHead = table.querySelector('thead input[type=checkbox]')
            const checkbox = e.currentTarget.querySelector('input[type=checkbox]')

            // Update row's checkbox status
            checkbox.checked = !checkbox.checked

            // Update Row classes
            this.tableToolscheckRow(checkbox, checkbox.checked)

            // Adjust checkbox in thead
            if (!checkbox.checked) {
              checkboxHead.checked = false
            } else {
              if (table.querySelectorAll('tbody input[type=checkbox]:checked').length === table.querySelectorAll('tbody input[type=checkbox]').length) {
                checkboxHead.checked = true
              }
            }
          }
        })
      })
    })
  }

  // Checkable table functionality helper - Checks or unchecks table row
  static tableToolscheckRow (checkbox, checkedStatus) {
    if (checkedStatus) {
      checkbox.closest('tr').classList.add('table-active')
    } else {
      checkbox.closest('tr').classList.remove('table-active')
    }
  }

  /*
   * CKEditor 5 init, for more examples you can check out http://ckeditor.com/
   *
   * Helpers.run('js-ckeditor5');
   *
   * Example usage:
   *
   * <div id="js-ckeditor5-classic">Hello classic CKEditor 5!</div>
   * ..or..
   * <div id="js-ckeditor5-inline">Hello inline CKEditor 5!</div>
   *
   */
  static jsCkeditor5 () {
    const ckeditor5Inline = document.querySelector('#js-ckeditor5-inline')
    const ckeditor5Full = document.querySelector('#js-ckeditor5-classic')

    // Init inline text editor
    if (ckeditor5Inline) {
      InlineEditor
        .create(document.querySelector('#js-ckeditor5-inline'))
        .then(editor => {
          window.editor = editor
        })
        .catch(error => {
          console.error('There was a problem initializing the inline editor.', error)
        })
    }

    // Init full text editor
    if (ckeditor5Full) {
      ClassicEditor
        .create(document.querySelector('#js-ckeditor5-classic'))
        .then(editor => {
          window.editor = editor
        })
        .catch(error => {
          console.error('There was a problem initializing the classic editor.', error)
        })
    }
  }

  /*
   * Flatpickr init, for more examples you can check out https://github.com/flatpickr/flatpickr
   *
   * Helpers.run('js-flatpickr');
   *
   * Example usage:
   *
   * <input type="text" class="js-flatpickr form-control">
   *
   */
  static jsFlatpickr () {
    const elements = document.querySelectorAll('.js-flatpickr:not(.js-flatpickr-enabled)')

    elements.forEach(el => {
      // Add .js-flatpickr-enabled class to tag it as activated
      el.classList.add('js-flatpickr-enabled')

      // Init it
      flatpickr(el)
    })
  }

  /*
   ********************************************************************************************
   *
   * Init helpers for jQuery plugins
   *
   ********************************************************************************************
   */

  /*
   * jQuery Appear, for more examples you can check out https://github.com/bas2k/jquery.appear
   *
   * Helpers.run('jq-appear');
   *
   * Example usage (the following div will appear on scrolling, remember to add the invisible class):
   *
   * <div class="invisible" data-toggle="appear">...</div>
   *
   */
  static jqAppear (elName) {
    const el = document.getElementById(elName)

    if (el) {
      el.classList.remove('d-none')

      // Adiciona a classe slidedown para iniciar a animação
      el.classList.add('slidedown')

      // Força um reflow para garantir que a classe slidedown é aplicada
      el.offsetHeight

      // Adiciona a classe slidedown-active para iniciar a transição
      el.classList.add('slidedown-active')

      // Função para remover as classes após a transição
      const transitionEndHandler = () => {
        el.classList.remove('slidedown')
        el.classList.remove('slidedown-active')
        el.removeEventListener('transitionend', transitionEndHandler)
      }

      // Adiciona event listener para transição
      el.addEventListener('transitionend', transitionEndHandler, { once: true })
    }
  }

  static jqAlert (options) {
    Swal.fire(options)
  }

  /*
   * Magnific Popup functionality, for more examples you can check out http://dimsemenov.com/plugins/magnific-popup/
   *
   * Helpers.run('jq-magnific-popup');
   *
   * Example usage:
   *
   * Please check out the Gallery page in Components for complete markup examples
   *
   */
  static jqMagnific () {
    // Gallery init
    window.jQuery('.js-gallery:not(.js-gallery-enabled)').each((index, element) => {
      // Add .js-gallery-enabled class to tag it as activated and init it
      window.jQuery(element).addClass('js-gallery-enabled').magnificPopup({
        delegate: 'a.img-lightbox',
        type: 'image',
        gallery: {
          enabled: true
        }
      })
    })
  }

  /*
   * Bootstrap Datepicker init, for more examples you can check out https://github.com/eternicode/bootstrap-datepicker
   *
   * Helpers.run('jq-datepicker');
   *
   * Example usage:
   *
   * <input type="text" class="js-datepicker form-control">
   *
   */
  static jqDatepicker () {
    // Init datepicker (with .js-datepicker and .input-daterange class)
    window.jQuery('.js-datepicker:not(.js-datepicker-enabled)').add('.input-daterange:not(.js-datepicker-enabled)').each((index, element) => {
      const el = window.jQuery(element)

      window.jQuery.fn.datepicker.dates.pt = {
        days: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sabado', 'Domingo'],
        daysShort: ['Dom, ', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom'],
        daysMin: ['Do', 'Se', 'Te', 'Qu', 'Qu', 'Se', 'Sa', 'Do'],
        months: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julio', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
        monthsShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dec'],
        today: 'Hoje'
      }

      // Add .js-datepicker-enabled class to tag it as activated and init it
      el.addClass('js-datepicker-enabled').datepicker({
        language: 'pt',
        weekStart: el.data('week-start') || 0,
        autoclose: el.data('autoclose') || false,
        todayHighlight: el.data('today-highlight') || false,
        startDate: el.data('start-date') || false,
        container: el.data('container') || '#page-container',
        orientation: 'bottom' // Position issue when using BS5, set it to bottom until officially supported
      })
    })
  }

  static dateInput () {
    const elem = document.querySelector('.datepicker')
    const datepicker = new Datepicker(elem, {
      format: 'dd/mm/yyyy'
    })
  }

  /*
   * Masked Inputs, for more examples you can check out https://github.com/digitalBush/jquery.maskedinput
   *
   * Helpers.run('jq-masked-inputs');
   *
   * Example usage:
   *
   * Please check out the Form plugins page for complete markup examples
   *
   */
  static jqMaskedInputs () {
    // Init Masked Inputs
    // a - Represents an alpha character (A-Z,a-z)
    // 9 - Represents a numeric character (0-9)
    // * - Represents an alphanumeric character (A-Z,a-z,0-9)
    window.jQuery('.js-masked-date:not(.js-masked-enabled)').mask('99/99/9999')
    window.jQuery('.js-masked-date-dash:not(.js-masked-enabled)').mask('99-99-9999')
    window.jQuery('.js-masked-phone:not(.js-masked-enabled)').mask('(999) 999-9999')
    window.jQuery('.js-masked-phone-ext:not(.js-masked-enabled)').mask('(999) 999-9999? x99999')
    window.jQuery('.js-masked-taxid:not(.js-masked-enabled)').mask('99-9999999')
    window.jQuery('.js-masked-ssn:not(.js-masked-enabled)').mask('999-99-9999')
    window.jQuery('.js-masked-pkey:not(.js-masked-enabled)').mask('a*-999-a999')
    window.jQuery('.js-masked-time:not(.js-masked-enabled)').mask('99:99')

    window.jQuery('.js-masked-date')
      .add('.js-masked-date-dash')
      .add('.js-masked-phone')
      .add('.js-masked-phone-ext')
      .add('.js-masked-taxid')
      .add('.js-masked-ssn')
      .add('.js-masked-pkey')
      .add('.js-masked-time')
      .addClass('js-masked-enabled')
  }

  /*
   * Select2, for more examples you can check out https://github.com/select2/select2
   *
   * Helpers.run('jq-select2');
   *
   * Example usage:
   *
   * <select class="js-select2 form-control" style="width: 100%;" data-placeholder="Choose one..">
   *   <option></option><!-- Required for data-placeholder attribute to work with Select2 plugin -->
   *   <option value="1">HTML</option>
   *   <option value="2">CSS</option>
   *   <option value="3">Javascript</option>
   * </select>
   *
   */
  static jqSelect2 () {
    // Init Select2 (with .js-select2 class)
    window.jQuery('.js-select2:not(.js-select2-enabled)').each((index, element) => {
      const el = window.jQuery(element)

      // Add .js-select2-enabled class to tag it as activated and init it
      el.addClass('js-select2-enabled').select2({
        placeholder: el.data('placeholder') || false,
        dropdownParent: document.querySelector(el.data('container') || '#page-container')
      })
    })
  }

  /*
   * Bootstrap Notify, for more examples you can check out http://bootstrap-growl.remabledesigns.com/
   *
   * Helpers.run('jq-notify');
   *
   * Example usage:
   *
   * Please check out the Notifications page for examples
   *
   */
  static jqNotify (options = {}) {
    if (window.jQuery.isEmptyObject(options)) {
      // Init notifications (with .js-notify class)
      window.jQuery('.js-notify:not(.js-notify-enabled)').each((index, element) => {
        // Add .js-notify-enabled class to tag it as activated and init it
        window.jQuery(element).addClass('js-notify-enabled').on('click.pixelcave.helpers', e => {
          const el = window.jQuery(e.currentTarget)

          // Create notification
          window.jQuery.notify({
            icon: el.data('icon') || '',
            message: el.data('message'),
            url: el.data('url') || ''
          },
          {
            element: 'body',
            type: el.data('type') || 'info',
            placement: {
              from: el.data('from') || 'top',
              align: el.data('align') || 'right'
            },
            allow_dismiss: true,
            newest_on_top: true,
            showProgressbar: false,
            offset: 20,
            spacing: 10,
            z_index: 1033,
            delay: 5000,
            timer: 1000,
            animate: {
              enter: 'animated fadeIn',
              exit: 'animated fadeOutDown'
            },
            template: `<div data-notify="container" class="col-11 col-sm-4 alert alert-{0} alert-dismissible" role="alert">
  <p class="mb-0">
    <span data-notify="icon"></span>
    <span data-notify="title">{1}</span>
    <span data-notify="message">{2}</span>
  </p>
  <div class="progress" data-notify="progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
    <div class="progress-bar progress-bar-{0}" style="width: 0%;"></div>
  </div>
  <a href="{3}" target="{4}" data-notify="url"></a>
  <a class="p-2 m-1 text-dark" href="javascript:void(0)" aria-label="Close" data-notify="dismiss">
    <i class="fa fa-times"></i>
  </a>
</div>`
          })
        })
      })
    } else {
      // Create notification
      window.jQuery.notify({
        icon: options.icon || '',
        message: options.message,
        url: options.url || ''
      },
      {
        element: options.element || 'body',
        type: options.type || 'info',
        placement: {
          from: options.from || 'top',
          align: options.align || 'right'
        },
        allow_dismiss: options.allow_dismiss !== false,
        newest_on_top: options.newest_on_top !== false,
        showProgressbar: !!options.show_progress_bar,
        offset: options.offset || 20,
        spacing: options.spacing || 10,
        z_index: options.z_index || 1033,
        delay: options.delay || 5000,
        timer: options.timer || 1000,
        animate: {
          enter: options.animate_enter || 'animated fadeIn',
          exit: options.animate_exit || 'animated fadeOutDown'
        },
        template: `<div data-notify="container" class="col-11 col-sm-4 alert alert-{0} alert-dismissible" role="alert">
  <p class="mb-0">
    <span data-notify="icon"></span>
    <span data-notify="title">{1}</span>
    <span data-notify="message">{2}</span>
  </p>
  <div class="progress" data-notify="progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
    <div class="progress-bar progress-bar-{0}" style="width: 0%;"></div>
  </div>
  <a href="{3}" target="{4}" data-notify="url"></a>
  <a class="p-2 m-1 text-dark" href="javascript:void(0)" aria-label="Close" data-notify="dismiss">
    <i class="fa fa-times"></i>
  </a>
</div>`
      })
    }
  }

  /*
   * Bootstrap Maxlength, for more examples you can check out https://github.com/mimo84/bootstrap-maxlength
   *
   * Helpers.run('jq-maxlength');
   *
   * Example usage:
   *
   * <input type="text" class="js-maxlength form-control" maxlength="20">
   *
   */
  static jqMaxlength () {
    // Init Bootstrap Maxlength (with .js-maxlength class)
    window.jQuery('.js-maxlength:not(.js-maxlength-enabled)').each((index, element) => {
      const el = window.jQuery(element)

      // Add .js-maxlength-enabled class to tag it as activated and init it
      el.addClass('js-maxlength-enabled').maxlength({
        alwaysShow: !!el.data('always-show'),
        threshold: el.data('threshold') || 10,
        warningClass: el.data('warning-class') || 'badge bg-warning',
        limitReachedClass: el.data('limit-reached-class') || 'badge bg-danger',
        placement: el.data('placement') || 'bottom',
        preText: el.data('pre-text') || '',
        separator: el.data('separator') || '/',
        postText: el.data('post-text') || ''
      })
    })
  }

  /*
   * Ion Range Slider, for more examples you can check out https://github.com/IonDen/ion.rangeSlider
   *
   * Helpers.run('jq-rangeslider');
   *
   * Example usage:
   *
   * <input type="text" class="js-rangeslider form-control" value="50">
   *
   */
  static jqRangeslider () {
    // Init Ion Range Slider (with .js-rangeslider class)
    window.jQuery('.js-rangeslider:not(.js-rangeslider-enabled)').each((index, element) => {
      const el = window.jQuery(element)

      // Add .js-rangeslider-enabled class to tag it as activated and init it
      window.jQuery(element).addClass('js-rangeslider-enabled').ionRangeSlider({
        input_values_separator: ';',
        skin: el.data('skin') || 'round'
      })
    })
  }
}
