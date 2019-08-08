import { configure, addDecorator } from '@storybook/react';
import { withInfo } from '@storybook/addon-info';

import '@wordpress/components/build-style/style.css';
import '../components/src/styles.scss';

// automatically import all files ending in *.stories.js
const req = require.context('../components', true, /\.stories\.js$/);
const req2 = require.context('../scheduler', true, /\.stories\.js$/);

function loadStories() {
  req.keys().forEach(filename => req(filename));
  req2.keys().forEach(filename => req2(filename));
}

addDecorator(withInfo);
configure(loadStories, module);
