import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['modal'];

    connect() {
        document.addEventListener('keydown', this._handleEscapeKey.bind(this));
    }

    disconnect() {
        document.removeEventListener('keydown', this._handleEscapeKey.bind(this));
    }

    open() {
        this.modalTarget.classList.remove('hidden');
        setTimeout(() => {
            this.modalTarget.querySelector('.modal-content').classList.remove('opacity-0');
            this.modalTarget.querySelector('.modal-content').classList.remove('translate-y-4');
        }, 50);
        document.body.classList.add('overflow-hidden');
    }

    close() {
        const modal = this.modalTarget;
        const content = modal.querySelector('.modal-content');
        
        content.classList.add('opacity-0', 'translate-y-4');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    }

    _handleEscapeKey(event) {
        if (event.key === 'Escape' && !this.modalTarget.classList.contains('hidden')) {
            this.close();
        }
    }

    // Close when clicking on the backdrop
    clickOutside(event) {
        if (event.target === this.modalTarget) {
            this.close();
        }
    }
}