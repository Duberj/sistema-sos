// report.js - VERSIÓN CORREGIDA Y SEGURA

// Variables y estructura base
let currentStep = 1;
let selectedIncident = null;
let selectedPrivacyOption = 'anonymous';
let evidenceFiles = [];
let reportData = {
    type: '',
    description: '',
    evidence: [],
    location: { campus: '', building: '', area: '' },
    privacy: 'anonymous',
    pseudonym: '',
    contact: '',
};

// Almacenamiento en caché de elementos del DOM
let dom = {};

// Inicializa todo cuando carga el DOM
document.addEventListener('DOMContentLoaded', function () {
    // Carga los elementos del DOM en el objeto 'dom'
    cacheDOMElements();
    setupEventListeners();
    updateStepDisplay();
    
    // Generar token CSRF si no existe
    if (!sessionStorage.getItem('csrf_token')) {
        sessionStorage.setItem('csrf_token', generateCSRFToken());
    }
});

function generateCSRFToken() {
    return Array.from(crypto.getRandomValues(new Uint8Array(32)))
        .map(b => b.toString(16).padStart(2, '0'))
        .join('');
}

function cacheDOMElements() {
    dom.stepContents = document.querySelectorAll('.step-content');
    dom.currentStepSpan = document.getElementById('currentStep');
    dom.stepNameSpan = document.getElementById('stepName');
    dom.stepProgress = document.getElementById('stepProgress');
    dom.headerBackBtn = document.getElementById('headerBackBtn');
    dom.nextBtn = document.getElementById('nextBtn');
    dom.buttonProgress = document.getElementById('buttonProgress');
    
    dom.incidentCards = document.querySelectorAll('.incident-card');
    dom.descriptionInput = document.getElementById('incidentDescription');
    dom.charCount = document.getElementById('charCount');
    
    dom.fileInput = document.getElementById('fileInput');
    dom.fileErrors = document.getElementById('fileErrors');
    dom.evidencePreview = document.getElementById('evidencePreview');
    dom.fileList = document.getElementById('fileList');
    dom.metadataStripped = document.getElementById('metadataStripped');
    
    dom.campusSelect = document.getElementById('campusSelect');
    
    dom.privacyOptions = document.querySelectorAll('.privacy-option');
    dom.pseudonymInputContainer = document.getElementById('pseudonymInput');
    dom.pseudonymInput = document.querySelector('#pseudonymInput input');
    dom.contactInputContainer = document.getElementById('contactInput');
    dom.contactInput = document.querySelector('#contactInput input');

    dom.reviewType = document.getElementById('reviewType');
    dom.reviewLocation = document.getElementById('reviewLocation');
    dom.reviewEvidence = document.getElementById('reviewEvidence');
    dom.reviewPrivacy = document.getElementById('reviewPrivacy');
    dom.reviewDescription = document.getElementById('reviewDescription');

    dom.successModal = document.getElementById('successModal');
    dom.trackingCode = document.getElementById('trackingCode');
}

