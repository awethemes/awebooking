import $ from 'jquery'
import Control from './utils/control'

class SearchForm {
  constructor(root, instance) {
    this.root = $(root)
    this.instance = instance

    // Store the input elements
    this.elements = {}
    this.linkElements()

    const self = this

    window.createDatePicker(this, {
      onChange(props) {
        const { startDate, endDate } = props

        const elements = self.elements

        elements['check_in'].set(startDate ? startDate.format('YYYY-MM-DD') : '')
        elements['check_out'].set(endDate ? endDate.format('YYYY-MM-DD') : '')
      }
    })

    if (this.elements.hasOwnProperty('check_in_alt')) {
      this.elements['check_in_alt'].sync(this.elements['check_in'])
    }

    if (this.elements.hasOwnProperty('check_out')) {
      this.elements['check_out_alt'].sync(this.elements['check_out'])
    }
  }

  getRootElement() {
    return this.root[0]
  }

  /**
   * Link elements between settings and inputs
   */
  linkElements() {
    const control = this

    const nodes = control.root.find('[data-element]')
    let radios = {}

    nodes.each((index, element) => {
      let node = $(element)

      if (node.data('_controlSettingLinked')) {
        return
      }

      // Prevent re-linking element.
      node.data('_controlSettingLinked', true)

      if (node.is(':radio')) {
        let name = node.prop('name')

        if (radios[name]) {
          return
        }

        radios[name] = true
        node = nodes.filter('[name="' + name + '"]')
      }

      if (node.data('element')) {
        index = node.data('element')
      }

      control.elements[index] = new Control(node)
    })
  }
}

$(function () {

  $('.awebooking .searchbox, .awebooking-block .searchbox').each((i, el) => {
    new SearchForm(el, i)
  })

})
