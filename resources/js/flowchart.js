import '../../vendor/getartisanflow/wireflow/dist/alpineflow.css';
import '../../vendor/getartisanflow/wireflow/dist/alpineflow-theme.css';
import Alpine from 'alpinejs';
import AlpineFlow from '../../vendor/getartisanflow/wireflow/dist/alpineflow.bundle.esm.js';

window.Alpine = Alpine;
Alpine.plugin(AlpineFlow);
Alpine.start();