function setupEventListeners() {
    // Selección tipo incidente
    dom.incidentCards.forEach((card) => {
        card.addEventListener('click', function () {
            dom.incidentCards.forEach((c) => c.classList.remove('selected'));
            this.classList.add('selected');
            selectedIncident = this.dataset.type;
            updateNextButton();
        });
    });

    // Descripción del incidente
    if (dom.descriptionInput) {
        dom.descriptionInput.addEventListener('input', function () {
            const charCount = this.value.length;
            dom.charCount.textContent = charCount;
            // Remover barra de progreso en tiempo real por seguridad
            updateNextButton();
        });
    }

    // Evidencia - VALIDACIÓN MEJORADA
    if (dom.fileInput) {
        dom.fileInput.addEventListener('change', async function (e) {
            let files = Array.from(e.target.files);
            let maxSize = 10 * 1024 * 1024; // 10MB
            let validatedFiles = [];
            let rejected = [];

            // LÍMITE DE ARCHIVOS POR USUARIO (anti-spam)
            if (evidenceFiles.length + files.length > 5) {
                alert('Máximo 5 archivos permitidos');
                return;
            }

            // Validar cada archivo
            for (let file of files) {
                if (file.size > maxSize) {
                    rejected.push({ name: file.name, reason: 'Tamaño excede 10MB' });
                    continue;
                }

                const buffer = await file.arrayBuffer();
                const bytes = new Uint8Array(buffer);
                let isValid = false;
                let reason = '';

                // Verificar firma del archivo por 'magic numbers'
                try {
                    // JPEG
                    if (bytes[0] === 0xff && bytes[1] === 0xd8 && bytes[2] === 0xff) {
                        isValid = true;
                    } // PNG
                    else if (bytes[0] === 0x89 && bytes[1] === 0x50 && bytes[2] === 0x4e && bytes[3] === 0x47) {
                        isValid = true;
                    } // WEBP
                    else if (bytes[0] === 0x52 && bytes[1] === 0x49 && bytes[2] === 0x46 && bytes[3] === 0x46 && bytes[8] === 0x57 && bytes[9] === 0x45 && bytes[10] === 0x42 && bytes[11] === 0x50) {
                        isValid = true;
                    } // MP4/HEIC/3GP
                    else if (bytes[4] === 0x66 && bytes[5] === 0x74 && bytes[6] === 0x79 && bytes[7] === 0x70) {
                        isValid = true;
                    } // WEBM
                    else if (bytes[0] === 0x1a && bytes[1] === 0x45 && bytes[2] === 0xdf && bytes[3] === 0xa3) {
                        isValid = true;
                    } // PDF
                    else if (bytes[0] === 0x25 && bytes[1] === 0x50 && bytes[2] === 0x44 && bytes[3] === 0x46) {
                        isValid = true;
                    } // DOC (OLE)
                    else if (bytes[0] === 0xd0 && bytes[1] === 0xcf && bytes[2] === 0x11 && bytes[3] === 0xe0) {
                        isValid = true;
                    } // DOCX/ZIP
                    else if (bytes[0] === 0x50 && bytes[1] === 0x4b && bytes[2] === 0x03 && bytes[3] === 0x04) {
                        isValid = true;
                    } else {
                        reason = 'Formato no permitido (archivo potencialmente malicioso)';
                    }
                } catch (err) {
                    reason = 'No se pudo leer el archivo (posible corrupción)';
                }

                if (!isValid) {
                    rejected.push({ name: file.name, reason: reason || 'Formato no permitido' });
                    continue;
                }

                validatedFiles.push(file);
            }

            if (validatedFiles.length > 5) validatedFiles = validatedFiles.slice(0, 5);
            evidenceFiles = validatedFiles;
            updateEvidencePreview();

            // Mostrar errores si los hay
            if (dom.fileErrors) {
                if (rejected.length > 0) {
                    dom.fileErrors.classList.remove('hidden');
                    dom.fileErrors.innerHTML =
                        '<strong>Archivos rechazados:</strong><ul class="mt-2 ml-4">' +
                        rejected.map((r) => `<li><strong>${escapeHtml(r.name)}</strong>: ${escapeHtml(r.reason)}</li>`).join('') +
                        '</ul>';
                } else {
                    dom.fileErrors.classList.add('hidden');
                    dom.fileErrors.innerHTML = '';
                }
            }
        });
    }

    // Ubicación
    if (dom.campusSelect) {
        dom.campusSelect.addEventListener('change', updateNextButton);
    }

    // Privacidad
    dom.privacyOptions.forEach((option) => {
        option.addEventListener('click', function () {
            selectedPrivacyOption = this.dataset.privacy;
            
            // Actualizar UI de selección
            dom.privacyOptions.forEach((o) => {
                o.classList.remove('selected');
                const circle = o.querySelector('div.w-6');
                const dot = o.querySelector('.w-2');
                if (circle) {
                    circle.classList.remove('bg-blue-600');
                    circle.classList.add('border-2', 'border-gray-300');
                }
                if (dot) dot.classList.add('hidden');
            });
            
            this.classList.add('selected');
            const circleSel = this.querySelector('.w-6');
            const dotSel = this.querySelector('.w-2');
            if (circleSel) {
                circleSel.classList.remove('border-2', 'border-gray-300');
                circleSel.classList.add('bg-blue-600');
            }
            if (dotSel) dotSel.classList.remove('hidden');
            
            // Mostrar/ocultar inputs correspondientes
            dom.pseudonymInputContainer.classList.toggle('hidden', selectedPrivacyOption !== 'pseudonym');
            dom.contactInputContainer.classList.toggle('hidden', selectedPrivacyOption !== 'contact');

            updateNextButton();
        });
    });

    // Inputs de seudónimo y contacto
    if (dom.pseudonymInput) {
        dom.pseudonymInput.addEventListener('input', function() {
            // VALIDACIÓN EN TIEMPO REAL DEL SEUDÓNIMO
            const validation = validatePseudonym(this.value);
            if (!validation.valid && this.value.length > 3) {
                this.setCustomValidity(validation.error);
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
            updateNextButton();
        });
    }
    if (dom.contactInput) {
        dom.contactInput.addEventListener('input', updateNextButton);
    }
}

function validatePseudonym(name) {
    name = name.trim();
    if (name.length < 3) return { valid: false, error: 'Mínimo 3 caracteres' };
    if (name.length > 30) return { valid: false, error: 'Máximo 30 caracteres' };
    
    const prohibited = ['admin', 'universidad', 'rector', 'policia', 'sos', 'uni', 
                       'puta', 'pendejo', 'idiota', 'estupido', 'anonimo', 'root'];
    const lower = name.toLowerCase();
    
    for (let word of prohibited) {
        if (lower.includes(word)) {
            return { valid: false, error: 'Nombre no permitido' };
        }
    }
    
    if (!/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-]+$/.test(name)) {
        return { valid: false, error: 'Caracteres no permitidos' };
    }
    
    return { valid: true };
}

