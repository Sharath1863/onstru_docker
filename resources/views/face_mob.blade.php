<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Face Recognition</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <script defer src="{{ asset('js/face-api.min.js') }}"></script> --}}
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        crossorigin="anonymous">

    <style>
        body {
            overflow: hidden;
            transition: background-color 0.3s ease;
        }

        body.attendance-checkin {
            background-color: #4CAF50 !important;
        }

        body.attendance-checkout {
            background-color: #f44336 !important;
        }

        #video,
        #overlay {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            height: 50%;
            object-fit: cover;
        }

        #overlay {
            z-index: 10;
            pointer-events: none;
        }

        #emp {
            position: relative;
            margin-top: 400px;
            color: #333;
            font-size: 1rem;
        }

        .not-recognized {
            color: red !important;
            font-weight: bold;
        }

        .recognized {
            color: black !important;
            font-weight: bold;
        }
    </style>
</head>

<body class="">

    <div class="container-fluid h-100 w-50">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-12 text-center">
                <canvas id="overlay"></canvas>
                <div id="loadingOverlay">
                    <div class="spinner"></div>
                    <p>Loading face recognition models...</p>
                </div>

                <video id="video" class="video-container" autoplay muted></video>

                <div id="matchStatuss" style="color: green; font-size: 16px; margin-top: 10px;"></div>
                <button id="scanBtn" style="display:none;">🔍 Scan Face</button>

            </div>
        </div>


        <p id="emp" class="text-center"></p>

    </div>
    <h1 class="text-center mt-0" id="matchStatus"></h1>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

    <script defer>
        const video = document.getElementById('video');
        const statusEl = document.getElementById('matchStatus');
        const scanBtn = document.getElementById('scanBtn');

        let groups = [];
        let lastX = null;
        let blinked = false;
        let headMoved = false;
        let lastLeft = 0;
        let modelsLoaded = false; // track model load
        let livePassed = false;

        // --- START VIDEO ---
        async function startVideo() {
            let stream;
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: {
                            exact: "environment"
                        }
                    }
                });
            } catch {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
            }
            video.srcObject = stream;
            console.log("📷 Camera started");
        }

        // --- LOAD MODELS ---
        // async function loadModels() {
        //     const url = 'https://web1.nulinz.in/models';
        //     statusEl.innerText = "Loading face models...";
        //     console.log("⏳ Loading Face API models...");
        //     await Promise.all([
        //         faceapi.nets.tinyFaceDetector.loadFromUri(url),
        //         faceapi.nets.faceLandmark68Net.loadFromUri(url),
        //         faceapi.nets.faceRecognitionNet.loadFromUri(url),
        //         faceapi.nets.faceExpressionNet.loadFromUri(url)
        //     ]);
        //     modelsLoaded = true;
        //     console.log("✅ All Face API models loaded!");
        // }

        // async function loadOrCacheModel(modelName, url) {
        //     try {
        //         // 1️⃣ Try to load from IndexedDB
        //         await faceapi.nets[modelName].loadFromIndexedDB('face-model-cache');
        //         console.log(`✅ Loaded ${modelName} from IndexedDB`);
        //     } catch (e) {
        //         console.warn(`ℹ️ ${modelName} not in cache, loading from network...`);
        //         // 2️⃣ Otherwise load from URI
        //         await faceapi.nets[modelName].loadFromUri(url);
        //         // 3️⃣ Save it for next time
        //         await faceapi.nets[modelName].save('face-model-cache');
        //         console.log(`💾 Cached ${modelName} in IndexedDB`);
        //     }
        // }

        // --- CACHE MODEL FILES (works across reloads) ---
        // async function cacheModelFiles(url) {
        //     const cacheName = 'face-model-cache-v1';
        //     const modelFiles = [
        //         'tiny_face_detector_model-shard1',
        //         'tiny_face_detector_model-weights_manifest.json',
        //         'face_landmark_68_model-shard1',
        //         'face_landmark_68_model-weights_manifest.json',
        //         'face_recognition_model-shard1',
        //         'face_recognition_model-weights_manifest.json',
        //         'face_expression_model-shard1',
        //         'face_expression_model-weights_manifest.json',
        //     ];

        //     const cache = await caches.open(cacheName);
        //     for (const file of modelFiles) {
        //         const fileUrl = `${url}/${file}`;
        //         const match = await cache.match(fileUrl);
        //         if (!match) {
        //             console.log(`💾 Caching model file: ${file}`);
        //             await cache.add(fileUrl);
        //         }
        //     }
        //     console.log('✅ Model files cached for offline use');
        // }


        async function loadModels() {
            const url = 'https://web1.nulinz.in/models';
            statusEl.innerText = "Loading face models...";
            console.log("⏳ Loading Face API models (with cache)...");

            // Cache files (first time only)
            // await cacheModelFiles(url);

            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(url),
                faceapi.nets.faceLandmark68Net.loadFromUri(url),
                faceapi.nets.faceRecognitionNet.loadFromUri(url),
                faceapi.nets.faceExpressionNet.loadFromUri(url)
            ]);

            modelsLoaded = true;
            console.log("✅ All Face API models ready (cache supported)!");
        }

        // --- LOAD EMBEDDINGS ---
        async function loadEmbeddings() {
            statusEl.innerText = "Loading known faces...";
            const res = await fetch('/faces_json');
            const faces = await res.json();

            const chunkSize = Math.ceil(faces.length / 3);
            groups = [
                faces.slice(0, chunkSize),
                faces.slice(chunkSize, 2 * chunkSize),
                faces.slice(2 * chunkSize)
            ];
            console.log(`✅ Loaded ${faces.length} known faces`);
        }

        // --- LIVENESS CHECK ---
        function checkLiveness(landmarks) {
            const leftEye = landmarks.getLeftEye();
            const nose = landmarks.getNose();
            const ear = getEAR(leftEye);

            if (lastLeft && ear < 0.22 && lastLeft - ear > 0.04) blinked = true;
            lastLeft = ear;

            const x = nose[3].x;
            if (lastX && Math.abs(lastX - x) > 5) headMoved = true;
            lastX = x;

            // return blinked || headMoved;

            if (blinked || headMoved) {
                livePassed = true;
                statusEl.innerText = "✅ Liveness check passed — ready to scan!";
                statusEl.style.display = "block";
                scanBtn.style.display = "inline-block";
            }
        }

        function getEAR(eye) {
            const dist = (p1, p2) => Math.hypot(p1.x - p2.x, p1.y - p2.y);
            const A = dist(eye[1], eye[5]);
            const B = dist(eye[2], eye[4]);
            const C = dist(eye[0], eye[3]);
            return (A + B) / (2.0 * C);
        }

        // --- FACE COMPARISON ---
        async function compareFaceWithGroup(descriptor, group) {
            let best = {
                name: 'Unknown',
                distance: 1.0
            };

            for (const person of group) {
                let raw = person.embedding;
                if (typeof raw === 'string') {
                    try {
                        raw = JSON.parse(raw);
                    } catch {
                        continue;
                    }
                }
                if (Array.isArray(raw)) raw = raw.map(v => parseFloat(v));
                else if (raw && typeof raw.embedding !== 'undefined')
                    raw = raw.embedding.map(v => parseFloat(v));

                const emb = new Float32Array(raw);
                if (emb.length !== 128) continue;

                const distance = faceapi.euclideanDistance(descriptor, emb);
                if (distance < best.distance) best = {
                    name: person.name,
                    distance
                };
            }
            return best;
        }

        // ---- Watch face until liveness passes ----
        async function monitorFace() {
            if (!modelsLoaded) return;

            const detections = await faceapi.detectAllFaces(
                video, new faceapi.TinyFaceDetectorOptions()
            ).withFaceLandmarks();

            if (detections.length === 0) {
                statusEl.innerText = "No face detected. Please come into frame.";
                scanBtn.style.display = "none";
                livePassed = false;
                return;
            }

            const {
                landmarks
            } = detections[0];
            if (!livePassed) {
                statusEl.innerText = "Please blink or move your head...";
                checkLiveness(landmarks);
            }
        }

        // --- FACE RECOGNITION ---
        async function recognizeFace() {
            statusEl.innerText = "Scanning face...";
            const detections = await faceapi.detectAllFaces(
                video, new faceapi.TinyFaceDetectorOptions()
            ).withFaceLandmarks().withFaceDescriptors();

            if (detections.length === 0) {
                statusEl.innerText = "No face detected";
                return;
            }

            const {
                descriptor
            } = detections[0];

            const [res1, res2, res3] = await Promise.all([
                compareFaceWithGroup(descriptor, groups[0]),
                compareFaceWithGroup(descriptor, groups[1]),
                compareFaceWithGroup(descriptor, groups[2]),
            ]);

            const best = [res1, res2, res3].reduce(
                (a, b) => (b.distance < a.distance ? b : a)
            );

            if (best.distance < 0.55)
                statusEl.innerText = `LIVE ✅ Matched: ${best.name} (${best.distance.toFixed(2)})`;
            else
                statusEl.innerText = "LIVE ✅ No match found";
        }


        // --- INIT SEQUENCE ---
        // window.addEventListener('DOMContentLoaded', async () => {
        //     try {
        //         await loadModels(); // ✅ load models first
        //         await loadEmbeddings(); // ✅ then embeddings
        //         await startVideo(); // ✅ then camera
        //         statusEl.innerText = "Ready — show your face & blink!";
        //         setInterval(recognizeFace, 5000); // run every 5s
        //     } catch (err) {
        //         console.error("❌ Initialization error:", err);
        //         statusEl.innerText = "Error initializing system";
        //     }
        // });
        window.addEventListener("DOMContentLoaded", async () => {
            try {
                await loadModels(); // ⏳ first load (then cached)
                await loadEmbeddings(); // faces from backend
                await startVideo(); // start camera
                statusEl.innerText = "Ready — show your face & blink!";
            } catch (err) {
                console.error("❌ Initialization error:", err);
                statusEl.innerText = "Error initializing system";
            }
            // await loadModels();
            // await loadEmbeddings();
            // await startVideo();

            // statusEl.innerText = "Position your face in front of the camera...";

            // Continuously check for liveness
            // setInterval(monitorFace, 1000);
        });

        scanBtn.addEventListener("click", () => {
            recognizeFace();
        });
    </script>


    {{-- <script defer>
        const video = document.getElementById('video');
        const statusEl = document.getElementById('matchStatus');

        let groups = [];
        let lastX = null;
        let blinked = false;
        let headMoved = false;
        let lastLeft = 0;

        // Start webcam video
        async function startVideo() {
            // const stream = await navigator.mediaDevices.getUserMedia({
            //     video: {}
            // });

            let stream;
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: {
                            exact: "environment"
                        }
                    }
                });
            } catch {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
            }



            video.srcObject = stream;
        }

        // Load face-api.js models from same-origin to avoid CORS
        async function loadModels() {
            const modelPath = '/models'; // ✅ Place models inside Laravel public/models
            await faceapi.nets.tinyFaceDetector.loadFromUri('https://web1.nulinz.in/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('https://web1.nulinz.in/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('https://web1.nulinz.in/models');
            await faceapi.nets.faceExpressionNet.loadFromUri('https://web1.nulinz.in/models');

            modelsLoaded = true; // ✅ mark ready
            console.log("✅ Face API models loaded");
        }

        // Load embeddings from backend (faces_json)
        async function loadEmbeddings() {
            const res = await fetch('/faces_json');
            const faces = await res.json();

            // console.log(faces);

            // Split embeddings into 3 async groups for parallel comparison
            const chunkSize = Math.ceil(faces.length / 3);
            groups = [
                faces.slice(0, chunkSize),
                faces.slice(chunkSize, 2 * chunkSize),
                faces.slice(2 * chunkSize)
            ];
            // groups = groups.map(g => g.map(face => new Float32Array(face.embedding)));

            // console.log(groups);

            // console.log(`✅ Loaded ${faces.length} faces in 3 groups`);
        }

        // Eye aspect ratio — used for blink detection
        // function getEAR(eye) {
        //     const dist = (p1, p2) => Math.hypot(p1.x - p2.x, p1.y - p2.y);
        //     const A = dist(eye[1], eye[5]);
        //     const B = dist(eye[2], eye[4]);
        //     const C = dist(eye[0], eye[3]);
        //     return (A + B) / (2.0 * C);
        // }

        // Detect blink or head movement
        function checkLiveness(landmarks) {
            const leftEye = landmarks.getLeftEye();
            const nose = landmarks.getNose();
            const ear = getEAR(leftEye);

            // Blink detection
            if (lastLeft && ear < 0.22 && lastLeft - ear > 0.04) blinked = true;
            lastLeft = ear;

            // Head movement detection
            const x = nose[3].x;
            if (lastX && Math.abs(lastX - x) > 5) headMoved = true;
            lastX = x;

            return blinked || headMoved;
        }

        // Compare with one embedding group
        async function compareFaceWithGroup(descriptor, group) {
            let best = {
                name: 'Unknown',
                distance: 1.0
            };

            for (const person of group) {

                let raw = person.embedding;

                // If it's a stringified array → parse it
                if (typeof raw === 'string') {
                    try {
                        raw = JSON.parse(raw);
                    } catch (e) {
                        console.error('Invalid embedding JSON:', raw);
                        continue;
                    }
                }

                // If still not numeric → convert elements to float
                if (Array.isArray(raw)) {
                    raw = raw.map(v => parseFloat(v));
                } else if (raw && typeof raw.embedding !== 'undefined') {
                    // if wrapped like { embedding: [0.1, ...] }
                    raw = raw.embedding.map(v => parseFloat(v));
                }

                const emb = new Float32Array(raw);

                if (emb.length !== 128) {
                    console.warn(`⚠️ Invalid embedding length: got ${emb.length} instead of 128`, raw);
                    continue;
                }
                // console.log(person);
                // if (!person.encode || person.encode.length !== 128) {
                //     console.warn(`⚠️ Skipping ${person.name}, invalid embedding length`);
                //     continue;
                // }
                // const emb = new Float32Array(person.embedding);
                // console.log(emb);
                // Force numeric 128-float array
                // const emb = new Float32Array(person.embedding.map(v => parseFloat(v)));

                // console.log(emb);

                // if (descriptor.length !== emb.length) {
                //     console.warn(
                //         `❌ Length mismatch for ${person.name}: descriptor=${descriptor.length}, embedding=${emb.length}`
                //     );
                //     continue;
                // }
                const distance = faceapi.euclideanDistance(descriptor, emb);
                if (distance < best.distance) {
                    best = {
                        name: person.name,
                        distance
                    };
                }
            }

            return best;
        }

        // Detect and recognize face
        async function recognizeFace() {

            if (!modelsLoaded) {
                console.warn("Models not yet loaded, skipping frame...");
                return;
            }

            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors();

            if (detections.length === 0) {
                statusEl.innerText = "No face detected";
                return;
            }

            const {
                descriptor,
                landmarks
            } = detections[0];

            // console.log(landmarks);
            const live = checkLiveness(landmarks);

            if (!live) {
                statusEl.innerText = "Please blink or move head...";
                return;
            }

            // Compare with all 3 groups in parallel
            const [res1, res2, res3] = await Promise.all([
                compareFaceWithGroup(descriptor, groups[0]),
                compareFaceWithGroup(descriptor, groups[1]),
                compareFaceWithGroup(descriptor, groups[2]),
            ]);

            // Find best match
            const allResults = [res1, res2, res3];
            // console.log(allResults);
            const best = allResults.reduce((prev, cur) => cur.distance < prev.distance ? cur : prev);

            if (best.distance < 0.55) {
                statusEl.innerText = `LIVE ✅ Matched: ${best.name} (distance: ${best.distance.toFixed(2)})`;
            } else {
                statusEl.innerText = "LIVE ✅ No match found";
            }
        }

        // Initialize system
        (async () => {
            // await loadModels();
            await loadEmbeddings();
            await startVideo();
            statusEl.innerText = "Ready — show your face & blink!";
            setInterval(recognizeFace, 5000);
        })();
        window.addEventListener('DOMContentLoaded', loadModels);
    </script> --}}


    {{-- <script defer>
        const video = document.getElementById('video');
        const statusEl = document.getElementById('matchStatus');
        let groups = [];
        let lastX = null,
            blinked = false,
            headMoved = false,
            lastLeft = 0;

        async function startVideo() {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {}
            });
            video.srcObject = stream;
        }

        async function loadModels() {
            await faceapi.nets.tinyFaceDetector.loadFromUri('http://127.0.0.1:8000/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('http://127.0.0.1:8000/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('http://127.0.0.1:8000/models');
            await faceapi.nets.faceExpressionNet.loadFromUri('http://127.0.0.1:8000/models');
        }

        async function loadEmbeddings() {
            const res = await fetch('/faces_json');
            const faces = await res.json();

            // Split embeddings into 3 async groups
            const chunkSize = Math.ceil(faces.length / 3);
            groups = [faces.slice(0, chunkSize), faces.slice(chunkSize, 2 * chunkSize), faces.slice(2 * chunkSize)];

            // console.log(Loaded $ {
            //         faces.length
            //     }
            //     faces in 3 groups);
        }

        function getEAR(eye) {
            const dist = (p1, p2) => Math.hypot(p1.x - p2.x, p1.y - p2.y);
            const A = dist(eye[1], eye[5]);
            const B = dist(eye[2], eye[4]);
            const C = dist(eye[0], eye[3]);
            return (A + B) / (2.0 * C);
        }

        function checkLiveness(landmarks) {
            const leftEye = landmarks.getLeftEye();
            const nose = landmarks.getNose();
            const ear = getEAR(leftEye);

            if (lastLeft && ear < 0.22 && lastLeft - ear > 0.04) blinked = true;
            lastLeft = ear;

            const x = nose[3].x;
            if (lastX && Math.abs(lastX - x) > 5) headMoved = true;
            lastX = x;

            return blinked || headMoved;
        }

        // Compare with one group of embeddings
        async function compareFaceWithGroup(descriptor, group) {
            let best = {
                name: 'Unknown',
                distance: 1.0
            };

            for (const person of group) {
                const emb = new Float32Array(person.embedding);
                const distance = faceapi.euclideanDistance(descriptor, emb);
                if (distance < best.distance) {
                    best = {
                        name: person.name,
                        distance
                    };
                }
            }

            return best;
        }

        async function recognizeFace() {
            const detections = await faceapi.detectAllFaces(video)
                .withFaceLandmarks()
                .withFaceDescriptors();

            if (detections.length === 0) {
                statusEl.innerText = "No face detected";
                return;
            }

            const {
                descriptor,
                landmarks
            } = detections[0];
            const live = checkLiveness(landmarks);

            if (!live) {
                statusEl.innerText = "Please blink or move head...";
                return;
            }

            // Run all 3 groups in parallel
            const [res1, res2, res3] = await Promise.all([
                compareFaceWithGroup(descriptor, groups[0]),
                compareFaceWithGroup(descriptor, groups[1]),
                compareFaceWithGroup(descriptor, groups[2]),
            ]);

            // Find best overall match
            const allResults = [res1, res2, res3];
            const best = allResults.reduce((prev, cur) => cur.distance < prev.distance ? cur : prev);

            if (best.distance < 0.55) {
                statusEl.innerText = "LIVE✅ Matched: " + best.name + " (distance: " + best.distance.toFixed(2) + ")";
            } else {
                statusEl.innerText = "LIVE ✅ No match found";
            }
        }

        // Initialize all
        (async () => {
            await loadModels();
            await loadEmbeddings();
            await startVideo();
            statusEl.innerText = "Ready — show your face & blink!";
            setInterval(recognizeFace, 2000);
        })();
    </script> --}}



    {{-- <script defer>
        async function init() {
            try {
                // Load face-api.js models
                await faceapi.nets.tinyFaceDetector.loadFromUri('http://127.0.0.1:8000/models');
                await faceapi.nets.faceLandmark68Net.loadFromUri('http://127.0.0.1:8000/models');
                await faceapi.nets.faceRecognitionNet.loadFromUri('http://127.0.0.1:8000/models');
                await faceapi.nets.faceExpressionNet.loadFromUri('http://127.0.0.1:8000/models');

                const video = document.getElementById('video');
                const canvas = document.getElementById('overlay');
                const ctx = canvas.getContext('2d');
                const statusLabel = document.getElementById('matchStatus');


                const guideBox = {
                    x: 100,
                    y: 150,
                    width: 300,
                    height: 300
                };
                let ajaxSent = false;
                let blinkDetected = false;
                let blinkFrames = 0;

                // Start webcam
                let stream;
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: {
                                exact: "environment"
                            }
                        }
                    });
                } catch {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: true
                    });
                }
                video.srcObject = stream;

                // Helper: calculate distance between 2 points
                const getDistance = (p1, p2) => Math.hypot(p1.x - p2.x, p1.y - p2.y);

                // Helper: calculate EAR for an eye
                const getEAR = (eye) => {
                    const A = getDistance(eye[1], eye[5]);
                    const B = getDistance(eye[2], eye[4]);
                    const C = getDistance(eye[0], eye[3]);
                    return (A + B) / (2.0 * C);
                };

                // let smileDetected = false;
                // let headTurnDetected = false;
                // let livenessPassed = false;
                // let prevNoseX = null;
                let livenessInterval = null; // store the interval ID


                // let blinkFrames = 0;
                // let blinkDetected = false;
                let smileDetected = false;
                let headTurnDetected = false;
                let livenessPassed = false;
                let prevNoseX = null;
                let boxColor = 'blue';

                video.addEventListener('play', () => {
                    const displaySize = {
                        width: video.videoWidth,
                        height: video.videoHeight
                    };
                    faceapi.matchDimensions(canvas, displaySize);

                    // Only start once
                    if (livenessInterval) clearInterval(livenessInterval);

                    livenessInterval = setInterval(async () => {
                        const detections = await faceapi
                            .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                            .withFaceLandmarks()
                            .withFaceExpressions()
                            .withFaceDescriptors();
                        // console.log(detections);

                        // If no face detected, skip this loop iteration
                        if (!detections || detections.length === 0) {
                            // detections = [];

                            console.log("No face detected, skipping this frame...");
                            return;
                        } else {
                            // console.log("Face detected:", detections);
                        }

                        const resized = faceapi.resizeResults(detections, displaySize);
                        ctx.clearRect(0, 0, canvas.width, canvas.height);

                        for (const det of resized) {
                            const landmarks = det.landmarks;
                            const expressions = det.expressions;
                            const nose = landmarks.getNose();
                            const noseTip = nose[3]; // center of nose bridge

                            // /* -------- 👁️ 1. Eye Blink Detection -------- */
                            const leftEAR = getEAR(landmarks.getLeftEye());
                            const rightEAR = getEAR(landmarks.getRightEye());
                            const ear = (leftEAR + rightEAR) / 2;

                            if (ear < 0.25) {
                                blinkFrames++;
                                statusLabel.innerText =
                                    "👁️ Eyes closed... please blink naturally.";
                                console.log("Eye closed frame:", blinkFrames, "EAR:", ear.toFixed(
                                    3));
                            } else {
                                console.log("Eye opened frame:", blinkFrames, "EAR:", ear.toFixed(
                                    3));
                                if (blinkFrames > 2 && !blinkDetected) {
                                    blinkDetected = true;
                                    statusLabel.innerText = "✅ Blink detected!";
                                    console.log("Blink detected after", blinkFrames, "frames");
                                }
                                blinkFrames = 0; // reset after eye reopens
                            }

                            /* -------- 🧠 2. Head Movement Detection -------- */
                            // if (prevNoseX !== null && noseTip) {
                            //     const moveX = noseTip.x - prevNoseX;
                            //     if (!headTurnDetected && Math.abs(moveX) > 15) {
                            //         headTurnDetected = true;
                            //         statusLabel.innerText = "👀 Head movement detected!";
                            //         console.log("Head moved by:", moveX.toFixed(2), "px");
                            //     }
                            // }
                            // prevNoseX = noseTip.x;

                            // 😀 Smile detection
                            // if (!smileDetected && expressions.happy > 0.7) {
                            //     smileDetected = true;
                            //     console.log("😀 Smile detected!");
                            //     statusLabel.innerText = "😀 Smile detected!";
                            // }

                            /* -------- ✅ 3. Final Liveness Check -------- */
                            if (blinkDetected && !livenessPassed) {
                                livenessPassed = true;
                                statusLabel.innerText = "✅ Liveness verified successfully!";
                                console.log("✅ Real human confirmed.");

                                alert("Liveness check passed!");
                                // Optional: stop further checks
                                clearInterval(livenessInterval);

                                // Optional: trigger verification
                                // verifyFace(det.descriptor);
                            }
                        }



                        // for (const det of resized) {
                        //     const landmarks = det.landmarks;
                        //     // const nose = landmarks.getNose(); // 9 points
                        //     // const expressions = det.expressions;

                        //     // if (!nose || !expressions) return;

                        //     const leftEAR = getEAR(landmarks.getLeftEye());
                        //     const rightEAR = getEAR(landmarks.getRightEye());
                        //     const ear = (leftEAR + rightEAR) / 2;

                        //     //  console.log('EAR:', ear.toFixed(3));

                        //         if (ear < 0.25) {
                        //             // Eye closed frame detected
                        //             blinkFrames++;

                        //             statusLabel.innerText = '👁️ Eyes closed... please blink naturally.';
                        //             console.log("Eye closed frame:", blinkFrames, "EAR:", ear.toFixed(3));

                        //         } else {
                        //             // Eye reopened — check if a full blink occurred
                        //             if (blinkFrames > 5 && !blinkDetected) {
                        //                 blinkDetected = true;
                        //                 console.log("✅ Blink detected! EAR:", ear.toFixed(3), "Frames:", blinkFrames);
                        //                 statusLabel.innerText = '✅ Blink detected, verifying face...';

                        //                 // You can trigger next step here
                        //                 // verifyFace();
                        //             }

                        //             // Reset the counter for the next blink
                        //             blinkFrames = 0;
                        //         }

                        //                         // console.log('Blink detected!');
                        //                         // playAlertSound();

                        //     // pick nose tip (middle)
                        //     // const noseTip = nose[3]; // near the center of the nose bridge

                        //     // --- 1️⃣ Detect Smile ---
                        //     // if (!smileDetected && expressions.happy > 0.6) {
                        //     //     smileDetected = true;
                        //     //     statusLabel.innerText = "😊 Smile detected! Now please turn your head right.";
                        //     //     console.log("Smile detected, happy:", expressions.happy.toFixed(2));
                        //     // }

                        //     // --- 2️⃣ Detect Head Movement ---
                        //     // if (prevNoseX !== null && noseTip) {
                        //     //     const moveX = noseTip.x - prevNoseX;

                        //     //     if (!headTurnDetected && Math.abs(moveX) > 15) {
                        //     //         headTurnDetected = true;
                        //     //         statusLabel.innerText = "👀 Head movement detected!";
                        //     //         console.log("Head moved by:", moveX.toFixed(2));
                        //     //          const headMoveValue = moveX.toFixed(2);

                        //     //         // Send the value to backend
                        //     //         fetch('/attd/save-headmove', {
                        //     //             method: 'POST',
                        //     //             headers: {
                        //     //                 'Content-Type': 'application/json',
                        //     //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        //     //             },
                        //     //             body: JSON.stringify({ head_move: headMoveValue }),
                        //     //         })
                        //     //         .then(res => res.json())
                        //     //         .then(data => console.log('Saved to express.json:', data))
                        //     //         .catch(err => console.error('Error saving head move:', err));

                        //     //     }
                        //     // }
                        //     // prevNoseX = noseTip.x;

                        //     // --- 3️⃣ Confirm Liveness when both actions are done ---
                        //     // if (smileDetected && headTurnDetected && !livenessPassed) {
                        //     //     livenessPassed = true;
                        //     //     statusLabel.innerText = "✅ Liveness verified successfully!";
                        //     //     console.log("✅ Liveness check passed.");
                        //     //     // playAlertSound();
                        //     //     // verifyFace(det.descriptor);
                        //     // }
                        // }


                        // for (const det of resized) {
                        //     const { box } = det.detection;
                        //     const descriptor = Array.from(det.descriptor);
                        //     // const landmarks = det.landmarks;

                        //     const insideGuide =
                        //         box.x > guideBox.x &&
                        //         box.y > guideBox.y &&
                        //         box.x + box.width < guideBox.x + guideBox.width &&
                        //         box.y + box.height < guideBox.y + guideBox.height;

                        //     if (insideGuide) {
                        //         boxColor = 'lime';

                        //         // // EAR-based blink detection
                        //         // if (!blinkDetected) {
                        //         //     const leftEAR = getEAR(landmarks.getLeftEye());
                        //         //     const rightEAR = getEAR(landmarks.getRightEye());
                        //         //     const ear = (leftEAR + rightEAR) / 2;

                        //         //     // console.log('EAR:', ear.toFixed(3));

                        //         //     if (ear < 0.25) {
                        //         //         blinkFrames++;

                        //         //         statusLabel.innerText = '✅ Blink not detected... please blink your eyes.';
                        //         //         console.log("Blink not detected, EAR:", ear.toFixed(3));
                        //         //         //  playAlertSound();
                        //         //         // console.log('Blink frame count:', blinkFrames);
                        //         //         // alert('Please blink your eyes to verify liveness.');
                        //         //     } else if (blinkFrames > 0) {
                        //         //         blinkDetected = true;
                        //         //         // alert('Blink detected! Verifying face...');
                        //         //         console.log("Blink detected, EAR:", ear.toFixed(3));
                        //         //         statusLabel.innerText = '✅ Blink detected, verifying face...';

                        //         //         // console.log('Blink detected!');
                        //         //         // playAlertSound();
                        //         //     }
                        //         // }

                        //         // Only send recognition after blink
                        //         if (blinkDetected && !ajaxSent) {
                        //             ajaxSent = true;
                        //             const faceImage = cropFace(video, box);
                        //             const empCode = await sendToServer(descriptor, faceImage);

                        //             if (empCode && empCode.emp_name) {
                        //                 statusLabel.innerText = `Matched: ${empCode.emp_name}`;
                        //                 if (empCode.attendance_action === 'check_in') showCheckInBackground();
                        //                 else if (empCode.attendance_action === 'check_out') showCheckOutBackground();
                        //             } else {
                        //                 statusLabel.innerText = 'Face not recognized';
                        //                 playErrorSound();
                        //             }

                        //             setTimeout(() => {
                        //                 ajaxSent = false;
                        //                 blinkDetected = false;
                        //                 blinkFrames = 0;
                        //                 statusLabel.innerText = 'Please blink your eyes to verify liveness 👁️';
                        //             }, 5000);
                        //         }
                        //     }
                        // }

                        // Draw guide box
                        ctx.strokeStyle = boxColor;
                        ctx.lineWidth = 3;
                        ctx.strokeRect(guideBox.x, guideBox.y, guideBox.width, guideBox.height);
                    }, 1000); // check frequently
                });
            } catch (error) {
                console.error('Initialization error:', error);
                alert('Unable to access camera or load models.');
            }
        }

        // Crop face helper
        function cropFace(video, box) {
            const c = document.createElement('canvas');
            c.width = box.width;
            c.height = box.height;
            const ctx = c.getContext('2d');
            ctx.drawImage(video, box.x, box.y, box.width, box.height, 0, 0, box.width, box.height);
            return c.toDataURL('image/jpeg');
        }
        window.addEventListener('DOMContentLoaded', init);
    </script> --}}

</body>

</html>
