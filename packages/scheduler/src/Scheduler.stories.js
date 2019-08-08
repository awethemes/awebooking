import React from 'react';
import { storiesOf } from '@storybook/react';
import Schedule from './Schedule';

storiesOf('Scheduler', module).add('Default', () => (
  <div>
    <Schedule startDate={'2019-08-08'} />
  </div>
));
