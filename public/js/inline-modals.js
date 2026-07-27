/** MODAL EDIT */
const $modalEditTogglers = document.querySelectorAll('[data-bs-target="#modal-edit-inline"]')
const $modalEditInputName = document.querySelector('#modal-edit-inline-input-name')
const $modalEditInputId = document.querySelector('#modal-edit-inline-input-id')

$modalEditTogglers.forEach(($btn) => {
    $btn.addEventListener('click', () => {
        $modalEditInputName.value = $btn.dataset.name
        $modalEditInputId.value = $btn.dataset.id

        console.log('Click on buttonnnn')
    })
})
