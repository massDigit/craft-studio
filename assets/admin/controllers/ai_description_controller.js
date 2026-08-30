import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'nameSource'];

    connect() {
        this.addAiButton();
    }

    addAiButton() {
        if (this.element.parentNode.querySelector('.ztc-ai-gen-btn')) {
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
        let nameValue = '';

        // 1. Recherche dans les champs du nom de la création
        const nameInputs = document.querySelectorAll('input[id*="translations_fr_FR_name"], input[id*="translations_fr_name"], input[id*="_name"], input[name*="[name]"]');
        for (const input of nameInputs) {
            if (input && input.value && input.value.trim() !== '') {
                nameValue = input.value.trim();
                break;
            }
        }

        // 2. En mode Édition, extrait le nom depuis le titre de la page ou du fil d'Ariane
        if (!nameValue) {
            const pageHeader = document.querySelector('.page-title, h1, .breadcrumb-item.active, .card-title');
            if (pageHeader && pageHeader.textContent) {
                const headerText = pageHeader.textContent.replace('Éditer', '').replace('Edit', '').trim();
                if (headerText !== '' && !headerText.includes('Créations') && !headerText.includes('Produits')) {
                    nameValue = headerText;
                }
            }
        }

        if (!nameValue) {
            alert('Veuillez d\'abord remplir le champ Nom de la Création.');
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
                // Mise à jour de la valeur sous-jacente
                this.element.value = data.content;

                // Rendu visuel riche WYSIWYG sans balises brutes pour l'administrateur
                const quillEditor = this.element.parentNode.querySelector('.ql-editor');
                if (quillEditor) {
                    quillEditor.innerHTML = data.content;
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
