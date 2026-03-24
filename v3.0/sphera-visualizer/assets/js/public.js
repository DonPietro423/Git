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

  const frameModel = (model, camera, controls) => {
    const box = new THREE.Box3().setFromObject(model);

    if (box.isEmpty()) {
      camera.position.set(0, 1.15, 4.2);
      controls.target.set(0, 0, 0);
      controls.update();

      return {
        position: new THREE.Vector3(0, 1.15, 4.2),
        target: new THREE.Vector3(0, 0, 0)
      };
    }

    const center = box.getCenter(new THREE.Vector3());
    const size = box.getSize(new THREE.Vector3());

    model.position.sub(center);

    const maxDim = Math.max(size.x, size.y, size.z) || 1;
    const fov = camera.fov * (Math.PI / 180);
    let cameraZ = Math.abs((maxDim / 2) / Math.tan(fov / 2));

    cameraZ *= 1.8;

    const cameraY = Math.max(maxDim * 0.2, 0.5);

    camera.position.set(0, cameraY, cameraZ);
    camera.near = Math.max(0.01, maxDim / 100);
    camera.far = Math.max(1000, maxDim * 20);
    camera.updateProjectionMatrix();

    controls.target.set(0, 0, 0);
    controls.minDistance = Math.max(maxDim * 0.5, 0.5);
    controls.maxDistance = Math.max(maxDim * 10, 10);
    controls.update();

    return {
      position: camera.position.clone(),
      target: controls.target.clone()
    };
  };

  window.SPHERA_VIEWERS.forEach((config) => {
    const container = document.getElementById(config.id);
    if (!container) return;
    if (container.clientHeight < 200) {
  container.style.minHeight = '420px';
}

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

    const finishSetup = (model, defaultView) => {
      viewers.set(config.id, {
        camera,
        controls,
        renderer,
        scene,
        model,
        defaultView
      });

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

          const defaultView = frameModel(model, camera, controls);
          finishSetup(model, defaultView);
        },
        undefined,
        (error) => {
          console.error('SPHERA: error loading model.', error);
          setFallback();

          const defaultView = frameModel(fallbackMesh, camera, controls);
          finishSetup(fallbackMesh, defaultView);
        }
      );
    } else {
      setFallback();

      const defaultView = frameModel(fallbackMesh, camera, controls);
      finishSetup(fallbackMesh, defaultView);
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

    if (viewer.defaultView) {
      viewer.camera.position.copy(viewer.defaultView.position);
      viewer.controls.target.copy(viewer.defaultView.target);
    } else {
      viewer.camera.position.set(0, 1.15, 4.2);
      viewer.controls.target.set(0, 0, 0);
    }

    viewer.controls.update();
  });
})();