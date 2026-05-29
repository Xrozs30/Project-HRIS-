@extends('layouts.admin')

@section('title', 'Edit Employee - HRIS')

@section('content')
    <div class="flex justify-start mb-6">
        <a href="{{ route('karyawan.index') }}" class="text-gray-500 hover:text-blue-600 font-bold no-underline flex items-center transition-colors">
            <i class="bi bi-arrow-left mr-2"></i> Back
        </a>
    </div>

    <style>
        #videoWrapper { position: relative; border-radius: 12px; overflow: hidden; background: #111; max-width: 100%; aspect-ratio: 4/3; margin-bottom: 1rem; }
        #videoWrapper video { width: 100%; height: 100%; object-fit: cover; display: block; }
        #faceCanvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; }
        .pulse-dot { width: 8px; height: 8px; border-radius: 50%; animation: pulse 1s infinite; }
        @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.3; } }
    </style>

    <div class="bg-white border-0 shadow-sm rounded-2xl w-full max-w-5xl mx-auto overflow-hidden">
        <div class="p-6 md:p-10">
            @if($errors->any())
                <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-5 mb-6 shadow-sm relative" role="alert">
                    <h6 class="font-bold text-red-800 mb-2 flex items-center"><i class="bi bi-exclamation-triangle-fill mr-2"></i>Please fix the following errors:</h6>
                    <ul class="mb-0 text-sm pl-5 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('karyawan.update', $employee->employee_id) }}" method="POST" id="editEmployeeForm">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Left Column: Details -->
                    <div class="lg:col-span-7">
                        <h5 class="font-extrabold text-xl mb-6 text-gray-800 flex items-center"><i class="bi bi-person-lines-fill mr-3 text-blue-500"></i> Personal Details</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Kode</label>
                                <input type="text" name="employee_nik" value="{{ old('employee_nik', $employee->employee_nik) }}" class="w-full bg-gray-100 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none transition-colors cursor-not-allowed" readonly required>
                            </div>
                            <div>
                                <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Full Name</label>
                                <input type="text" name="employee_name" value="{{ old('employee_name', $employee->employee_name) }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Email</label>
                                <input type="email" name="employee_email" value="{{ old('employee_email', $employee->employee_email) }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" required>
                            </div>
                            <div>
                                <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Phone</label>
                                <input type="text" name="employee_phone" value="{{ old('employee_phone', $employee->employee_phone) }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Gender</label>
                                <select name="employee_gender" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors">
                                    <option value="">Select Gender...</option>
                                    <option value="male" {{ old('employee_gender', $employee->employee_gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('employee_gender', $employee->employee_gender) == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Birth Date</label>
                                <input type="date" name="employee_birth_date" value="{{ old('employee_birth_date', $employee->employee_birth_date) }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Address</label>
                            <textarea name="employee_address" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors min-h-[100px]">{{ old('employee_address', $employee->employee_address) }}</textarea>
                        </div>

                        <hr class="my-10 border-gray-100">

                        <h5 class="font-extrabold text-xl mb-6 text-gray-800 flex items-center"><i class="bi bi-bank mr-3 text-cyan-500"></i> Bank & Insurance Details</h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Bank Name</label>
                                <input type="text" name="employee_bank_name" value="{{ old('employee_bank_name', $employee->employee_bank_name) }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" placeholder="e.g. BCA">
                            </div>
                            <div>
                                <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Account Number</label>
                                <input type="text" name="employee_bank_number" value="{{ old('employee_bank_number', $employee->employee_bank_number) }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors">
                            </div>
                            <div>
                                <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">BPJS Number</label>
                                <input type="text" name="employee_bpjs_number" value="{{ old('employee_bpjs_number', $employee->employee_bpjs_number) }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors">
                            </div>
                        </div>

                        <hr class="my-10 border-gray-100">

                        <h5 class="font-extrabold text-xl mb-6 text-gray-800 flex items-center"><i class="bi bi-briefcase-fill mr-3 text-green-500"></i> Employment & Payroll</h5>
                        
                        <div class="mb-6">
                            <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Position</label>
                            <select name="position_id" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors cursor-pointer" required>
                                <option value="" disabled {{ old('position_id', $employee->position_id) ? '' : 'selected' }}>Select Position...</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->position_id }}" {{ old('position_id', $employee->position_id) == $position->position_id ? 'selected' : '' }}>{{ ucwords($position->position_type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Basic Salary (Rp)</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-gray-200 bg-gray-100 text-gray-500 font-bold">Rp</span>
                                    <input type="number" name="employee_basic_salary" value="{{ old('employee_basic_salary', $employee->employee_basic_salary) }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-r-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" min="0" required>
                                </div>
                            </div>
                            <div>
                                <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">PPh 21 Status</label>
                                <select name="tax_id" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" required>
                                    <option value="" disabled selected>Select Status...</option>
                                    @foreach($taxes as $tax)
                                        <option value="{{ $tax->tax_id }}" {{ old('tax_id', $employee->tax_id) == $tax->tax_id ? 'selected' : '' }}>{{ $tax->tax_status }} ({{ $tax->tax_type }} - {{ $tax->tax_amount * 100 }}%)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-10">
                            <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Password (Optional)</label>
                            <input type="password" name="employee_password" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" placeholder="Biarkan kosong jika tidak ingin mengganti password" autocomplete="new-password">
                        </div>
                    </div>
                
                    <!-- Right Column: Face Enrollment Reset -->
                    <div class="lg:col-span-5">
                        <div class="bg-gray-800 text-white border-0 shadow-sm rounded-2xl h-full">
                            <div class="p-6 md:p-8 flex flex-col h-full">
                                <h5 class="font-extrabold text-xl mb-4 flex items-center"><i class="bi bi-camera-video mr-3 text-yellow-400"></i> Reset Face</h5>
                                @if($employee->employee_face_descriptor)
                                    <div class="bg-green-500/20 text-green-300 font-bold px-4 py-3 rounded-xl text-sm mb-4 border border-green-500/30">
                                        <i class="bi bi-check-circle-fill mr-1.5"></i> Karyawan ini sudah memiliki data wajah.
                                    </div>
                                    <p class="text-xs text-gray-300 mb-6 leading-relaxed">Gunakan fitur ini HANYA JIKA Anda ingin mereset/mengganti data wajah karyawan ini.</p>
                                @else
                                    <div class="bg-red-500/20 text-red-300 font-bold px-4 py-3 rounded-xl text-sm mb-4 border border-red-500/30">
                                        <i class="bi bi-exclamation-triangle-fill mr-1.5"></i> Data wajah belum terdaftar.
                                    </div>
                                    <p class="text-xs text-gray-300 mb-6 leading-relaxed">Silakan unggah foto karyawan untuk mendaftarkan wajah secara manual jika diperlukan.</p>
                                @endif
                                
                                <div class="mb-6">
                                    <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Upload New Face Photo</label>
                                    <input type="file" id="photoUpload" class="w-full bg-gray-700 border border-gray-600 text-white rounded-xl px-4 py-3 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-600 file:text-white hover:file:bg-gray-500 transition-colors cursor-pointer" accept="image/*">
                                </div>
                                
                                <div id="statusDiv" class="mb-4 hidden">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm bg-gray-700/50">
                                        <span class="pulse-dot bg-white" id="pulseDot"></span>
                                        <span id="statusText" class="text-white">Analyzing Photo...</span>
                                    </span>
                                </div>

                                <div id="imageWrapper" class="text-center mb-4 hidden rounded-xl overflow-hidden bg-black max-h-[250px] shadow-inner">
                                    <img id="previewImage" class="max-w-full h-auto object-contain max-h-[250px] mx-auto">
                                </div>
                                
                                <div id="captureSuccessBadge" class="bg-green-500/20 text-green-400 border border-green-500/30 px-4 py-3 rounded-xl mt-4 hidden font-bold text-sm" role="alert">
                                    <i class="bi bi-check-circle-fill mr-1.5"></i> Face Profile Extracted Successfully!
                                </div>

                                <input type="hidden" id="faceDescriptorInput" name="employee_face_descriptor">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-col items-center">
                    <button type="submit" id="submitTotalBtn" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-10 rounded-xl transition-colors shadow-sm cursor-pointer border-0 inline-flex items-center justify-center">
                        <i class="bi bi-save mr-2 text-lg"></i> Update Data
                    </button>
                    <p class="text-center text-gray-400 text-sm mt-4">Data wajah tidak akan berubah jika Anda tidak mengunggah foto baru.</p>
                </div>

            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        const photoUpload        = document.getElementById('photoUpload');
        const previewImage       = document.getElementById('previewImage');
        const imageWrapper       = document.getElementById('imageWrapper');
        const statusDiv          = document.getElementById('statusDiv');
        const statusText         = document.getElementById('statusText');
        const pulseDot           = document.getElementById('pulseDot');
        const captureSuccessBadge= document.getElementById('captureSuccessBadge');
        const faceDescriptorInput= document.getElementById('faceDescriptorInput');
        const editForm           = document.getElementById('editEmployeeForm');

        let apiLoaded   = false;
        let faceValid   = false;

        async function loadModels() {
            const MODEL_URL = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@0.22.2/weights';
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
            await faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
            apiLoaded = true;
        }

        function setStatus(type, msg) {
            const map = {
                success: { text: 'text-green-400', dot: 'bg-green-400' },
                warning: { text: 'text-yellow-400', dot: 'bg-yellow-400' },
                danger:  { text: 'text-red-400',  dot: 'bg-red-400'  },
                info:    { text: 'text-blue-400',    dot: 'bg-blue-400'    },
            };
            const s = map[type] || map.warning;
            
            statusDiv.classList.remove('hidden');
            
            // Remove previous text colors and dot colors
            statusText.className = `text-sm font-bold ${s.text}`;
            pulseDot.className = `pulse-dot ${s.dot}`;
            statusText.innerText = msg;
        }

        photoUpload.addEventListener('change', async (e) => {
            const file = e.target.files[0];

            faceValid = false;
            faceDescriptorInput.value = '';
            captureSuccessBadge.classList.add('hidden');
            imageWrapper.classList.add('hidden');
            statusDiv.classList.add('hidden');

            if (!file) return;

            if (!apiLoaded) {
                setStatus('warning', 'AI masih loading...');
                return;
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                previewImage.src = event.target.result;
                imageWrapper.classList.remove('hidden');
                setStatus('info', 'Menganalisis foto...');
            };
            reader.readAsDataURL(file);
        });

        previewImage.addEventListener('load', async () => {
            if (!apiLoaded || !photoUpload.files.length) return;

            setStatus('info', 'Menganalisis foto...');

            try {
                const detections = await faceapi
                    .detectAllFaces(previewImage, new faceapi.TinyFaceDetectorOptions({ inputSize: 512, scoreThreshold: 0.5 }))
                    .withFaceLandmarks(true)
                    .withFaceDescriptors();

                if (detections.length === 0) {
                    faceValid = false;
                    faceDescriptorInput.value = '';
                    setStatus('danger', 'Tidak ada wajah terdeteksi.');
                } else if (detections.length > 1) {
                    faceValid = false;
                    faceDescriptorInput.value = '';
                    setStatus('danger', 'Terdeteksi > 1 wajah.');
                } else {
                    const descriptor = detections[0].descriptor;
                    faceDescriptorInput.value = JSON.stringify(Array.from(descriptor));
                    faceValid = true;
                    statusDiv.classList.add('hidden');
                    captureSuccessBadge.classList.remove('hidden');
                }
            } catch (err) {
                faceValid = false;
                setStatus('danger', 'Gagal: ' + err.message);
            }
        });

        editForm.addEventListener('submit', function(e) {
            const hasFile = photoUpload.files && photoUpload.files.length > 0;
            if (hasFile && !faceValid) {
                e.preventDefault();
                setStatus('danger', 'Foto tidak valid (tidak ada wajah).');
            }
        });

        loadModels().catch(e => console.error(e));
    </script>
    @endpush
@endsection
