const $modalConfirmDeleteTogglers = document.querySelectorAll('[data-bs-target="#modal-confirm-delete"]')
const $modalConfirmDeleteInputName = document.querySelector('#modal-confirm-delete-input-name')
const $modalConfirmDeleteInputId = document.querySelector('#modal-confirm-delete-input-id')

$modalConfirmDeleteTogglers.forEach(($btn) => {
    $btn.addEventListener('click', () => {
        $modalConfirmDeleteInputName.innerText = $btn.dataset.name
        $modalConfirmDeleteInputId.value = $btn.dataset.id

        console.log('Click on buttonnnn')
    })
})
