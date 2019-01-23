import $ from 'jquery'

const SELECTED = 'selected'

const Selector = {
  ROOT: '.payment-methods',
  ITEM: '.payment-method',
  INPUT: 'input[type="radio"]',
  SELECTED: '.selected',
}

const _handleLabelClick = function (e) {
  const element = e.currentTarget
  const input = element.querySelector(Selector.INPUT)

  // Prevent action on non-input.
  if (!input) {
    return
  }

  let triggerChange = true
  const rootElement = $(element).closest(Selector.ROOT)[0]

  if (input.checked && $(element).hasClass(SELECTED)) {
    triggerChange = false
  } else {
    $(rootElement).children(Selector.SELECTED).removeClass(SELECTED)
  }

  if (triggerChange) {
    if (input.hasAttribute('disabled') || input.classList.contains('disabled')) {
      e.preventDefault()
      return
    }

    input.checked = !element.classList.contains(SELECTED)

    input.focus({ preventScroll: true })
    $(input).trigger('change')

    $(element).addClass(SELECTED)
  }
}

const _triggerPaymentMethod = function (e) {
  const input = e.currentTarget
  if (!input.checked) {
    return
  }

  const root = $(input).closest(Selector.ROOT)[0]
  const event = $.Event('selected.awebooking.gateway', {
    relatedTarget: input
  })

  $(root).trigger(event, input.value)
}

$(function () {
  const $el = $('#payment-methods')
    .on('click', Selector.ITEM, _handleLabelClick)
    .on('change', Selector.INPUT, _triggerPaymentMethod)

  setTimeout(() => {
    $el.find(`${Selector.INPUT}:checked`)
       .closest(Selector.ITEM)
       .trigger('click')
  }, 0)
})
