import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content', 'icon'];
    static values = { open: { type: Boolean, default: true } };

    connect() {
        this.updateState();
    }

    toggle(event) {
        event.preventDefault();
        this.openValue = !this.openValue;
        this.updateState();
    }

    updateState() {
        if (!this.hasContentTarget) {
            return;
        }

        if (this.openValue) {
            this.contentTarget.style.display = 'block';
            if (this.hasIconTarget) {
                this.iconTarget.textContent = '[-]';
            }
        } else {
            this.contentTarget.style.display = 'none';
            if (this.hasIconTarget) {
                this.iconTarget.textContent = '[+]';
            }
        }
    }
}
