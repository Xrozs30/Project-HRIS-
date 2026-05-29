@extends('layouts.admin')

@section('title', 'Attendance - HRIS')

@section('content')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<style>
    #videoWrapper { position: relative; border-radius: 16px; overflow: hidden; background: #000; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); }
    #videoWrapper video { width: 100%; border-radius: 16px; display: block; object-fit: cover; }
    #overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; }
    .pulse-dot { width: 10px; height: 10px; border-radius: 50%; animation: pulse 1s infinite; }
    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.3; } }
</style>

<div class="flex justify-center">
    <div class="w-full max-w-xl">
        <div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden">
            <div class="p-6 md:p-8 text-center">

                {{-- Session Flash Messages --}}
                @if(session('success'))
                    <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-4 mb-4 font-bold text-sm shadow-sm">{{ session('success') }}</div>
                @endif

                {{-- Attendance State --}}
                @if($cek && $cek->presence_time_out == null)
                    <div class="bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-xl p-4 mb-6 font-bold text-sm shadow-sm flex items-center justify-center">
                        <i class="bi bi-clock-history mr-2 text-lg"></i>You are clocked in since {{ $cek->presence_time_in }}. Please clock out.
                    </div>
                @elseif($cek && $cek->presence_time_out != null)
                    <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-4 mb-6 font-bold text-sm shadow-sm flex items-center justify-center">
                        <i class="bi bi-check-circle-fill mr-2 text-lg"></i>Attendance complete for today! In: {{ $cek->presence_time_in }} | Out: {{ $cek->presence_time_out }}
                    </div>
                @else
                    <div class="bg-blue-50 text-blue-700 border border-blue-200 rounded-xl p-4 mb-6 font-bold text-sm shadow-sm flex items-center justify-center">
                        <i class="bi bi-camera-fill mr-2 text-lg"></i>Please position your face and clock in.
                    </div>
                @endif

                {{-- Hidden location inputs --}}
                <input type="hidden" id="lat" value="">
                <input type="hidden" id="long" value="">

                @if(!$cek || ($cek && $cek->presence_time_out == null))
                    <div class="flex flex-col gap-3 mb-6 items-center">
                        {{-- Face Status Badge --}}
                        <div id="faceStatus">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm bg-gray-100 text-gray-500">
                                <span class="pulse-dot bg-gray-500"></span>
                                <span id="faceStatusText">Loading face model...</span>
                            </span>
                        </div>

                        {{-- GPS Status --}}
                        <div id="gpsStatus">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm bg-yellow-50 text-yellow-600">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span id="gpsStatusText">Getting location...</span>
                            </span>
                        </div>
                    </div>

                    {{-- Camera container (used by both webcam.js for snap and face-api for video) --}}
                    <div id="videoWrapper" class="mb-6 aspect-[4/3] w-full max-w-[480px] mx-auto">
                        <video id="liveVideo" autoplay muted playsinline class="h-full w-full"></video>
                        <canvas id="overlay"></canvas>
                    </div>

                    {{-- Hidden webcam.js div --}}
                    <div id="camera" style="display:none"></div>
                @endif

                <div class="mt-4">
                    @if(!$cek)
                        <button id="takeabsen" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-md transition-all flex items-center justify-center text-lg disabled:opacity-50 disabled:cursor-not-allowed border-0 cursor-pointer" disabled>
                            <i class="bi bi-camera-fill mr-2"></i> Clock In
                        </button>
                        <p class="text-gray-400 text-sm mt-3 font-medium" id="btnHint">Waiting for face verification & GPS...</p>
                    @elseif($cek && $cek->presence_time_out == null)
                        <button id="takeabsen_pulang" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 rounded-xl shadow-md transition-all flex items-center justify-center text-lg disabled:opacity-50 disabled:cursor-not-allowed border-0 cursor-pointer" disabled>
                            <i class="bi bi-box-arrow-right mr-2"></i> Clock Out
                        </button>
                        <p class="text-gray-400 text-sm mt-3 font-medium" id="btnHint">Waiting for face verification & GPS...</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<script>
// ===================== STORED FACE DESCRIPTOR =====================
const storedDescriptorRaw = {!! json_encode(auth()->user()->employee_face_descriptor ?? null) !!};
let storedDescriptor = null;
if (storedDescriptorRaw) {
    try { storedDescriptor = new Float32Array(JSON.parse(storedDescriptorRaw)); } catch(e) {}
}

// ===================== GPS =====================
let gpsReady = false;
const latInput  = document.getElementById('lat');
const longInput = document.getElementById('long');
const gpsStatusText = document.getElementById('gpsStatusText');
const gpsStatus = document.getElementById('gpsStatus');

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            latInput.value  = pos.coords.latitude;
            longInput.value = pos.coords.longitude;
            gpsReady = true;
            if (gpsStatus) {
                gpsStatus.innerHTML = '<span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm bg-green-50 text-green-600 border border-green-100"><i class="bi bi-geo-alt-fill"></i><span>Location acquired ✓</span></span>';
            }
            checkReady();
        },
        function() {
            gpsReady = true;
            if (gpsStatus) {
                gpsStatus.innerHTML = '<span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm bg-gray-50 text-gray-500 border border-gray-200"><i class="bi bi-geo-alt"></i><span>Location unavailable (allowed)</span></span>';
            }
            checkReady();
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
} else {
    gpsReady = true;
    checkReady();
}

