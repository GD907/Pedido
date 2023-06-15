import './bootstrap';
import '../../vendor/alperenersoy/filament-export/resources/js/filament-export.js';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
window.Alpine = Alpine;

Alpine.plugin(focus);

Alpine.start();
