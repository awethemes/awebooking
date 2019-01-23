import $ from 'jquery'
import SearchForm from './search-form/SearchForm'
import OldSearchForm from './search-form/old'

$(function () {
  $('.awebooking .searchbox, .awebooking-block .searchbox').each((index, element) => {
    const form = new SearchForm(element, index)

    if (!element.classList.contains('searchbox--experiment-style')) {
      new OldSearchForm(element, form)
    }
  })
})
