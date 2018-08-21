import $ from 'jquery'
import SearchForm from './search-form/index'

$(function () {
  $('.awebooking .searchbox, .awebooking-block .searchbox').each((i, el) => {
    new SearchForm(el)
  })
})