// ===================== FACE API =====================
let faceVerified = false;
let faceApiLoaded = false;
let videoStream  = null;
const video  = document.getElementById('liveVideo');
const canvas = document.getElementById('overlay');
const faceStatusDiv  = document.getElementById('faceStatus');

async function loadModels() {
    const MODEL_URL = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@0.22.2/weights';
    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
    await faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL);
    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
    faceApiLoaded = true;
    startCamera();
}

async function startCamera() {
    try {
        videoStream = await navigator.mediaDevices.getUserMedia({ video: { width: 480, height: 360 } });
        video.srcObject = videoStream;
        video.addEventListener('loadedmetadata', () => {
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            detectLoop();
        });
    } catch(e) {
        updateFaceStatus('error', 'Camera access denied.');
    }
}

async function detectLoop() {
    if (!faceApiLoaded) return;

    const detections = await faceapi
        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks(true)
        .withFaceDescriptors();

    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    const resized = faceapi.resizeResults(detections, { width: canvas.width, height: canvas.height });
    faceapi.draw.drawDetections(canvas, resized);

    if (detections.length === 0) {
        faceVerified = false;
        updateFaceStatus('warning', 'No face detected');
    } else if (!storedDescriptor) {
        faceVerified = false;
        updateFaceStatus('danger', 'No face enrolled! Go to Profile first.');
    } else {
        const liveDescriptor = detections[0].descriptor;
        const distance = faceapi.euclideanDistance(liveDescriptor, storedDescriptor);
        if (distance < 0.43) {
            faceVerified = true;
            updateFaceStatus('success', 'Face Verified ✓ (' + (distance * 100).toFixed(0) + '% match)');
        } else {
            faceVerified = false;
            updateFaceStatus('danger', 'Face Mismatch ✗ (distance: ' + distance.toFixed(2) + ')');
        }
    }

    checkReady();
    setTimeout(detectLoop, 600);
}

function updateFaceStatus(type, message) {
    const map = {
        success: { bg: 'bg-green-50', text: 'text-green-600', dot: 'bg-green-500' },
        danger:  { bg: 'bg-red-50',  text: 'text-red-600',  dot: 'bg-red-500' },
        warning: { bg: 'bg-yellow-50', text: 'text-yellow-600', dot: 'bg-yellow-500' },
        error:   { bg: 'bg-red-50',  text: 'text-red-600',  dot: 'bg-red-500' },
    };
    const s = map[type] || map.warning;
    if (faceStatusDiv) {
        faceStatusDiv.innerHTML = `<span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm ${s.bg} ${s.text} border border-[currentcolor] border-opacity-20">
            <span class="pulse-dot ${s.dot}"></span><span>${message}</span>
        </span>`;
    }
}

function checkReady() {
    const btn_in  = document.getElementById('takeabsen');
    const btn_out = document.getElementById('takeabsen_pulang');
    const btn = btn_in || btn_out;
    const hint = document.getElementById('btnHint');
    
    if (!btn) return;

    if (gpsReady && faceVerified) {
        btn.disabled = false;
        if (hint) hint.textContent = '';
    } else {
        btn.disabled = true;
        if (hint) {
            const issues = [];
            if (!gpsReady) issues.push('GPS');
            if (!faceVerified) issues.push('Face verification');
            hint.textContent = 'Waiting for: ' + issues.join(', ') + '...';
        }
    }
}

// ===================== CAPTURE & SUBMIT (Clock In) =====================
const btn_absen = document.getElementById('takeabsen');
if (btn_absen) {
    btn_absen.addEventListener('click', function() {
        // Take snapshot from live video using canvas
        const snapCanvas = document.createElement('canvas');
        snapCanvas.width  = video.videoWidth;
        snapCanvas.height = video.videoHeight;
        const sCtx = snapCanvas.getContext('2d');
        sCtx.drawImage(video, 0, 0);
        const imageData = snapCanvas.toDataURL('image/jpeg', 0.9);

        submitAttendance('{{ route("presence.store") }}', imageData);
    });
}

function submitAttendance(action, imageData) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    appendHidden(form, '_token', '{{ csrf_token() }}');
    appendHidden(form, 'image', imageData);
    appendHidden(form, 'lat',   latInput.value  || '');
    appendHidden(form, 'long',  longInput.value || '');

    document.body.appendChild(form);
    form.submit();
}

function appendHidden(form, name, value) {
    const i = document.createElement('input');
    i.type = 'hidden'; i.name = name; i.value = value;
    form.appendChild(i);
}

// ===================== CLOCK OUT =====================
const btn_pulang = document.getElementById('takeabsen_pulang');
if (btn_pulang) {
    btn_pulang.addEventListener('click', function() {
        const snapCanvas = document.createElement('canvas');
        snapCanvas.width  = video.videoWidth;
        snapCanvas.height = video.videoHeight;
        const sCtx = snapCanvas.getContext('2d');
        sCtx.drawImage(video, 0, 0);
        const imageData = snapCanvas.toDataURL('image/jpeg', 0.9);

        submitAttendance('{{ route("presence.update") }}', imageData);
    });
}

// ===================== INIT =====================
const liveVideo = document.getElementById('liveVideo');
if (liveVideo) {
    updateFaceStatus('warning', 'Loading AI models...');
    loadModels().catch(e => updateFaceStatus('error', 'Failed to load AI: ' + e.message));
}
</script>
@endpush
@endsection
