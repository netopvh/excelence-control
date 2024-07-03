import DataTable from 'datatables.net-bs5'
import 'datatables.net-bs5/css/dataTables.bootstrap5.css'
import { clearErrors, clearForm, getRoleName, showErrors } from '../../codebase/utils'
import { Modal } from 'bootstrap'
import Button from '../../codebase/components/button'
import { get, post, put } from '../../codebase/api'
import Swal from 'sweetalert2'
import { Responses } from '../../codebase/constants'

class pageUser {
  static userModal = null
  static userEditModal = null
  static passwordModal = null
  static tableUsers = null

  static initDataTables () {
    const tableUsers = document.querySelector('.list-user')
    this.tableUsers = new DataTable(tableUsers, {
      serverSide: true,
      processing: true,
      paging: false,
      searching: false,
      pageLength: 50,
      lengthMenu: [[5, 10, 20, 40, 50, 80, 100], [5, 10, 20, 40, 50, 80, 100]],
      autoWidth: false,
      dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
      ajax: {
        url: '/api/user/list',
        type: 'GET'
      },
      columns: [
        { data: 'name', name: 'name' },
        { data: 'username', name: 'username' },
        { data: 'email', name: 'email' },
        {
          data: 'roles',
          name: 'roles'
        },
        { data: 'created_at', name: 'created_at' },
        {
          data: 'action',
          name: 'action',
          orderable: false,
          searchable: false,
          render: (data) => {
            return `
            <div class="btn-group">
              <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-id="${data}" id="password-user-${data}" title="Alterar Senha">
                <i class="fa fa-key"></i>
              </button>
              <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-id="${data}" id="edit-user-${data}" title="Editar Usuário">
                <i class="fa fa-edit"></i>
              </button>
            </div>
            `
          }
        }
      ],
      language: {
        url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json'
      },
      drawCallback: function () {
        const api = this.api()
        api.rows().every(function () {
          const data = this.data()
          document.querySelector(`#password-user-${data.id}`).addEventListener('click', () => {
            pageUser.showModalPassword(data.id)
          })
          document.querySelector(`#edit-user-${data.id}`).addEventListener('click', () => {
            pageUser.editUserModal(data.id)
          })
          return true
        })
      }
    })
  }

  static addUserModal () {
    document.getElementById('add-user').addEventListener('click', function (event) {
      event.preventDefault()

      const modal = document.getElementById('userModal')

      if (!pageUser.userModal) {
        pageUser.userModal = new Modal(modal)
      }

      const modalBody = modal.querySelector('.modal-content')

      modalBody.innerHTML = `
        <div class="block block-rounded">
          <div class="block-header block-header-default">
            <h3 class="block-title fw-bold">
              Cadastrar Usuário
            </h3>
          </div>
          <div class="block-content">
            <div class="row items-push">
              <div class="col-md-12">
                <div class="block block-rounded h-100 mb-0">
                  <div class="block-content fs-md">
                    <form id="form-user" method="POST">
                      <div class="mb-3">
                        <label class="form-label" for="name">Nome:</label>
                        <input type="text" class="form-control" id="name" name="name">
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="username">Nome de Usuário:</label>
                        <input type="text" class="form-control" id="username" name="username">
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="email">E-mail:</label>
                        <input type="email" class="form-control" id="email" name="email">
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="password">Senha:</label>
                        <input type="password" class="form-control" id="password" name="password">
                      </div>
                    </form>
                    <div id="error-container" class="d-none">
                      <div class="alert alert-danger" role="alert">
                        <div class="d-flex">
                          <div>
                            <strong>Erro ao cadastrar:</strong>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="d-flex gap-2 mb-4 px-4">
            <div class="col-12 col-md-6" id="btn-submit-container">
            </div>
            <div class="col-12 col-md-6" id="btn-cancel-container">
            </div>
          </div>
        </div>
      `

      const btnSubmit = new Button('Salvar', null, 'btn btn-primary w-100', 'submit', 'form-user')
      const btnCancel = new Button('Cancelar', null, 'btn btn-danger w-100')

      document.getElementById('btn-submit-container').appendChild(btnSubmit.render())
      document.getElementById('btn-cancel-container').appendChild(btnCancel.render())

      pageUser.userModal.show()

      const formUser = document.getElementById('form-user')
      const errorContainer = document.getElementById('error-container')

      if (formUser) {
        clearErrors(errorContainer)
        clearForm(formUser)

        formUser.addEventListener('submit', async function (event) {
          event.preventDefault()

          const formData = new FormData(this)
          const formObject = {}

          formData.forEach((value, key) => {
            formObject[key] = value
          })

          btnSubmit.setLoading(true)

          try {
            const res = await post('/api/user', formObject)

            if (res.success) {
              btnSubmit.setLoading(false)
              clearForm(this)
              pageUser.userModal.hide()
              Swal.fire({
                icon: 'success',
                title: 'Sucesso',
                text: 'Usuário criado com sucesso!'
              })
              pageUser.tableUsers.draw()
            }
          } catch (error) {
            btnSubmit.setLoading(false)
            showErrors(errorContainer, error.data)
          }
        })
      }

      btnCancel.setOnClick(() => {
        pageUser.userModal.hide()
      })
    })
  }

