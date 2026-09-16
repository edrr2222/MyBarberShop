<template>
  <div class="scanner-wrap">
    <div v-if="resultado && resultado.corte_gratis" class="resultado corte-gratis">
      🎉 ¡CORTE GRATIS!
      <button @click="redimir">Marcar como aplicado</button>
    </div>

    <div v-else-if="resultado" class="resultado" :class="{ error: !resultado.ok }">
      {{ resultado.mensaje }}
      <template v-if="resultado.ok">
        <br />{{ resultado.sellos_actuales }} / {{ resultado.sellos_requeridos }} sellos
      </template>
    </div>

    <div id="qr-reader" v-show="!resultado"></div>

    <button v-if="resultado" @click="escanearOtro">Escanear siguiente</button>
  </div>
</template>

<script>
// Requiere: npm install html5-qrcode
import { Html5Qrcode } from 'html5-qrcode'

export default {
  name: 'EmpleadoScanner',
  props: {
    servicioId: { type: Number, default: null },
  },
  data() {
    return {
      scanner: null,
      resultado: null,
      procesando: false,
    }
  },
  mounted() {
    this.iniciarCamara()
  },
  beforeUnmount() {
    this.detenerCamara()
  },
  methods: {
    iniciarCamara() {
      this.scanner = new Html5Qrcode('qr-reader')
      this.scanner
        .start(
          { facingMode: 'environment' },
          { fps: 10, qrbox: 250 },
          (decodedText) => this.onScan(decodedText)
        )
        .catch((err) => console.error('No se pudo iniciar la cámara', err))
    },
    detenerCamara() {
      if (this.scanner) {
        this.scanner.stop().catch(() => {})
      }
    },
    async onScan(token) {
      if (this.procesando) return
      this.procesando = true
      this.detenerCamara()

      try {
        const res = await fetch('/api/loyalty/escanear', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          },
          body: JSON.stringify({ token, servicio_id: this.servicioId }),
        })
        this.resultado = await res.json()
      } catch (e) {
        this.resultado = { ok: false, mensaje: 'Error de conexión, intenta de nuevo' }
      } finally {
        this.procesando = false
      }
    },
    async redimir() {
      await fetch('/api/loyalty/redimir', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ card_id: this.resultado.card_id }),
      })
      this.escanearOtro()
    },
    escanearOtro() {
      this.resultado = null
      this.$nextTick(() => this.iniciarCamara())
    },
  },
}
</script>

<style scoped>
.scanner-wrap { display: flex; flex-direction: column; align-items: center; gap: 16px; }
#qr-reader { width: 300px; }
.resultado { font-size: 1.2rem; font-weight: 600; text-align: center; padding: 20px; }
.resultado.error { color: #b00020; }
.corte-gratis { font-size: 1.8rem; font-weight: 800; color: var(--color-primario, #111); }
</style>