function validateStep(step) {
    switch (step) {
        case 1:
            return selectedIncident !== null;
        case 2:
            return dom.descriptionInput.value.trim().length >= 10; // Mínimo 10 chars
        case 3:
            return true; // Evidencia es opcional
        case 4:
            return dom.campusSelect.value !== '';
        case 5:
            if (selectedPrivacyOption === 'pseudonym') {
                const pseudonym = dom.pseudonymInput.value.trim();
                return validatePseudonym(pseudonym).valid;
            } else if (selectedPrivacyOption === 'contact') {
                return validateEmail(dom.contactInput.value);
            }
            return true; // 'anonymous' siempre es válido
        case 6:
            return reportData.type && reportData.description && reportData.location.campus;
    }
    return false;
}

function updateNextButton() {
    const canProceed = validateStep(currentStep);
    dom.nextBtn.disabled = !canProceed;
    dom.nextBtn.style.opacity = canProceed ? '1' : '0.5';
}

function nextStep() {
    if (!dom.nextBtn.disabled) {
        saveStepData();
        if (currentStep < 6) {
            currentStep++;
            updateStepDisplay();
            if (currentStep === 6) {
                populateReview();
            }
        }
    }
}

function goBack() {
    if (currentStep === 1) return;
    if (currentStep > 1) {
        currentStep--;
        updateStepDisplay();
    }
}

