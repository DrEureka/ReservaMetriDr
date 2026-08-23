import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Swal from 'sweetalert2';

Alpine.plugin(collapse);

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start();
