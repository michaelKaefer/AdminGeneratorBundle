tinymce.init({
    license_key: 'gpl',
    selector: 'textarea.tinymce',
    plugins: 'lists link image table code',
    toolbar: 'undo redo | blocks | bold italic | bullist numlist | link image | table | code',
    menubar: false
});




document.querySelectorAll('.js-collection-type-add-item').forEach(button => {
    button.addEventListener('click' , function (e) {
        const container = button.previousElementSibling;

        let item = container.dataset.prototype.replace(
            /__name__/g,
            container.children.length,
        );

        const div = document.createElement('div');
        div.innerHTML = item;

        container.appendChild(div);
    })
});

document.querySelectorAll('.js-polymorphic-collection-type-add-item').forEach(initializeAddButtons);

function initializeAddButtons(button) {
    const container = button.parentElement.querySelector('[data-prototype-' + button.dataset.type + ']');

    new Sortable(container, {
        animation: 150,
        onEnd: function () {
            initializePositions(container);
        }
    });

    initializeDeleteButton(container);

    button.addEventListener('click' , () => addItem(button, container));
}

function addItem(button, container) {
    const item = document.createElement('template');

    function upperCaseFirstChar(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    const dataset = 'prototype' + upperCaseFirstChar(button.dataset.type);

    item.innerHTML = container
        .dataset[dataset]
        .replace(
            /__name__/g,
            container.childElementCount
        );

    container.appendChild(...item.content.childNodes);

    container.lastChild.querySelectorAll('.js-polymorphic-collection-type-add-item').forEach(initializeAddButtons);

    initializeDeleteButton(container);

    initializePositions(container);
}

function initializeDeleteButton(container) {
    for (const item of container.children) {
        if (0 === item.querySelectorAll(':scope > button').length) {
            let deleteButton = document.createElement('button');
            deleteButton.innerText = 'Delete';
            deleteButton.classList.add('delete')

            item.append(deleteButton);

            deleteButton.addEventListener('click', (e) => {
                e.preventDefault();
                item.remove();
            });
        }
    }
}

function initializePositions(container) {
    Array.from(container.children).forEach((item, index) => {
        const position = item.querySelector('input[name$="[position]"]');

        if (position) {
            position.value = index;
        }
    });
}




function createMediaPreview(mimeType, url, container) {
    if (!url || !mimeType) {
        return;
    }

    let element;

    if (mimeType.startsWith('image/')) {
        element = new Image();
        element.src = url;
        element.style.maxWidth = '100%';

    } else if (mimeType === 'application/pdf') {
        element = document.createElement('iframe');
        element.src = url;

    } else if (mimeType.startsWith('video/')) {
        element = document.createElement('video');
        element.src = url;
        element.controls = true;

    } else if (mimeType.startsWith('audio/')) {
        element = document.createElement('audio');
        element.src = url;
        element.controls = true;
    }

    if (element) {
        container.insertBefore(element, container.children[1]);
    }
}

document.querySelectorAll('[data-preview] > input[type="hidden"]').forEach(el => {
    const mimeType = el.dataset.mimeType;
    const url = el.value;
    const container = el.parentElement.querySelector('[data-form-type="file"]');

    createMediaPreview(mimeType, url, container);
});
