import { date } from '@wordpress/date';

export function formatDateString(dateString, format) {
  const { i18n } = window.awebooking || {};

  if (!dateString) {
    return '';
  }

  return date(format || i18n.dateFormat, dateString);
}
