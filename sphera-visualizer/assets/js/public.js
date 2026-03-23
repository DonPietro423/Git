(async function () {
  if (!window.SPHERA_VIEWERS || !window.SPHERA_VIEWERS.length) {
    return;
  }

  const loadScript = (src) =>
    new Promise((resolve, reject) => {
      const existing = document.querySelector(`script[src="${src}"]`);
      if (existing) {
        resolve();
        return;
      }

      const script = document.createElement('script');
      script.src = src;
      script.onload = resolve;
      script.onerror = reject;
      document.head.appendChild(script);
    });

  try {
    await loadScript('https://unpkg.com/three@0.160.0/build/three.min.js');
    await loadScript('https://unpkg.com/three@0.160.0/examples/js/controls/OrbitControls.js');
    await loadScript('https://unpkg.com/three@0.160.0/examples/js/loaders/GLTFLoader.js');
  } catch (error) {
    console.error('SPHERA: unable to load Three.js assets.', error);
    return;
  }

  const viewers = new Map();

  const createFallbackMaterial = (accentHex) => {
    const color = parseInt((accentHex || '#1F3CFF').replace('#', ''), 16);

    return new THREE.MeshPhysicalMaterial({
      color,
      transmission: 0.78,
      thickness: 0.9,
      roughness: 0.08,
      metalness: 0.04,
      ior: 1.32,
      clearcoat: 1,
      clearcoatRoughness: 0.05,
      iridescence: 1,
      iridescenceIOR: 1.3,
      reflectivity: 1
    });
  };

  window.SPHERA_VIEWERS.forEach((config) => {
    const container = document.getElementById(config.id);
    if (!container) return;

    const scene = new THREE.Scene();

    const camera = new THREE.PerspectiveCamera(
      45,
      container.clientWidth / Math.max(container.clientHeight, 1),
      0.1,
      1000
    );
    camera.position.set(0, 1.15, 4.2);

    const renderer = new THREE.WebGLRenderer({
      antialias: true,
      alpha: true
    });

    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = config.exposure || 1.1;

    container.appendChild(renderer.domElement);

    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.autoRotate = !!config.autoRotate;
    controls.autoRotateSpeed = 1.15;
    controls.enablePan = false;
    controls.minDistance = 1.8;
    controls.maxDistance = 8;
    controls.target.set(0, 0, 0);

    const ambient = new THREE.AmbientLight(0xffffff, 1.7);
    scene.add(ambient);

    const keyLight = new THREE.DirectionalLight(0xffffff, 2.25);
    keyLight.position.set(3, 4, 4);
    scene.add(keyLight);

    const rimLight = new THREE.DirectionalLight(0x8cc8ff, 1.2);
    rimLight.position.set(-3, 2, -4);
    scene.add(rimLight);

    const fillLight = new THREE.DirectionalLight(0xb78fff, 0.8);
    fillLight.position.set(0, -1, 2);
    scene.add(fillLight);

    if (config.showGrid) {
      const grid = new THREE.GridHelper(10, 10, 0x4F7CFF, 0xC7D1E6);
      grid.position.y = -1.25;
      scene.add(grid);
    }

    const fallbackGeometry = new THREE.TorusKnotGeometry(1, 0.28, 240, 48);
    const fallbackMaterial = createFallbackMaterial(config.accent);
    const fallbackMesh = new THREE.Mesh(fallbackGeometry, fallbackMaterial);

    const setFallback = () => {
      if (!scene.children.includes(fallbackMesh)) {
        scene.add(fallbackMesh);
      }
    };

    const finishSetup = (model) => {
      viewers.set(config.id, { camera, controls, renderer, scene, model });

      const animate = () => {
        const viewer = viewers.get(config.id);
        if (!viewer) return;
        requestAnimationFrame(animate);
        viewer.controls.update();
        viewer.renderer.render(viewer.scene, viewer.camera);
      };

      animate();
    };

    if (config.modelUrl) {
      const loader = new THREE.GLTFLoader();

      loader.load(
        config.modelUrl,
        (gltf) => {
          const model = gltf.scene;

          model.traverse((child) => {
            if (child.isMesh && child.material) {
              if (Array.isArray(child.material)) {
                child.material.forEach((material) => {
                  material.envMapIntensity = config.environmentStrength || 1;
                });
              } else {
                child.material.envMapIntensity = config.environmentStrength || 1;
              }
            }
          });

          scene.add(model);
          finishSetup(model);
        },
        undefined,
        () => {
          setFallback();
          finishSetup(fallbackMesh);
        }
      );
    } else {
      setFallback();
      finishSetup(fallbackMesh);
    }

    const onResize = () => {
      const width = container.clientWidth;
      const height = Math.max(container.clientHeight, 1);

      camera.aspect = width / height;
      camera.updateProjectionMatrix();
      renderer.setSize(width, height);
    };

    window.addEventListener('resize', onResize);
  });

  document.addEventListener('click', (event) => {
    const button = event.target.closest('.sphera-reset-btn');
    if (!button) return;

    const target = button.getAttribute('data-target');
    const viewer = viewers.get(target);
    if (!viewer) return;

    viewer.camera.position.set(0, 1.15, 4.2);
    viewer.controls.target.set(0, 0, 0);
    viewer.controls.update();
  });
})();