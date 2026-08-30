import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = { locale: String };

    connect() {
        this.addTranslateButton();
    }

    addTranslateButton() {
        if (this.element.querySelector('.ztc-ai-trans-btn')) {
            return;
        }

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-sm btn-outline-info my-2 ztc-ai-trans-btn fw-bold ms-2';
        button.innerHTML = `Traduire vers ${this.localeValue.toUpperCase()} par IA`;

        button.addEventListener('click', (e) => {
            e.preventDefault();
            this.translateField(button);
        });

        this.element.parentNode.insertBefore(button, this.element);
    }

    async translateField(button) {
        // Trouver le champ source correspondant en Français
        const fieldName = this.element.name;
        const sourceFieldName = fieldName.replace(`[${this.localeValue}]`, '[fr]').replace(`_${this.localeValue}_`, '_fr_FR_');

        const sourceInput = document.querySelector(`[name="${sourceFieldName}"]`) || document.querySelector('input[id*="fr"], textarea[id*="fr"]');
        const sourceText = sourceInput ? sourceInput.value : '';

        if (!sourceText) {
            alert('Le champ source en Français est vide.');
            return;
        }

        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = 'Traduction IA...';

        try {
            const response = await fetch('/admin/ajax/ai/translate-field', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    text: sourceText,
                    targetLocale: this.localeValue,
                }),
            });

            const data = await response.json();
            if (data.success && data.translatedText) {
                this.element.value = data.translatedText;

                // Si Quill WYSIWYG est présent
                const wrapper = this.element.previousElementSibling;
                if (wrapper && wrapper.classList.contains('ql-container')) {
                    const editor = wrapper.querySelector('.ql-editor');
                    if (editor) {
                        editor.innerHTML = data.translatedText;
                    }
                }
            }
        } catch (error) {
            console.error('Erreur Traduction IA:', error);
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }
}
