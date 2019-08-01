import { DateTimePicker } from '@wordpress/components'
import { __experimentalGetSettings } from '@wordpress/date'
import { withState } from '@wordpress/compose'

const DatePicker = withState({
  date: new Date(),
})(({ date, setState }) => {
  const settings = __experimentalGetSettings()

  // To know if the current timezone is a 12 hour time with look for an "a" in the time format.
  // We also make sure this a is not escaped by a "/".
  const is12HourTime = /a(?!\\)/i.test(
    settings.formats.time
            .toLowerCase() // Test only the lower case a
            .replace(/\\\\/g, '') // Replace "//" with empty strings
            .split('').reverse().join('') // Reverse the string and test for "a" not followed by a slash
  )

  return (
    <DateTimePicker
      currentDate={date}
      onChange={(date) => setState({ date })}
      is12Hour={is12HourTime}
    />
  )
})

export default DatePicker
