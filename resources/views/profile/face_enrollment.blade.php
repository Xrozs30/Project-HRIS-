@extends('layouts.admin')

@section('title', 'Face Enrollment - HRIS')


@section('content')
<style>
    #videoWrapper { position: relative; border-radius: 16px; overflow: hidden; background: #111; max-width: 480px; margin: auto; }
    #videoWrapper video { width: 100%; display: block; border-radius: 16px; }
    #faceCanvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; }
    @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.3; } }
    .animate-pulse-custom { animation: pulse 1s infinite; }
</style>

<div class="flex justify-center items-center">
    <div class="w-full max-w-2xl">

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center font-bold shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill mr-2 text-lg"></i> {{ session('success') }}
            </div>
        @elseif(session('warning'))
            <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl mb-6 flex items-center font-bold shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill mr-2 text-lg"></i> {{ session('warning') }}
            </div>
        @endif

        @if(auth()->user()->employee_face_descriptor)
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl mb-6 flex items-center font-bold shadow-sm">
                <i class="bi bi-person-check-fill mr-2 text-lg"></i> You already have a face enrolled. Re-enrolling will replace it.
            </div>
        @else
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center font-bold shadow-sm">
                <i class="bi bi-exclamation-octagon-fill mr-2 text-lg"></i> Wajah belum terdaftar! Anda tidak dapat mengakses menu lain sebelum mendaftarkan wajah.
            </div>
            <style>
                /* Hide sidebar and header navigation to force user to stay on this page */
                #sidebarMenu, .navbar { display: none !important; }
                main { margin-left: auto !important; margin-right: auto !important; max-width: 800px; padding-top: 50px !important; }
            </style>
        @endif

        <div class="bg-white border-0 shadow-sm rounded-3xl p-8 mb-8 text-center ring-1 ring-gray-100">
            <h5 class="font-bold text-2xl text-gray-800 mb-2">Register Your Face</h5>
            <p class="text-gray-500 text-sm mb-6">Position your face clearly in the camera, then click "Save Face". This is required to use the Attendance feature.</p>

            <div id="statusDiv" class="mb-6 flex justify-center">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm bg-gray-50 text-gray-500 border border-gray-200">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-500 animate-pulse-custom"></span>
                    <span id="statusText">Loading AI model...</span>
                </span>
            </div>

            <div id="videoWrapper" class="mb-8 shadow-md">
                <video id="enrollVideo" autoplay muted playsinline></video>
                <canvas id="faceCanvas"></canvas>
            </div>

            <button id="saveFaceBtn" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3.5 px-6 rounded-xl shadow-sm transition-colors border-0 cursor-pointer w-full text-lg flex justify-center items-center disabled:opacity-60 disabled:cursor-not-allowed" disabled>
                <i class="bi bi-person-bounding-box mr-2"></i> Save My Face
            </button>

            <form id="faceForm" method="POST" action="{{ route('profile.face.save') }}">
                @csrf
                <input type="hidden" id="faceDescriptorInput" name="face_descriptor">
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
const video  = document.getElementById('enrollVideo');
const canvas = document.getElementById('faceCanvas');
const btn    = document.getElementById('saveFaceBtn');
const statusDiv  = document.getElementById('statusDiv');
const statusText = document.getElementById('statusText');

let lastDescriptor = null;
let apiLoaded = false;

async function loadModels() {
    const URL = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@0.22.2/weights';
    await faceapi.nets.tinyFaceDetector.loadFromUri(URL);
    await faceapi.nets.faceLandmark68TinyNet.loadFromUri(URL);
    await faceapi.nets.faceRecognitionNet.loadFromUri(URL);
    apiLoaded = true;
    startVideo();
}

async function startVideo() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 480, height: 360 } });
        video.srcObject = stream;
        video.addEventListener('loadedmetadata', () => {
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            detectLoop();
        });
    } catch(e) {
        setStatus('danger', 'Camera access denied.');
    }
}

async function detectLoop() {
    if (!apiLoaded) return;
    const detections = await faceapi
        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks(true)
        .withFaceDescriptors();

    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    const resized = faceapi.resizeResults(detections, { width: canvas.width, height: canvas.height });
    faceapi.draw.drawDetections(canvas, resized);
    faceapi.draw.drawFaceLandmarks(canvas, resized);

    if (detections.length === 1) {
        lastDescriptor = detections[0].descriptor;
        setStatus('success', 'Face detected! Click "Save My Face" to enroll.');
        btn.disabled = false;
    } else if (detections.length === 0) {
        lastDescriptor = null;
        setStatus('warning', 'No face detected. Position your face.');
        btn.disabled = true;
    } else {
        lastDescriptor = null;
        setStatus('danger', 'Multiple faces detected. Ensure only you are in frame.');
        btn.disabled = true;
    }

    setTimeout(detectLoop, 500);
}

function setStatus(type, msg) {
    const map = {
        success: { bg: 'bg-green-50', text: 'text-green-700', border: 'border-green-200', dot: 'bg-green-600' },
        warning: { bg: 'bg-amber-50', text: 'text-amber-700', border: 'border-amber-200', dot: 'bg-amber-500' },
        danger:  { bg: 'bg-red-50',  text: 'text-red-700',  border: 'border-red-200', dot: 'bg-red-600'  },
    };
    const s = map[type] || map.warning;
    statusDiv.innerHTML = `<span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm ${s.bg} ${s.text} border ${s.border}">
        <span class="w-2.5 h-2.5 rounded-full ${s.dot} animate-pulse-custom"></span>
        <span id="statusText">${msg}</span>
    </span>`;
}

btn.addEventListener('click', function() {
    if (!lastDescriptor) { alert('No face detected. Please try again.'); return; }
    document.getElementById('faceDescriptorInput').value = JSON.stringify(Array.from(lastDescriptor));
    document.getElementById('faceForm').submit();
});

setStatus('warning', 'Loading AI...');
loadModels().catch(e => setStatus('danger', 'AI load failed: ' + e.message));
</script>
@endpush
@endsection