function updateStepDisplay() {
    dom.stepContents.forEach((s) => s.classList.remove('active'));
    document.getElementById('step' + currentStep).classList.add('active');
    
    // Actualiza indicador de paso
    dom.currentStepSpan.textContent = currentStep;
    const stepNames = ['Tipo de incidente', 'Descripción', 'Evidencia', 'Ubicación', 'Anonimato', 'Confirmación'];
    dom.stepNameSpan.textContent = stepNames[currentStep - 1];
    dom.stepProgress.style.width = `${(currentStep / 6) * 100}%`;
    
    // Habilitar/deshabilitar el botón de retroceso
    if (dom.headerBackBtn) {
        if (currentStep === 1) {
            dom.headerBackBtn.classList.add('opacity-50', 'pointer-events-none');
            dom.headerBackBtn.setAttribute('aria-disabled', 'true');
        } else {
            dom.headerBackBtn.classList.remove('opacity-50', 'pointer-events-none');
            dom.headerBackBtn.removeAttribute('aria-disabled');
        }
    }

    // Ocultar/mostrar botones de navegación
    dom.nextBtn.classList.toggle('hidden', currentStep === 6);
    
    updateNextButton();
}

function saveStepData() {
    switch (currentStep) {
        case 1:
            reportData.type = selectedIncident;
            break;
        case 2:
            reportData.description = sanitizeInput(dom.descriptionInput.value, 500);
            break;
        case 3:
            reportData.evidence = evidenceFiles;
            break;
        case 4:
            reportData.location.campus = sanitizeInput(document.getElementById('campusSelect').value);
            reportData.location.building = sanitizeInput(document.getElementById('buildingInput').value, 200);
            reportData.location.area = sanitizeInput(document.getElementById('areaInput').value, 200);
            break;
        case 5:
            reportData.privacy = selectedPrivacyOption;
            if (selectedPrivacyOption === 'pseudonym') {
                reportData.pseudonym = sanitizeInput(dom.pseudonymInput.value, 50);
            } else if (selectedPrivacyOption === 'contact') {
                const email = sanitizeInput(dom.contactInput.value);
                reportData.contact = validateEmail(email) ? email : '';
            }
            break;
    }
}

function updateEvidencePreview() {
    if (!dom.evidencePreview || !dom.fileList) return;

    // REMOVIDO: Mensaje falso de metadatos eliminados
    dom.metadataStripped.classList.add('hidden');

    if (evidenceFiles.length > 0) {
        dom.evidencePreview.classList.remove('hidden');
        dom.fileList.innerHTML = '';
        
        evidenceFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between bg-gray-50 rounded-lg p-3';
            
            const span = document.createElement('span');
            span.textContent = file.name;
            span.className = 'truncate pr-4';
            
            const button = document.createElement('button');
            button.textContent = 'Eliminar';
            button.className = 'text-red-500 hover:text-red-700 flex-shrink-0 text-sm';
            button.onclick = () => removeFile(index);
            
            fileItem.appendChild(span);
            fileItem.appendChild(button);
            dom.fileList.appendChild(fileItem);
        });
    } else {
        dom.evidencePreview.classList.add('hidden');
    }
}

function populateReview() {
    const incidentTypes = {
        harassment: 'Acoso Sexual',
        violence: 'Violencia Física',
        discrimination: 'Discriminación',
        drugs: 'Consumo/Venta de Sustancias',
        weapons: 'Amenazas con Armas',
        academic: 'Acoso Académico',
        suicide: 'Ideación Suicida',
        extortion: 'Extorsión Digital',
        theft: 'Robo o Hurto',
        other: 'Otro',
    };
    
    dom.reviewType.textContent = incidentTypes[reportData.type] || '-';
    dom.reviewDescription.textContent = reportData.description || '-';
    dom.reviewEvidence.textContent = (reportData.evidence || []).length + ' archivos';
    
    const location = reportData.location;
    const locationText = sanitizeInput(location.campus) +
        (location.building ? ` - ${sanitizeInput(location.building)}` : '') +
        (location.area ? ` - ${sanitizeInput(location.area)}` : '');
    dom.reviewLocation.textContent = locationText || '-';
    
    let privacyText = '';
    if (reportData.privacy === 'pseudonym') {
        privacyText = `Seudónimo: ${sanitizeInput(reportData.pseudonym) || 'No especificado'}`;
    } else if (reportData.privacy === 'contact') {
        privacyText = `Contacto seguro: ${sanitizeInput(reportData.contact) || 'No especificado'}`;
    } else {
        privacyText = 'Totalmente anónimo';
    }
    dom.reviewPrivacy.textContent = privacyText;
}

