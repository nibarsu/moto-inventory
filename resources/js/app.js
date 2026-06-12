import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import TomSelect from 'tom-select';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

const selectInstances = new WeakMap();

function initializeSearchableSelects() {
    document.querySelectorAll('select:not([multiple]):not([data-native-select])').forEach((element) => {
        if (selectInstances.has(element) || element.dataset.enhanced === 'true') {
            return;
        }

        const placeholderOption = element.querySelector('option[value=""]');
        const placeholder = element.dataset.placeholder
            ?? placeholderOption?.textContent?.trim()
            ?? '請選擇';

        const instance = new TomSelect(element, {
            create: false,
            allowEmptyOption: true,
            placeholder,
            maxOptions: 200,
            hideSelected: true,
            plugins: {
                clear_button: {
                    title: '清除選擇',
                },
            },
            render: {
                no_results(data, escape) {
                    return `<div class="px-3 py-2 text-sm text-gray-500">找不到「${escape(data.input)}」</div>`;
                },
            },
        });

        selectInstances.set(element, instance);
        element.dataset.enhanced = 'true';
    });
}

function initializeDatePickers() {
    document.querySelectorAll('input[type="date"]:not([data-native-date])').forEach((element) => {
        if (element.dataset.enhanced === 'true') {
            return;
        }

        flatpickr(element, {
            dateFormat: 'Y-m-d',
            allowInput: true,
            disableMobile: true,
            locale: {
                firstDayOfWeek: 1,
            },
        });

        element.dataset.enhanced = 'true';
    });
}

function initializeDeleteConfirmation() {
    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const methodInput = form.querySelector('input[name="_method"]');
        const isDeleteForm = methodInput?.value?.toUpperCase() === 'DELETE';

        if (!isDeleteForm || form.dataset.confirmed === 'true') {
            return;
        }

        event.preventDefault();

        Swal.fire({
            title: '確認刪除？',
            text: '刪除後將無法復原。',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '確認刪除',
            cancelButtonText: '取消',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                confirmButton: 'swal2-confirm btn-danger',
                cancelButton: 'swal2-cancel btn-secondary',
            },
            buttonsStyling: false,
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            form.dataset.confirmed = 'true';
            form.requestSubmit();
        });
    });
}

function initializeFlashMessages() {
    const flashElement = document.querySelector('[data-flash-success]');

    if (!flashElement) {
        return;
    }

    const message = flashElement.dataset.flashSuccess?.trim();

    if (!message) {
        return;
    }

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: message,
        showConfirmButton: false,
        timer: 2600,
        timerProgressBar: true,
    });
}

function initializeUiEnhancements() {
    initializeSearchableSelects();
    initializeDatePickers();
    initializeFlashMessages();
}

document.addEventListener('DOMContentLoaded', () => {
    initializeUiEnhancements();
    initializeDeleteConfirmation();
});

Alpine.start();
