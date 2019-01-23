export function formatDateString(dateString, format) {
  const { i18n, utils } = window.awebooking || {}

  const date = utils.dates.parse(dateString, 'Y-m-d')

  if (!date) {
    return ''
  }

  return utils.dates.format(date, format || i18n.dateFormat)
}
