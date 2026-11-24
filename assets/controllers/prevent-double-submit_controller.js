import { Controller } from '@hotwired/stimulus';

// Prevents a form from being submitted twice (double click / double tap).
export default class extends Controller {
    connect()
    {
        this.submitting = false;

        // Reset the state when coming back to the page (browser back button).
        window.addEventListener('pageshow', this.reset);
        this.element.addEventListener('submit', this.onSubmit);
    }

    disconnect()
    {
        window.removeEventListener('pageshow', this.reset);
        this.element.removeEventListener('submit', this.onSubmit);
    }

    onSubmit = (event) => {
        if (this.submitting) {
            event.preventDefault();

            return;
        }

        this.submitting = true;
        this.element.querySelectorAll('[type="submit"]').forEach((button) => {
            button.classList.add('disabled');
            button.setAttribute('aria-disabled', 'true');
        });
    }

    reset = () => {
        this.submitting = false;
        this.element.querySelectorAll('[type="submit"]').forEach((button) => {
            button.classList.remove('disabled');
            button.removeAttribute('aria-disabled');
        });
    }
}
