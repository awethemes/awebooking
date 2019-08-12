import { render } from 'react-dom';
import Layout from './Layout';

(function() {
  const rootElement = document.getElementById('awebooking-root');

  if (!rootElement) {
    throw new Error('Cannot locate the #awebooking-root element.');
  }

  render(<Layout />, rootElement);
})();
