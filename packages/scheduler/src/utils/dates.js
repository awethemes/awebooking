const MONTHS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];

export function range(
  start,
  end,
  interval = 'days',
  options = {excludeEnd: false, step: 1},
) {
  const range = {start, end};

  return {
    [Symbol.iterator]() {
      const step = options.step || 1;
      const diff = Math.abs(range.start.diff(range.end, interval)) / step;

      let excludeEnd = options.excludeEnd || false;
      let iteration = 0;

      if (options.hasOwnProperty('exclusive')) {
        excludeEnd = options.exclusive;
      }

      return {
        next() {
          const current = range.start.clone().add((iteration * step), interval);

          const done = excludeEnd
            ? !(iteration < diff)
            : !(iteration <= diff);

          iteration++;

          return {
            done,
            value: (done ? undefined : current),
          };
        },
      };
    },
  };
}

export function today() {
  return dates.startOf(new Date(), 'day');
}
