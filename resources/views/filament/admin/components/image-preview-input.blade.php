@php
    $fieldId = $getFieldName() ?? str()->random(10);
@endphp

<div class="space-y-3" x-data="imagePreview(@json($fieldId))" x-cloak>
    <!-- TextInput & FileUpload di sini via parent -->
    
    <!-- Image Preview Section -->
    <div x-show="imageUrl" x-transition class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
        <p class="mb-3 text-xs font-medium text-gray-600 dark:text-gray-400">📸 Preview Gambar:</p>
        <div class="relative overflow-hidden rounded-lg bg-gray-100 dark:bg-black/50">
            <img 
                :src="imageUrl" 
                alt="Preview" 
                class="max-h-80 w-full object-cover"
                @error="imageUrl = null"
            />
            <div class="absolute bottom-2 right-2 rounded bg-black/70 px-2 py-1 text-xs text-gray-300">
                Ukuran: <span x-text="imageDimensions"></span>
            </div>
        </div>
        <p class="mt-2 text-xs text-gray-500">
            <span class="text-gray-600 dark:text-gray-400" x-text="imageUrl"></span>
        </p>
    </div>

    <!-- No Image Message -->
    <div x-show="!imageUrl" class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 text-center dark:border-gray-600 dark:bg-gray-900/30">
        <p class="text-sm text-gray-600 dark:text-gray-400">🖼️ Upload atau paste URL gambar untuk melihat preview</p>
    </div>
</div>

<script>
    function imagePreview(fieldId) {
        return {
            imageUrl: null,
            imageDimensions: 'Memuat...',
            
            init() {
                // Get TextInput value
                const textInput = document.querySelector(`input[data-image-field="${fieldId}"]`);
                const fileUpload = document.querySelector(`input[data-file-upload="${fieldId}"]`);
                
                if (textInput) {
                    this.imageUrl = textInput.value || null;
                    
                    // Listen to TextInput changes
                    textInput.addEventListener('input', (e) => {
                        this.imageUrl = e.target.value || null;
                        this.updateDimensions();
                    });
                    textInput.addEventListener('change', (e) => {
                        this.imageUrl = e.target.value || null;
                        this.updateDimensions();
                    });
                }
                
                if (fileUpload) {
                    // Listen to FileUpload changes (trigger after upload)
                    fileUpload.addEventListener('change', (e) => {
                        if (e.target.files?.[0]) {
                            const reader = new FileReader();
                            reader.onload = (event) => {
                                this.imageUrl = event.target.result;
                                this.updateDimensions();
                            };
                            reader.readAsDataURL(e.target.files[0]);
                        }
                    });
                }
                
                // Initial dimensions
                this.updateDimensions();
            },
            
            updateDimensions() {
                if (!this.imageUrl) {
                    this.imageDimensions = '';
                    return;
                }
                
                const img = new Image();
                img.onload = () => {
                    this.imageDimensions = `${img.width}x${img.height}px`;
                };
                img.onerror = () => {
                    this.imageDimensions = 'Format tidak valid';
                };
                img.src = this.imageUrl;
            }
        }
    }
</script>
