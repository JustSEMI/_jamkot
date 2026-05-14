import './wireflow/alpineflow.css';
import './wireflow/alpineflow-theme.css';
import Alpine from 'alpinejs';
import AlpineFlow from './wireflow/alpineflow.bundle.esm.js';

window.Alpine = Alpine;
Alpine.plugin(AlpineFlow);
Alpine.start();
