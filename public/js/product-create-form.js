const $element = document.querySelector('#ingredients')
const choices = new Choices($element, {
    removeItemButton: true,
})

choices.setChoices([
    { value: 'new', label: '➕ Nuevo ingrediente', placeholder: false }
], 'value', 'label', false)

const modal = new bootstrap.Modal(document.getElementById('modal-new-ingredient'))
const nameInput = document.getElementById('new-ingredient-name')
const aliasesInput = document.getElementById('new-ingredient-aliases')
const submitBtn = document.getElementById('new-ingredient-submit')
const nameError = document.getElementById('new-ingredient-name-error')
const serverError = document.getElementById('new-ingredient-server-error')

$element.addEventListener('change', (e) => {
    const selected = Array.from(e.target.selectedOptions).map(o => o.value)
    if (selected.includes('new')) {
        choices.removeActiveItemsByValue('new')
        modal.show()
    }
})

document.getElementById('modal-new-ingredient').addEventListener('show.bs.modal', () => {
    nameInput.value = ''
    aliasesInput.value = ''
    nameError.classList.add('d-none')
    serverError.classList.add('d-none')
    submitBtn.disabled = false
    setTimeout(() => nameInput.focus(), 300)
})

submitBtn.addEventListener('click', async () => {
    const name = nameInput.value.trim()

    if (!name) {
        nameError.classList.remove('d-none')
        return
    }

    nameError.classList.add('d-none')
    submitBtn.disabled = true

    try {
        const response = await fetch('/ingredients/create-ajax', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name: name,
                aliases: aliasesInput.value.trim() || null,
            })
        })

        if (!response.ok) throw new Error()

        const ingredient = await response.json()

        choices.setChoices([
            { value: String(ingredient.id), label: ingredient.name }
        ], 'value', 'label', false)

        choices.setChoiceByValue(String(ingredient.id))

        modal.hide()
    } catch (e) {
        serverError.classList.remove('d-none')
        submitBtn.disabled = false
    }
})
