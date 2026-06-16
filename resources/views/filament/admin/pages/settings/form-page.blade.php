<x-filament-panels::page>
    <!-- Breadcrumb & Description -->
    <div class="mb-8 rounded-xl border border-white/10 bg-gradient-to-br from-white/5 to-white/0 p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-400">⚙️ Pengaturan Website</p>
                <h1 class="mt-2 text-2xl font-bold text-white">
                    {{ $this->getTitle() }}
                </h1>
                @if ($subtitle = $this->getSubheading())
                    <p class="mt-2 text-sm leading-relaxed text-gray-300">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Form -->
    <form wire:submit="save" class="space-y-6" x-data="settingsImagePreview()" @load.window="init()">
        {{ $this->form }}

        <!-- Submit Section -->
        <div class="flex items-center justify-end gap-3 border-t border-white/10 pt-6">
            <p class="text-xs text-gray-500">💾 Semua perubahan akan disimpan otomatis</p>
            <x-filament::button type="submit" class="bg-primary-600 hover:bg-primary-700">
                ✓ Simpan Pengaturan
            </x-filament::button>
        </div>
    </form>

    <script>
        function settingsImagePreview() {
            return {
                init() {
                    // Find all URL input fields with media-related names
                    const mediaInputs = document.querySelectorAll('input[type="url"].fi-input, input[id*="_url"].fi-input');
                    
                    mediaInputs.forEach((input) => {
                        // Check if this looks like a media/image field
                        const fieldId = input.id || '';
                        const isMediaField = fieldId.includes('media_') || 
                                           fieldId.includes('image_') || 
                                           fieldId.includes('hero_');
                        
                        if (!isMediaField) return;
                        
                        const fieldKey = fieldId.replace('form.', '').replace(/\[\]/g, '');
                        
                        // Find or create preview container after field wrapper
                        let previewContainer = document.querySelector(`[data-preview-for="${fieldKey}"]`);
                        if (!previewContainer) {
                            previewContainer = document.createElement('div');
                            previewContainer.className = 'mt-4 space-y-3';
                            previewContainer.setAttribute('data-preview-for', fieldKey);
                            
                            // Insert after the input's form group wrapper
                            const wrapper = input.closest('[class*="fi-fo"]') || input.closest('.space-y');
                            wrapper?.parentElement?.insertBefore(previewContainer, wrapper.nextSibling);
                        }
                        
                        // Initial preview if value exists
                        this.updatePreview(fieldKey, input.value);
                        
                        // Listen to changes
                        input.addEventListener('input', (e) => {
                            this.updatePreview(fieldKey, e.target.value);
                        });
                        
                        input.addEventListener('change', (e) => {
                            this.updatePreview(fieldKey, e.target.value);
                        });
                    });
                    
                    // Also handle file uploads
                    const fileInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
                    fileInputs.forEach((fileInput) => {
                        const fileLabel = fileInput.closest('[class*="fi-fo"]')?.previousElementSibling?.textContent || '';
                        const isMediaFile = fileLabel.includes('Upload') || 
                                          fileLabel.includes('Gambar') || 
                                          fileLabel.includes('Image');
                        
                        if (!isMediaFile) return;
                        
                        fileInput.addEventListener('change', (e) => {
                            if (e.target.files?.[0]) {
                                // Find the associated URL input
                                const uploadLabel = fileInput.closest('[class*="fi-fo"]')?.previousElementSibling;
                                const formGroup = uploadLabel?.closest('.space-y');
                                const urlInput = formGroup?.querySelector('input[type="url"]');
                                
                                if (urlInput) {
                                    const fieldKey = urlInput.id?.replace('form.', '') || 'file-preview';
                                    const reader = new FileReader();
                                    reader.onload = (event) => {
                                        this.updatePreview(fieldKey, event.target.result);
                                    };
                                    reader.readAsDataURL(e.target.files[0]);
                                }
                            }
                        });
                    });
                },
                
                updatePreview(fieldKey, imageUrl) {
                    let container = document.querySelector(`[data-preview-for="${fieldKey}"]`);
                    if (!container) {
                        return; // Container not found, skip
                    }
                    
                    if (!imageUrl) {
                        container.innerHTML = `
                            <div class="rounded-lg border border-dashed border-gray-600 bg-gray-900/30 p-4 text-center">
                                <p class="text-sm text-gray-400">🖼️ Upload atau paste URL gambar untuk melihat preview</p>
                            </div>
                        `;
                        return;
                    }
                    
                    container.innerHTML = `
                        <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                            <p class="mb-3 text-xs font-medium text-gray-400">📸 Preview Gambar:</p>
                            <div class="relative overflow-hidden rounded-lg bg-black/50">
                                <img 
                                    src="${this.escapeHtml(imageUrl)}" 
                                    alt="Preview" 
                                    class="max-h-80 w-full object-cover"
                                    onerror="this.parentElement.innerHTML='<div class=\\'py-8 text-center text-gray-500\\'>❌ Gambar tidak bisa dimuat</div>'"
                                    loading="lazy"
                                />
                                <div class="absolute bottom-2 right-2 rounded bg-black/70 px-2 py-1 text-xs text-gray-300">
                                    <span class="image-dimensions">Memuat...</span>
                                </div>
                            </div>
                            <p class="mt-2 break-all text-xs text-gray-500 line-clamp-2 hover:line-clamp-none" title="${this.escapeHtml(imageUrl)}">
                                ${this.escapeHtml(imageUrl)}
                            </p>
                        </div>
                    `;
                    
                    // Get image dimensions
                    const img = new Image();
                    img.onload = () => {
                        const dimensionsEl = container.querySelector('.image-dimensions');
                        if (dimensionsEl) {
                            dimensionsEl.textContent = `${img.width}x${img.height}px`;
                        }
                    };
                    img.onerror = () => {
                        const dimensionsEl = container.querySelector('.image-dimensions');
                        if (dimensionsEl) {
                            dimensionsEl.textContent = 'Gagal memuat';
                        }
                    };
                    img.src = imageUrl;
                },
                
                escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }
            }
        }
    </script>
</x-filament-panels::page>
