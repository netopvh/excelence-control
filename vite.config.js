import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/sass/main.scss',
        'resources/sass/codebase/themes/corporate.scss',
        'resources/sass/codebase/themes/earth.scss',
        'resources/sass/codebase/themes/elegance.scss',
        'resources/sass/codebase/themes/flat.scss',
        'resources/sass/codebase/themes/pulse.scss',
        'resources/js/codebase/app.js',
        'resources/js/pages/dashboard.js',
        'resources/js/pages/order/index.js',
        'resources/js/pages/order/create.js',
        'resources/js/pages/order/show.js',
        'resources/js/pages/order/kanban.js',
        'resources/js/pages/purchase/index.js',
        'resources/js/pages/user/index.js',
        'resources/js/pages/import/index.js',
        'resources/js/pages/production/index.js'
      ],
      refresh: true
    })
  ]
})
