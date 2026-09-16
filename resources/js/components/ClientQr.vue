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
      duracionTotalMs: 60000, // se recalcula con el valor real que devuelva el backend
      segundosRestantes: 100,
      sellosActuales: 0,
      sellosRequeridos: 7,
      corteGratis: false,
      refreshTimer: null,
      tickTimer: null,
    }
  },
  mounted() {
    this.refrescarToken()
    this.tickTimer = setInterval(this.actualizarBarra, 1000)
  },
  beforeUnmount() {
    clearTimeout(this.refreshTimer)
    clearInterval(this.tickTimer)
  },
  methods: {
    async refrescarToken() {
      try {
        const res = await fetch('/api/loyalty/qr-token')
        const data = await res.json()
        if (!data.ok) return

        const ahora = Date.now()
        this.token = data.token
        this.expiresAt = new Date(data.expires_at)
        // Duración real configurada por la barbería (loyalty.config.qr_token_segundos),
        // no un valor fijo — así la barra y el refresco calzan sin importar el valor.
        this.duracionTotalMs = Math.max(1000, this.expiresAt.getTime() - ahora)
        this.$nextTick(() => this.pintarQr())

        // Se programa el siguiente refresco un poco antes de que expire (80% del
        // tiempo total), en vez de un intervalo fijo que podía desincronizarse
        // de la duración real y dejar la barra "pegada" sin bajar de la mitad.
        clearTimeout(this.refreshTimer)
        this.refreshTimer = setTimeout(this.refrescarToken, this.duracionTotalMs * 0.8)
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
      this.segundosRestantes = Math.max(0, Math.round((totalMs / this.duracionTotalMs) * 100))
    },
    // Llamado externamente (ej. via WebSocket/polling de estado de tarjeta)
    // cuando el backend confirma que se completó la tarjeta.
    marcarCorteGratis(sellos, requeridos) {
      this.sellosActuales = sellos
      this.sellosRequeridos = requeridos
      this.corteGratis = true
      clearTimeout(this.refreshTimer)
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
