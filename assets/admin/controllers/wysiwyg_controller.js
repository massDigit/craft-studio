import { Controller } from '@hotwired/stimulus';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

export default class extends Controller {
    connect() {
        if (this.element.dataset.wysiwygInitialized) {
            return;
        }
        this.element.dataset.wysiwygInitialized = 'true';

        const wrapper = document.createElement('div');
        wrapper.style.minHeight = '320px';
        wrapper.style.background = '#ffffff';
        wrapper.style.color = '#212529';
        wrapper.style.borderRadius = '0 0 8px 8px';

        this.element.parentNode.insertBefore(wrapper, this.element);
        this.element.style.display = 'none';

        wrapper.innerHTML = this.element.value || '';

        const quill = new Quill(wrapper, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ color: [] }, { background: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ align: [] }],
                    ['link', 'image'],
                    ['clean'],
                ],
            },
        });

        quill.on('text-change', () => {
            this.element.value = quill.root.innerHTML;
        });

        const form = this.element.closest('form');
        if (form) {
            form.addEventListener('submit', () => {
                this.element.value = quill.root.innerHTML;
            });
        }
    }
}
