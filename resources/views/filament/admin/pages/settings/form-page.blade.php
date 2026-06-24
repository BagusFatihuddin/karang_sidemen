<x-filament-panels::page>
    @php
        $isHomepageBuilder = $this instanceof \App\Filament\Admin\Pages\Settings\HomepageSettingsPage;
    @endphp

    <!-- Breadcrumb & Description -->
    <div class="settings-page-intro mb-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gradient-to-br dark:from-white/5 dark:to-white/0">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    {{ $isHomepageBuilder ? 'Homepage Builder' : 'Pengaturan Website' }}
                </p>
                <h1 class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">
                    {{ $this->getTitle() }}
                </h1>
                @if ($subtitle = $this->getSubheading())
                    <p class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
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
        <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-white/10">
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $isHomepageBuilder ? 'Simpan perubahan konten homepage' : 'Semua perubahan akan disimpan otomatis' }}</p>
            <x-filament::button type="submit" class="bg-primary-600 hover:bg-primary-700">
                {{ $isHomepageBuilder ? 'Simpan Homepage' : 'Simpan Pengaturan' }}
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
                    
                    // Also handle file uploads. Filament/Livewire can reject temporary uploads
                    // before submit, so surface a local message immediately.
                    const bindFileInputs = () => {
                        const fileInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
                        fileInputs.forEach((fileInput) => {
                            if (fileInput.dataset.settingsUploadBound === 'true') {
                                return;
                            }

                            fileInput.dataset.settingsUploadBound = 'true';

                            const fieldWrapper = fileInput.closest('[class*="fi-fo"]') || fileInput.closest('[data-field-wrapper]') || fileInput.parentElement;
                        const fileLabel = fieldWrapper?.textContent || '';
                        const isMediaFile = fileLabel.includes('Upload') ||
                                          fileLabel.includes('Gambar') ||
                                          fileLabel.includes('Image') ||
                                          fileInput.accept.includes('image');

                        if (!isMediaFile) return;

                        fileInput.addEventListener('livewire-upload-error', () => {
                            const maxMb = this.maxUploadSizeMb(fileInput, fileLabel);
                            this.showUploadError(
                                fileInput,
                                `Upload gagal. Pastikan file berupa gambar yang didukung dan ukurannya maksimal ${maxMb} MB.`
                            );
                        });

                        fileInput.addEventListener('change', (e) => {
                            const file = e.target.files?.[0];

                            if (!file) {
                                this.clearUploadError(fileInput);
                                return;
                            }

                            const maxMb = this.maxUploadSizeMb(fileInput, fileLabel);
                            const maxBytes = maxMb * 1024 * 1024;
                            const allowedTypes = this.allowedUploadTypes(fileInput, fileLabel);

                            if (!allowedTypes.includes(file.type)) {
                                this.showUploadError(
                                    fileInput,
                                    `Tipe file tidak sesuai. Gunakan ${this.allowedUploadLabel(fileLabel)}.`
                                );
                                e.target.value = '';
                                return;
                            }

                            if (file.size > maxBytes) {
                                this.showUploadError(
                                    fileInput,
                                    `Ukuran gambar terlalu besar. Maksimal ${maxMb} MB.`
                                );
                                e.target.value = '';
                                return;
                            }

                            this.clearUploadError(fileInput);

                            // Find the associated URL input
                            const uploadLabel = fieldWrapper?.previousElementSibling;
                            const formGroup = uploadLabel?.closest('.space-y');
                            const urlInput = formGroup?.querySelector('input[type="url"]');

                            if (urlInput) {
                                const fieldKey = urlInput.id?.replace('form.', '') || 'file-preview';
                                const reader = new FileReader();
                                reader.onload = (event) => {
                                    this.updatePreview(fieldKey, event.target.result);
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                        });
                    };

                    bindFileInputs();

                    const observer = new MutationObserver(() => bindFileInputs());
                    observer.observe(this.$root, { childList: true, subtree: true });

                    this.$root.addEventListener('livewire-upload-error', (event) => {
                        if (event.target?.matches?.('input[type="file"]')) {
                            const maxMb = this.maxUploadSizeMb(event.target);
                            this.showUploadError(
                                event.target,
                                `Upload gagal. Pastikan file berupa gambar yang didukung dan ukurannya maksimal ${maxMb} MB.`
                            );
                        }
                    }, true);
                },
                
                maxUploadSizeMb() {
                    return 2;
                },

                allowedUploadTypes(fileInput, labelText = '') {
                    const types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

                    if (fileInput.accept.includes('svg') || labelText.toLowerCase().includes('logo')) {
                        types.push('image/svg+xml');
                    }

                    return types;
                },

                allowedUploadLabel(labelText = '') {
                    return labelText.toLowerCase().includes('logo')
                        ? 'JPG, PNG, WEBP, atau SVG'
                        : 'JPG, PNG, atau WEBP';
                },

                uploadErrorContainer(fileInput) {
                    const wrapper = fileInput.closest('[class*="fi-fo"]') || fileInput.parentElement;
                    let container = wrapper?.querySelector('[data-settings-upload-error]');

                    if (!container && wrapper) {
                        container = document.createElement('p');
                        container.dataset.settingsUploadError = 'true';
                        container.className = 'mt-2 rounded-lg border border-danger-200 bg-danger-50 px-3 py-2 text-sm font-medium text-danger-700 dark:border-danger-800 dark:bg-danger-950/30 dark:text-danger-300';
                        wrapper.appendChild(container);
                    }

                    return container;
                },

                showUploadError(fileInput, message) {
                    const container = this.uploadErrorContainer(fileInput);

                    if (container) {
                        container.textContent = message;
                        container.hidden = false;
                    }
                },

                clearUploadError(fileInput) {
                    const container = this.uploadErrorContainer(fileInput);

                    if (container) {
                        container.textContent = '';
                        container.hidden = true;
                    }
                },

                updatePreview(fieldKey, imageUrl) {
                    let container = document.querySelector(`[data-preview-for="${fieldKey}"]`);
                    if (!container) {
                        return; // Container not found, skip
                    }
                    
                    if (!imageUrl) {
                        container.innerHTML = `
                            <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 text-center dark:border-gray-600 dark:bg-gray-900/30">
                                <p class="text-sm text-gray-600 dark:text-gray-400">🖼️ Upload atau paste URL gambar untuk melihat preview</p>
                            </div>
                        `;
                        return;
                    }
                    
                    container.innerHTML = `
                        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
                            <p class="mb-3 text-xs font-medium text-gray-600 dark:text-gray-400">📸 Preview Gambar:</p>
                            <div class="relative overflow-hidden rounded-lg bg-gray-100 dark:bg-black/50">
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
                            <p class="mt-2 break-all text-xs text-gray-500 dark:text-gray-400 line-clamp-2 hover:line-clamp-none" title="${this.escapeHtml(imageUrl)}">
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
