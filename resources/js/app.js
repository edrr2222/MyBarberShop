import './bootstrap';
import { createApp } from 'vue';
import ClientQr from './components/ClientQr.vue';
import EmpleadoScanner from './components/EmpleadoScanner.vue';

const clientQrEl = document.getElementById('client-qr-app');
if (clientQrEl) {
    createApp(ClientQr).mount(clientQrEl);
}

const scannerEl = document.getElementById('empleado-scanner-app');
if (scannerEl) {
    const servicioId = scannerEl.dataset.servicioId ? Number(scannerEl.dataset.servicioId) : null;
    createApp(EmpleadoScanner, { servicioId }).mount(scannerEl);
}
