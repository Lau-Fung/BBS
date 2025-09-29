@once
    <style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: none;
    }
    
    .modal-overlay.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-content {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);
        max-width: 95vw;
        max-height: 90vh;
        overflow-y: auto;
        width: 100%;
        max-width: 1400px;
        min-width: 800px;
        border: 1px solid #e5e7eb;
    }
    
    .modal-header {
        padding: 1.5rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 2px solid #3b82f6;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .close-btn {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: white;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .close-btn:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Form styling within modal */
    .modal-body input[type="text"],
    .modal-body input[type="email"],
    .modal-body input[type="number"],
    .modal-body select,
    .modal-body textarea {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        width: 100%;
    }
    
    .modal-body input[type="text"]:focus,
    .modal-body input[type="email"]:focus,
    .modal-body input[type="number"]:focus,
    .modal-body select:focus,
    .modal-body textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .modal-body label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .modal-body .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .modal-body .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .modal-body .btn-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .modal-body .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Responsive form styling */
    @media (max-width: 1024px) {
        .modal-content {
            min-width: 90vw;
            max-width: 95vw;
        }
    }
    
    @media (max-width: 768px) {
        .modal-content {
            min-width: 95vw;
            max-width: 98vw;
        }
    }
    </style>
@endonce

<!-- Modal Overlay -->
<div id="clientSheetRowModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-800">
                {{ __('messages.clients.new_row') }}
            </h3>
            <button type="button" class="close-btn" onclick="closeModal()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div id="modalContent">
                <!-- Form content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function openModal(title, url) {
    const modal = document.getElementById('clientSheetRowModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    modalTitle.textContent = title;
    modalContent.innerHTML = '<div class="flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2" style="border-color: #3b82f6;"></div><span class="ml-3 text-gray-600">Loading...</span></div>';
    
    modal.classList.add('show');
    
    // Load form content
    fetch(url)
        .then(response => response.text())
        .then(html => {
            modalContent.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading form:', error);
            modalContent.innerHTML = '<div class="text-red-600 text-center py-8">Error loading form. Please try again.</div>';
        });
}

function closeModal() {
    const modal = document.getElementById('clientSheetRowModal');
    modal.classList.remove('show');
}

// Close modal when clicking outside
document.getElementById('clientSheetRowModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
