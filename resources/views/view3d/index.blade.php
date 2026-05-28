@extends('layouts.app')

@section('title', '3D Model View')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/view3d.css') }}">
@endpush

@section('content')
    <header class="content-header-flex">
        <div>
            <h1>3D MODEL VIEW</h1>
            <p>Lihat dan analisis objek 3D secara interaktif dari berkas ekspor Tinkercad.</p>
        </div>

        <!-- JAM -->
        <div class="datetime-widget">
            <div id="realtime-clock" class="time-display">00:00:00</div>
            <div id="realtime-date" class="date-display">Memuat...</div>
        </div>
    </header>

    <!-- 3D VIEWER WORKSPACE -->
    <div class="view3d-container">
        
        <!-- CANVAS AREA -->
        <div class="canvas-wrapper">
            <!-- LOADING OVERLAY -->
            <div class="loader-overlay" id="loader-overlay">
                <div class="spinner-modern"></div>
                <div class="progress-text" id="progress-text">Memuat Model: 0%</div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" id="progress-bar-fill"></div>
                </div>
            </div>
            
            <canvas id="canvas3d"></canvas>
        </div>

        <!-- CONTROL PANEL SIDEBAR -->
        <div class="control-sidebar">
            
            <!-- UTILITY CONTROLS -->
            <div class="panel-card">
                <h3 class="panel-card-title">Kontrol Viewport</h3>
                <div class="control-grid">
                    <button class="btn-control" id="btn-reset">
                        Reset Kamera <i class="fa-solid fa-camera"></i>
                    </button>
                    <button class="btn-control" id="btn-rotate">
                        Rotasi Otomatis <i class="fa-solid fa-arrows-spin"></i>
                    </button>
                    <button class="btn-control" id="btn-wireframe">
                        Mode Wireframe <i class="fa-solid fa-border-none"></i>
                    </button>
                </div>

                <!-- SLIDER PENCALAHAN -->
                <div class="slider-group">
                    <div class="slider-label">
                        <span>Intensitas Cahaya</span>
                        <span id="light-val">1.2x</span>
                    </div>
                    <input type="range" id="slider-light" class="slider-input" min="0.2" max="3" step="0.1" value="1.2">
                </div>
            </div>

            <!-- INTERACTION HELP -->
            <div class="panel-card">
                <!-- Desktop Header -->
                <h3 class="panel-card-title desktop-only">Navigasi Mouse</h3>
                <div class="instruction-list desktop-only">
                    <div class="instruction-item">
                        <i class="fa-solid fa-mouse-pointer"></i>
                        <span><strong>Klik & Seret (Kiri):</strong> Putar/Rotasi objek 3D secara bebas.</span>
                    </div>
                    <div class="instruction-item">
                        <i class="fa-solid fa-computer-mouse"></i>
                        <span><strong>Scroll Wheel:</strong> Perbesar (Zoom In) atau perkecil (Zoom Out).</span>
                    </div>
                    <div class="instruction-item">
                        <i class="fa-solid fa-hand"></i>
                        <span><strong>Klik & Seret (Kanan):</strong> Geser kamera (Panning) ke segala arah.</span>
                    </div>
                </div>

                <!-- Mobile Header -->
                <h3 class="panel-card-title mobile-only">Navigasi Sentuh</h3>
                <div class="instruction-list mobile-only">
                    <div class="instruction-item">
                        <i class="fa-solid fa-fingerprint"></i>
                        <span><strong>Satu Jari:</strong> Putar/Rotasi objek 3D secara bebas.</span>
                    </div>
                    <div class="instruction-item">
                        <i class="fa-solid fa-up-down-left-right"></i>
                        <span><strong>Cubit (Pinch):</strong> Perbesar (Zoom In) atau perkecil (Zoom Out).</span>
                    </div>
                    <div class="instruction-item">
                        <i class="fa-solid fa-up-right-from-square"></i>
                        <span><strong>Dua Jari:</strong> Geser kamera (Panning) ke segala arah.</span>
                    </div>
                </div>
            </div>

            <!-- OBJECT INFORMATION -->
            <div class="panel-card">
                <h3 class="panel-card-title">Informasi Model</h3>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Nama Berkas</td>
                        <td class="info-value">tinker.obj</td>
                    </tr>
                    <tr>
                        <td class="info-label">Material (.mtl)</td>
                        <td class="info-value">obj.mtl</td>
                    </tr>
                    <tr>
                        <td class="info-label">Format File</td>
                        <td class="info-value">Wavefront OBJ</td>
                    </tr>
                    <tr>
                        <td class="info-label">Renderer Engine</td>
                        <td class="info-value">Three.js WebGL</td>
                    </tr>
                </table>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <!-- LOAD THREE.JS FROM CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/MTLLoader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/OBJLoader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

    <!-- IN-PAGE CORE CLOCK SCRIPT -->
    <script src="{{ asset('js/utils/clock.js') }}"></script>

    <!-- THREE.JS ENGINE LOGIC -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('canvas3d');
            const wrapper = canvas.parentElement;
            
            // Elements
            const loaderOverlay = document.getElementById('loader-overlay');
            const progressText = document.getElementById('progress-text');
            const progressBarFill = document.getElementById('progress-bar-fill');
            const btnReset = document.getElementById('btn-reset');
            const btnRotate = document.getElementById('btn-rotate');
            const btnWireframe = document.getElementById('btn-wireframe');
            const sliderLight = document.getElementById('slider-light');
            const lightValText = document.getElementById('light-val');

            // Constants
            const mtlPath = "{{ asset('3d/obj.mtl') }}";
            const objPath = "{{ asset('3d/tinker.obj') }}";

            // State
            let scene, camera, renderer, controls;
            let loadedObject = null;
            let autoRotate = false;
            let wireframeMode = false;
            let ambientLight, dirLight, dirLight2;
            let defaultCameraPosition = { x: 0, y: 0, z: 10 };
            let defaultCameraTarget = { x: 0, y: 0, z: 0 };

            // Initialize Three.js Scene
            function init() {
                scene = new THREE.Scene();
                scene.background = null;

                // Camera Setup
                camera = new THREE.PerspectiveCamera(
                    45,
                    wrapper.clientWidth / wrapper.clientHeight,
                    0.1,
                    1000
                );
                camera.position.set(defaultCameraPosition.x, defaultCameraPosition.y, defaultCameraPosition.z);

                // Renderer Setup
                renderer = new THREE.WebGLRenderer({
                    canvas: canvas,
                    antialias: true,
                    alpha: true,
                    powerPreference: "high-performance"
                });
                renderer.setSize(wrapper.clientWidth, wrapper.clientHeight);
                renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
                renderer.shadowMap.enabled = true;
                renderer.outputEncoding = THREE.sRGBEncoding;

                // Lighting
                ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
                scene.add(ambientLight);

                dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
                dirLight.position.set(10, 20, 15);
                dirLight.castShadow = true;
                scene.add(dirLight);

                dirLight2 = new THREE.DirectionalLight(0x7c94b4, 0.4);
                dirLight2.position.set(-10, -10, -10);
                scene.add(dirLight2);

                // Controls
                controls = new THREE.OrbitControls(camera, renderer.domElement);
                controls.enableDamping = true;
                controls.dampingFactor = 0.05;
                controls.screenSpacePanning = true;
                controls.minDistance = 1;
                controls.maxDistance = 500;

                // Event Listeners
                window.addEventListener('resize', onWindowResize);
                
                setupControlHandlers();
                loadModel();
                animate();
            }

            // Window Resizing
            function onWindowResize() {
                const width = wrapper.clientWidth;
                const height = wrapper.clientHeight;
                
                camera.aspect = width / height;
                camera.updateProjectionMatrix();
                
                renderer.setSize(width, height);
            }

            // Load OBJ and MTL
            function loadModel() {
                const mtlLoader = new THREE.MTLLoader();
                
                        const mtlBaseUrl = mtlPath.substring(0, mtlPath.lastIndexOf('/') + 1);
                const mtlFileName = mtlPath.substring(mtlPath.lastIndexOf('/') + 1);
                
                const objBaseUrl = objPath.substring(0, objPath.lastIndexOf('/') + 1);
                const objFileName = objPath.substring(objPath.lastIndexOf('/') + 1);

                progressText.innerText = "Mengunduh Material (MTL)...";

                mtlLoader.setPath(mtlBaseUrl);
                mtlLoader.load(mtlFileName, function (materials) {
                    materials.preload();
                    
                    const objLoader = new THREE.OBJLoader();
                    objLoader.setMaterials(materials);
                    objLoader.setPath(objBaseUrl);
                    
                    objLoader.load(objFileName, 
                        // On Success
                        function (object) {
                            loadedObject = object;
                            scene.add(object);

                                            const box = new THREE.Box3().setFromObject(object);
                            const size = box.getSize(new THREE.Vector3());
                            const center = box.getCenter(new THREE.Vector3());

                                            object.position.x = -center.x;
                            object.position.y = -center.y;
                            object.position.z = -center.z;

                                            const maxDim = Math.max(size.x, size.y, size.z);
                            const fov = camera.fov * (Math.PI / 180);
                            let cameraZ = Math.abs(maxDim / 2 / Math.tan(fov / 2));
                            
                            cameraZ *= 1.4;
                            
                                            defaultCameraPosition = { x: 0, y: maxDim * 0.1, z: cameraZ };
                            camera.position.set(defaultCameraPosition.x, defaultCameraPosition.y, defaultCameraPosition.z);
                            
                            controls.target.set(0, 0, 0);
                            controls.maxDistance = cameraZ * 5;
                            controls.update();

                                            setTimeout(() => {
                                loaderOverlay.classList.add('fade-out');
                            }, 500);
                        },
                        
                        // On Progress
                        function (xhr) {
                            if (xhr.lengthComputable) {
                                const percentComplete = Math.round((xhr.loaded / xhr.total) * 100);
                                progressText.innerText = `Memuat Berkas 3D: ${percentComplete}%`;
                                progressBarFill.style.width = `${percentComplete}%`;
                            } else {
                                progressText.innerText = "Mengunduh file model 3D...";
                            }
                        },
                        
                        // On Error
                        function (error) {
                            console.error('Error loading 3D Object:', error);
                            progressText.innerText = "Gagal memuat model 3D. Periksa berkas.";
                            progressText.style.color = "#ef4444";
                        }
                    );
                }, undefined, function (error) {
                    console.error('Error loading MTL materials:', error);
                    progressText.innerText = "Gagal memuat material objek.";
                    progressText.style.color = "#ef4444";
                });
            }

            // Setup Control Handlers
            function setupControlHandlers() {
                // Reset Camera
                btnReset.addEventListener('click', () => {
                    camera.position.set(defaultCameraPosition.x, defaultCameraPosition.y, defaultCameraPosition.z);
                    controls.target.set(0, 0, 0);
                    controls.update();
                    
                                btnReset.classList.add('active');
                    setTimeout(() => btnReset.classList.remove('active'), 200);
                });

                // Auto Rotate
                btnRotate.addEventListener('click', () => {
                    autoRotate = !autoRotate;
                    if (autoRotate) {
                        btnRotate.classList.add('active');
                    } else {
                        btnRotate.classList.remove('active');
                    }
                });

                // Wireframe Mode
                btnWireframe.addEventListener('click', () => {
                    wireframeMode = !wireframeMode;
                    if (wireframeMode) {
                        btnWireframe.classList.add('active');
                    } else {
                        btnWireframe.classList.remove('active');
                    }

                    if (loadedObject) {
                        loadedObject.traverse(function (child) {
                            if (child.isMesh) {
                                if (Array.isArray(child.material)) {
                                    child.material.forEach(mat => mat.wireframe = wireframeMode);
                                } else if (child.material) {
                                    child.material.wireframe = wireframeMode;
                                }
                            }
                        });
                    }
                });

                // Light Intensity Slider
                sliderLight.addEventListener('input', (e) => {
                    const value = parseFloat(e.target.value);
                    lightValText.innerText = `${value.toFixed(1)}x`;
                    dirLight.intensity = value * 0.65;
                    ambientLight.intensity = value * 0.5;
                });
            }

            // Animation Loop
            function animate() {
                requestAnimationFrame(animate);

                    if (autoRotate && loadedObject) {
                    loadedObject.rotation.y += 0.005;
                }

                controls.update();
                renderer.render(scene, camera);
            }

            // Run Init
            init();
        });
    </script>
@endpush