// NUEVA FUNCIÓN: Enviar reporte al backend
async function submitReport() {
    const submitBtn = document.querySelector('#step6 button');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Enviando...';

    try {
        // 1. Subir archivos y eliminar metadatos
        let uploadedFiles = [];
        if (evidenceFiles.length > 0) {
            const formData = new FormData();
            formData.append('csrf_token', sessionStorage.getItem('csrf_token'));
            
            evidenceFiles.forEach(file => {
                formData.append('evidence[]', file);
            });

            console.log('Subiendo archivos...'); // Para depuración
            const uploadResponse = await fetch('api/upload_evidencia.php?tracking_code=' + trackingCode, {
                method: 'POST',
                body: formData
            });

            const uploadResult = await uploadResponse.json();
            
            if (!uploadResult.success) {
                throw new Error(uploadResult.error || 'Error al subir archivos');
            }
            
            uploadedFiles = uploadResult.files;
            
            // Mostrar mensaje de éxito
            if (uploadedFiles.length > 0) {
                alert('✅ Metadatos eliminados correctamente de ' + uploadedFiles.length + ' archivo(s)');
            }
        }

        // 2. Enviar datos de la denuncia
        const payload = {
            type: reportData.type,
            description: reportData.description,
            location: reportData.location,
            privacy: reportData.privacy,
            pseudonym: reportData.pseudonym || null,
            contact: reportData.contact || null,
            evidence: uploadedFiles,
            csrf_token: sessionStorage.getItem('csrf_token')
        };

        const response = await fetch('api/procesar_denuncia.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            dom.trackingCode.textContent = result.tracking_code;
            dom.successModal.classList.remove('hidden');
            dom.successModal.classList.add('flex');
            
            // Limpiar datos sensibles
            sessionStorage.removeItem('csrf_token');
            evidenceFiles = [];
            reportData = {};
        } else {
            throw new Error(result.error || 'Error al procesar denuncia');
        }

    } catch (error) {
        alert('❌ Error: ' + error.message);
        console.error('Error completo:', error);
        submitBtn.disabled = false;
        submitBtn.textContent = '🚨 ENVIAR REPORTE ANÓNIMO';
    }

}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function cancelReport() {
    if (confirm('¿Estás seguro que quieres cancelar tu denuncia? Todos los datos se perderán.')) {
        // Limpiar datos sensibles
        evidenceFiles = [];
        reportData = {};
        sessionStorage.removeItem('csrf_token');
        window.location.href = 'index.html'; 
    }
}

function goHome() {
    if (confirm('¿Salir del formulario? Los datos no guardados se perderán.')) {
        cancelReport();
    }
}

// REMOVIDO: Funciones vacías que no hacen nada
// function showResources() {}
// function showSettings() {}

function selectFiles() {
    if (dom.fileInput) {
        dom.fileInput.click();
    }
}

function removeFile(index) {
    evidenceFiles.splice(index, 1);
    updateEvidencePreview();
}

// VALIDACIÓN MEJORADA
function sanitizeInput(str, maxLength = null) {
    if (!str) return '';
    let sanitized = String(str).trim();
    
    // Remover caracteres de control y scripts potenciales
    sanitized = sanitized.replace(/[\x00-\x1f\x7f-\x9f]/g, '');
    
    // Escapar HTML
    sanitized = sanitized
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
    
    if (maxLength && sanitized.length > maxLength) {
        sanitized = sanitized.substring(0, maxLength);
    }
    return sanitized;
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email) && email.length <= 100;
}