  static async editUserModal (id) {
    try {
      const roles = await get('/api/role/list')
      const res = await get(`/api/user/${id}`)

      const userRoleIds = res.data.roles.map(role => role.id)

      if (res.success) {
        const modal = document.getElementById('userModal')

        if (!pageUser.userEditModal) {
          pageUser.userEditModal = new Modal(modal)
        }

        const modalBody = modal.querySelector('.modal-content')

        modalBody.innerHTML = `
        <div class="block block-rounded">
          <div class="block-header block-header-default">
            <h3 class="block-title fw-bold">
              Editar Usuário
            </h3>
          </div>
          <div class="block-content">
            <div class="row items-push">
              <div class="col-md-12">
                <div class="block block-rounded h-100 mb-0">
                  <div class="block-content fs-md">
                    <form id="form-user-edit" method="POST">
                      <div class="mb-3">
                        <label class="form-label" for="name">Nome:</label>
                        <input type="text" class="form-control" id="name" value="${res.data.name}" name="name">
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="username">Nome de Usuário:</label>
                        <input type="text" class="form-control" id="username" value="${res.data.username}" name="username">
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="email">E-mail:</label>
                        <input type="email" class="form-control" id="email" value="${res.data.email}" name="email">
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="role_id">Perfil:</label>
                        <select class="form-select js-select2" id="role_id" name="role_id" multiple="multiple">
                          ${roles.map(role => {
                            return `<option value="${role.id}" ${userRoleIds.includes(role.id) ? 'selected' : ''}>${role.name}</option>`
                          })}
                        </select>
                      </div>
                    </form>
                    <div id="error-container" class="d-none">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="d-flex gap-2 mb-4">
              <div class="col-12 col-md-6" id="btn-submit-container">
              </div>
              <div class="col-12 col-md-6" id="btn-cancel-container">
              </div>
            </div>
          </div>
        </div>
      `

        const btnSubmit = new Button('Salvar', null, 'btn btn-primary w-100', 'submit', 'form-user-edit')
        const btnCancel = new Button('Cancelar', null, 'btn btn-danger w-100')

        document.getElementById('btn-submit-container').appendChild(btnSubmit.render())
        document.getElementById('btn-cancel-container').appendChild(btnCancel.render())

        pageUser.userEditModal.show()

        const formUserEdit = document.getElementById('form-user-edit')
        const errorContainer = document.getElementById('error-container')

        if (formUserEdit) {
          clearErrors(errorContainer)

          formUserEdit.addEventListener('submit', async function (event) {
            event.preventDefault()

            const formData = new FormData(this)
            const formObject = {}

            formData.forEach((value, key) => {
              formObject[key] = value
            })

            btnSubmit.setLoading(true)

            try {
              const res = await put(`/api/user/${id}`, formObject)

              if (res.success) {
                btnSubmit.setLoading(false)
                pageUser.userEditModal.hide()
                Swal.fire('Sucesso', 'Usuário alterado com sucesso', 'success')
                pageUser.tableUsers.ajax.reload()
              }
            } catch (error) {
              btnSubmit.setLoading(false)
              console.log(error)
              showErrors(errorContainer, error.data)
            }
          })
        }

        btnCancel.setOnClick(() => {
          pageUser.userEditModal.hide()
        })
      }
    } catch (error) {
      console.log(error)
    }
  }

  static showModalPassword (id) {
    const modal = document.getElementById('userModal')

    if (!pageUser.passwordModal) {
      pageUser.passwordModal = new Modal(modal)
    }

    const modalBody = modal.querySelector('.modal-content')

    modalBody.innerHTML = `
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title fw-bold">
            Alterar Senha do Usuário
          </h3>
        </div>
        <div class="block-content">
          <div class="row items-push">
            <div class="col-md-12">
              <div class="block block-rounded h-100 mb-0">
                <div class="block-content fs-md">
                  <form id="form-password" method="POST">
                    <div class="mb-3">
                      <label class="form-label" for="password">Senha:</label>
                      <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                      <label class="form-label" for="password_confirmation">Confirmar Senha:</label>
                      <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                  </form>
                  <div id="error-container" class="d-none">
                    <div class="alert alert-danger" role="alert">
                      <div class="d-flex">
                        <div>
                          <strong>Erro ao cadastrar:</strong>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="d-flex gap-2 mb-4">
            <div class="col-12 col-md-6" id="btn-submit-container">
            </div>
            <div class="col-12 col-md-6" id="btn-cancel-container">
            </div>
          </div>
        </div>
      `

    const btnSubmit = new Button('Salvar', null, 'btn btn-primary w-100', 'submit', 'form-password')
    const btnCancel = new Button('Cancelar', null, 'btn btn-danger w-100')

    document.getElementById('btn-submit-container').appendChild(btnSubmit.render())
    document.getElementById('btn-cancel-container').appendChild(btnCancel.render())

    pageUser.passwordModal.show()

    const formPassword = document.getElementById('form-password')
    const errorContainer = document.getElementById('error-container')

    if (formPassword) {
      clearErrors(errorContainer)
      clearForm(formPassword)

      formPassword.addEventListener('submit', async function (event) {
        event.preventDefault()

        const errorContainer = document.getElementById('error-container')

        const formData = new FormData(this)
        const formObject = {}

        formData.forEach((value, key) => {
          formObject[key] = value
        })

        btnSubmit.setLoading(true)

        try {
          const res = await post(`/api/user/${id}/password`, formObject)

          btnSubmit.setLoading(false)
          clearErrors(errorContainer)
          clearForm(this)
          pageUser.passwordModal.hide()

          Swal.fire({
            icon: 'success',
            title: 'Sucesso',
            text: 'Senha alterada com sucesso!'
          })

          if (pageUser.tableUsers) {
            pageUser.tableUsers.draw()
          }
        } catch (error) {
          btnSubmit.setLoading(false)
          showErrors(errorContainer, error.data)
        }
      })
    }

    btnCancel.setOnClick(() => {
      formPassword.reset()
      pageUser.passwordModal.hide()
    })
  }

  static init () {
    this.initDataTables()
    this.addUserModal()
  }
}

window.Codebase.onLoad(() => pageUser.init())
window.Codebase.helpersOnLoad(['bs-tooltip', 'jq-select2'])
