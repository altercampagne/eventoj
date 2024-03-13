// @øee https://symfony.com/doc/current/form/form_collections.html#javascript-with-stimulus

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["collectionContainer"]

    static values = {
        index    : Number,
        prototype: String,
    }

    addCollectionElement(event)
    {
        const item = document.createElement('li');
        item.innerHTML = this.prototypeValue.replace(/__name__/g, this.indexValue);
        this.collectionContainerTarget.appendChild(item);
        this.indexValue++;
    }

    removeCollectionElement(event)
    {
        this.collectionContainerTarget.removeChild(document.querySelector(event.target.dataset.target).closest('li'));
        this.indexValue--;
    }
}
