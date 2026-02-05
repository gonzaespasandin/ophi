const $element = document.querySelector('#ingredient-children')
const choices = new Choices($element, {
    addChoices: true,
    removeItemButton: true,
})

const $element2 = document.querySelector('#ingredient-parent')
const choices2 = new Choices($element2, {
    addChoices: true,
    removeItemButton: true,
})

// TODO: Hacer que no se puedan repetir la misma opción en los 2 selects
// (Por ejemplo: Si quiero que x ingrediente sea hijo de "histamina", que no pueda tener de hijo a "histamina" también)
