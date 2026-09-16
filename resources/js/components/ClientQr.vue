<template>
  <div class="qr-wrap">
    <div v-if="corteGratis" class="corte-gratis">
      🎉 ¡CORTE GRATIS DISPONIBLE!
    </div>

    <template v-else>
      <canvas ref="canvas"></canvas>
      <p class="contador">{{ sellosActuales }} / {{ sellosRequeridos }} sellos</p>
      <div class="progreso">
        <div class="barra" :style="{ width: segundosRestantes + '%' }"></div>
      </div>
    </template>
  </div>
</template>

<script>
// Requiere: npm install qrcode
import QRCode from 'qrcode'

export default {
  name: 'ClientQr',
  data() {
    return {
      token: null,
      expiresAt: null,
      segundosRestantes: 100,
      sellosActuales: 0,
      sellosRequeridos: 7,
      corteGratis: false,
      pollTimer: null,
      tickTimer: null,
    }
  },
  mounted() {
    this.refrescarToken()
    // Regenera el QR periódicamente antes de que expire (ver loyalty.config.qr_token_segundos)
    this.pollTimer = setInterval(this.refrescarToken, 30000)
    this.tickTimer = setInterval(this.actualizarBarra, 1000)
  },
  beforeUnmount() {
    clearInterval(this.pollTimer)
    clearInterval(this.tickTimer)
  },
  methods: {
    async refrescarToken() {
      try {
        const res = await fetch('/api/loyalty/qr-token')
        const data = await res.json()
        if (!data.ok) return

        this.token = data.token
        this.expiresAt = new Date(data.expires_at)
        this.$nextTick(() => this.pintarQr())
      } catch (e) {
        console.error('No se pudo generar el QR', e)
      }
    },
    pintarQr() {
      if (this.$refs.canvas) {
        QRCode.toCanvas(this.$refs.canvas, this.token, { width: 260 })
      }
    },
    actualizarBarra() {
      if (!this.expiresAt) return
      const totalMs = this.expiresAt - Date.now()
      const totalDuracion = 60000 // ajustar si loyalty.config cambia el default
      this.segundosRestantes = Math.max(0, Math.round((totalMs / totalDuracion) * 100))
    },
    // Llamado externamente (ej. via WebSocket/polling de estado de tarjeta)
    // cuando el backend confirma que se completó la tarjeta.
    marcarCorteGratis(sellos, requeridos) {
      this.sellosActuales = sellos
      this.sellosRequeridos = requeridos
      this.corteGratis = true
      clearInterval(this.pollTimer)
    },
  },
}
</script>

<style scoped>
.qr-wrap { display: flex; flex-direction: column; align-items: center; gap: 12px; }
.contador { font-size: 1.1rem; font-weight: 600; }
.progreso { width: 260px; height: 6px; background: var(--color-secundario, #eee); border-radius: 4px; overflow: hidden; }
.barra { height: 100%; background: var(--color-primario, #111); transition: width 1s linear; }
.corte-gratis {
  font-size: 1.8rem; font-weight: 800; text-align: center;
  color: var(--color-primario, #111); padding: 40px 20px;
}
</style>
