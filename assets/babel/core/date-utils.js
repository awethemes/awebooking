const DateUtils = (function () {
  const pad = (number) => `0${number}`.slice(-2)
  const int = (bool) => (bool === true ? 1 : 0)
  const monthToStr = (monthNumber, shorthand, locale) => locale.months[shorthand ? 'shorthand' : 'longhand'][monthNumber]

  const tokenRegex = {
    D: "(\\w+)",
    F: "(\\w+)",
    G: "(\\d\\d|\\d)",
    H: "(\\d\\d|\\d)",
    J: "(\\d\\d|\\d)\\w+",
    K: "",
    M: "(\\w+)",
    S: "(\\d\\d|\\d)",
    U: "(.+)",
    W: "(\\d\\d|\\d)",
    Y: "(\\d{4})",
    Z: "(.+)",
    d: "(\\d\\d|\\d)",
    h: "(\\d\\d|\\d)",
    i: "(\\d\\d|\\d)",
    j: "(\\d\\d|\\d)",
    l: "(\\w+)",
    m: "(\\d\\d|\\d)",
    n: "(\\d\\d|\\d)",
    s: "(\\d\\d|\\d)",
    w: "(\\d\\d|\\d)",
    y: "(\\d{2})",
  };

  const revFormat = {
    D: () => undefined,

    F: function (date, monthName, locale) {
      date.setMonth(locale.months.longhand.indexOf(monthName));
    },

    G: (date, hour) => {
      date.setHours(parseFloat(hour));
    },

    H: (date, hour) => {
      date.setHours(parseFloat(hour));
    },

    J: (date, day) => {
      date.setDate(parseFloat(day));
    },

    K: (date, amPM, locale) => {
      date.setHours((date.getHours() % 12) + 12 * int(new RegExp(locale.amPM[1], 'i').test(amPM)))
    },

    M: function (date, shortMonth, locale) {
      date.setMonth(locale.months.shorthand.indexOf(shortMonth));
    },

    S: (date, seconds) => {
      date.setSeconds(parseFloat(seconds));
    },

    U: (_, unixSeconds) => new Date(parseFloat(unixSeconds) * 1000),

    W: function (date, weekNum) {
      const weekNumber = parseInt(weekNum);
      return new Date(date.getFullYear(), 0, 2 + (weekNumber - 1) * 7, 0, 0, 0, 0);
    },

    Y: (date, year) => {
      date.setFullYear(parseFloat(year));
    },

    Z: (_, ISODate) => new Date(ISODate),

    d: (date, day) => {
      date.setDate(parseFloat(day));
    },

    h: (date, hour) => {
      date.setHours(parseFloat(hour));
    },

    i: (date, minutes) => {
      date.setMinutes(parseFloat(minutes));
    },

    j: (date, day) => {
      date.setDate(parseFloat(day));
    },

    l: () => undefined,

    m: (date, month) => {
      date.setMonth(parseFloat(month) - 1);
    },

    n: (date, month) => {
      date.setMonth(parseFloat(month) - 1);
    },

    s: (date, seconds) => {
      date.setSeconds(parseFloat(seconds));
    },

    w: () => undefined,

    y: (date, year) => {
      date.setFullYear(2000 + parseFloat(year));
    },
  };

  const formats = {
    // Get the date in UTC
    Z: (date) => date.toISOString(),

    // Weekday name, short, e.g. Thu
    D: function (date, locale) {
      return locale.weekdays.shorthand[formats.w(date, locale)]
    },

    // Full month name e.g. January
    F: function (date, locale) {
      return monthToStr(formats.n(date, locale) - 1, false, locale)
    },

    // Padded hour 1-12
    G: function (date, locale) {
      return pad(formats.h(date, locale))
    },

    // Hours with leading zero e.g. 03
    H: (date) => pad(date.getHours()),

    // Day (1-30) with ordinal suffix e.g. 1st, 2nd
    J: function (date, locale) {
      return locale.ordinal !== undefined
        ? date.getDate() + locale.ordinal(date.getDate())
        : date.getDate()
    },

    // AM/PM
    K: (date, locale) => locale.amPM[int(date.getHours() > 11)],

    // Shorthand month e.g. Jan, Sep, Oct, etc
    M: function (date, locale) {
      return monthToStr(date.getMonth(), true, locale)
    },

    // Seconds 00-59
    S: (date) => pad(date.getSeconds()),

    // Unix timestamp
    U: (date) => date.getTime() / 1000,

    // ISO-8601 week number of year
    W: function (date) {
      return DateUtils.getWeek(date)
    },

    // Full year e.g. 2016
    Y: (date) => date.getFullYear(),

    // Day in month, padded (01-30)
    d: (date) => pad(date.getDate()),

    // Hour from 1-12 (am/pm)
    h: (date) => (date.getHours() % 12 ? date.getHours() % 12 : 12),

    // Minutes, padded with leading zero e.g. 09
    i: (date) => pad(date.getMinutes()),

    // Day in month (1-30)
    j: (date) => date.getDate(),

    // Weekday name, full, e.g. Thursday
    l: function (date, locale) {
      return locale.weekdays.longhand[date.getDay()]
    },

    // Padded month number (01-12)
    m: (date) => pad(date.getMonth() + 1),

    // The month number (1-12)
    n: (date) => date.getMonth() + 1,

    // Seconds 0-59
    s: (date) => date.getSeconds(),

    // Number of the day of the week
    w: (date) => date.getDay(),

    // Last two digits of year e.g. 16 for 2016
    y: (date) => String(date.getFullYear()).substring(2),
  };

  return {
    l10n: {
      amPM: ['AM', 'PM'],
      weekdays: {
        shorthand: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        longhand: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
      },
      months: {
        shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        longhand: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
      },
    },

    getWeek(givenDate) {
      const date = new Date(givenDate.getTime());
      date.setHours(0, 0, 0, 0);

      // Thursday in current week decides the year.
      date.setDate(date.getDate() + 3 - ((date.getDay() + 6) % 7));

      // January 4 is always in week 1.
      const week1 = new Date(date.getFullYear(), 0, 4);

      // Adjust to Thursday in week 1 and count number of weeks from date to week1.
      return (1 + Math.round(((date.getTime() - week1.getTime()) / 86400000 - 3 + ((week1.getDay() + 6) % 7)) / 7))
    },

    format (date, format, locale) {
      locale = locale || this.l10n

      return format
        .split('')
        .map((c, i, arr) => formats[c] && arr[i - 1] !== '\\' ? formats[c](date, locale) : c !== '\\' ? c : '')
        .join('')
    },

    parse(date, format, timeless, locale) {
      locale = locale || this.l10n

      if (date !== 0 && !date) {
        return undefined
      }

      let parsedDate
      const dateOrig = date

      if (date instanceof Date) {
        parsedDate = new Date(date.getTime())
      } else if (typeof date !== 'string' && date.toFixed !== undefined) {
        parsedDate = new Date(date)
      } else if (typeof date === 'string') {
        const datestr = String(date).trim()

        if (datestr === 'today') {
          parsedDate = new Date();
          timeless = true;
        } else if (/Z$/.test(datestr) || /GMT$/.test(datestr)) {
          parsedDate = new Date(date);
        } else {
          parsedDate = new Date(new Date().getFullYear(), 0, 1, 0, 0, 0, 0)

          let matched, ops = [];
          for (let i = 0, matchIndex = 0, regexStr = ''; i < format.length; i++) {
            const token = format[i]
            const isBackSlash = token === '\\'
            const escaped = format[i - 1] === '\\' || isBackSlash

            if (tokenRegex[token] && !escaped) {
              regexStr += tokenRegex[token]
              const match = new RegExp(regexStr).exec(date)

              if (match && (matched = true)) {
                ops[token !== 'Y' ? 'push' : 'unshift']({
                  fn: revFormat[token],
                  val: match[++matchIndex],
                })
              }
            } else if (!isBackSlash) {
              regexStr += '.' // don't really care
            }

            ops.forEach(({fn, val}) => (parsedDate = fn(parsedDate, val, locale) || parsedDate))
          }

          parsedDate = matched ? parsedDate : undefined;
        }
      }

      /* istanbul ignore next */
      if (!(parsedDate instanceof Date && !isNaN(parsedDate.getTime()))) {
        // config.errorHandler(new Error(`Invalid date provided: ${dateOrig}`))
        return undefined
      }

      if (timeless === true) {
        parsedDate.setHours(0, 0, 0, 0)
      }

      return parsedDate;
    }
  }
})()

export default DateUtils
