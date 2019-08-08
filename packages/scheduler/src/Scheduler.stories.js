import React from 'react';
import { storiesOf } from '@storybook/react';
import Schedule from './Schedule';

storiesOf('Scheduler', module).add('Default', () => (
  <Schedule startDate={'2019-08-08'} />
));
