import { get } from '../codebase/api'

class pageDashboard {
  static initCharts () {
    const ctx = document.getElementById('chartContainer')

    get('/dashboard/chart')
      .then(data => {
        const options = {
          animationEnabled: true,
          theme: 'light2',
          title: {
            text: 'Pedidos no mês atual'
          },
          axisY: {
            title: 'Quantidade de Pedidos'
          },
          data: [{
            type: 'column',
            showInLegend: true,
            legendText: 'Dias do mês',
            dataPoints: data.map(item => ({
              label: item.order_date_formatted,
              y: item.total_orders
            }))
          }]
        }

        // Renderizar o gráfico usando CanvasJS
        const chart = new CanvasJS.Chart(ctx, options)
        chart.render()
      })
      .catch(error => {
        console.error('Erro ao obter dados do endpoint:', error)
      })
  }

  static init () {
    this.initCharts()
  }
}

// Initialize when page loads
window.Codebase.onLoad(() => pageDashboard.init())
