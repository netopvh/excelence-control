import { get } from '../codebase/api'
import LoadingSpinner from '../codebase/components/loading'

class pageDashboard {
  static async initCards () {
    const containerCardLate = document.getElementById('container-card-late')
    const containerCardProductLate = document.getElementById('container-card-product-late')
    const containerApproved = document.getElementById('container-approved')
    const containerWaiting = document.getElementById('container-waiting')
    const containerCancelled = document.getElementById('container-design')
    const containerToBuy = document.getElementById('container-to-buy')
    const containerLate = document.getElementById('container-late')
    const containerLateProducts = document.getElementById('container-late-products')
    const loading = new LoadingSpinner(24, '#000000')

    containerApproved.appendChild(loading.render())
    containerWaiting.appendChild(loading.render())
    containerCancelled.appendChild(loading.render())
    containerToBuy.appendChild(loading.render())
    containerLate.appendChild(loading.render())
    containerLateProducts.appendChild(loading.render())

    try {
      const res = await get('/api/dashboard')

      if (res.success) {
        containerApproved.innerHTML = res.data.approved
        containerWaiting.innerHTML = res.data.waitingApproval
        containerCancelled.innerHTML = res.data.waitingArt
        containerToBuy.innerHTML = res.data.itemsToBuy
        containerLate.innerHTML = res.data.lateOrders
        containerLateProducts.innerHTML = res.data.lateProducts

        this.blinkCard(containerCardLate, res.data.lateOrders)
        this.blinkCard(containerCardProductLate, res.data.lateProducts)
      }
    } catch (error) {
      console.error('Erro ao obter dados do endpoint:', error)
    }
  }

  static blinkCard (element, counter) {
    if (counter > 0) {
      let isWarning = false
      setInterval(() => {
        if (isWarning) {
          element.classList.remove('bg-warning')
        } else {
          element.classList.add('bg-warning')
        }
        isWarning = !isWarning
      }, 400)
    } else {
      element.classList.remove('bg-warning')
    }
  }

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
    this.initCards()
  }
}

// Initialize when page loads
window.Codebase.onLoad(() => pageDashboard.init())
