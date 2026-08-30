import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'nameSource'];

    connect() {
        this.addAiButton();
    }

    addAiButton() {
        if (this.element.querySelector('.ztc-ai-gen-btn')) {
            return;
        }

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-sm btn-outline-warning my-2 ztc-ai-gen-btn fw-bold';
        button.style.letterSpacing = '0.03em';
        button.innerHTML = 'Générer la Description par IA (Qwen 2.5 Local)';

        button.addEventListener('click', (e) => {
            e.preventDefault();
            this.generateDescription(button);
        });

        this.element.parentNode.insertBefore(button, this.element);
    }

    async generateDescription(button) {
        const nameInput = document.querySelector('input[id$="_translations_fr_FR_name"], input[id$="_translations_fr_name"], input[name*="[title]"], input[name*="[name]"]');
        const nameValue = nameInput ? nameInput.value : '';

        if (!nameValue) {
            alert('Veuillez d\'abord saisir le Nom de la Création.');
            return;
        }

        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = 'Génération IA en cours (Qwen 2.5)...';

        try {
            const response = await fetch('/admin/ajax/ai/generate-description', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: nameValue }),
            });

            const data = await response.json();
            if (data.success && data.content) {
                this.element.value = data.content;

                // Si Quill WYSIWYG est actif sur le champ
                const wrapper = this.element.previousElementSibling;
                if (wrapper && wrapper.classList.contains('ql-container')) {
                    const editor = wrapper.querySelector('.ql-editor');
                    if (editor) {
                        editor.innerHTML = data.content;
                    }
                }
            }
        } catch (error) {
            console.error('Erreur Génération IA:', error);
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }
}
