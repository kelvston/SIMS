import './bootstrap';

import Alpine from 'alpinejs';
import '@fortawesome/fontawesome-free/css/all.min.css';
import Chart from 'chart.js/auto';
import jQuery from 'jquery';
import Swal from 'sweetalert2';
import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';
window.$ = window.jQuery = jQuery;
window.Swal = Swal;
window.Html5Qrcode = Html5Qrcode;
window.Html5QrcodeSupportedFormats = Html5QrcodeSupportedFormats;

// Import DataTables
import 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-responsive-bs5';

// Import styles
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css';

window.Alpine = Alpine;

Alpine.start();
