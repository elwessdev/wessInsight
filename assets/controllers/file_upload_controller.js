import { Controller } from '@hotwired/stimulus';

/*
 * File upload controller
 * Handles displaying file name and preview when a file is selected
 */
export default class extends Controller {
    static targets = ['input', 'preview', 'filename']

    connect() {
        // Initialize any setup code if needed
    }

    // Called when file input changes
    preview() {
        const input = this.inputTarget;
        const previewContainer = this.previewTarget;
        const filenameElement = this.filenameTarget;
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Display filename
            filenameElement.textContent = file.name;
            
            // Show preview container
            previewContainer.classList.remove('hidden');
        } else {
            // Hide preview container when no file is selected
            previewContainer.classList.add('hidden');
            filenameElement.textContent = '';
        }
    }
}