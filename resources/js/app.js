import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import 'bootstrap-icons/font/bootstrap-icons.css'

window.Alpine = Alpine;

Alpine.plugin(focus);

Alpine.start();
