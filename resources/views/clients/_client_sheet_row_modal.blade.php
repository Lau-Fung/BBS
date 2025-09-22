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
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        max-width: 90vw;
        max-height: 90vh;
        overflow-y: auto;
        width: 100%;
        max-width: 1200px;
    }
    
    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
        padding: 0.5rem;
        border-radius: 0.25rem;
    }
    
    .close-btn:hover {
        background-color: #f3f4f6;
        color: #374151;
    }
    </style>
@endonce

<!-- Modal Overlay -->
<div id="clientSheetRowModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">
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
    modalContent.innerHTML = '<div class="flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';
    
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
