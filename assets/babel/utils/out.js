/*global document */

'use strict';

module.exports = (elements, eventHandler) => {

    // Handle the click and fire the event handler if needed
    const handleClick = (e) => {

        for (let target = e.target; target; target = target.parentNode) {
            let found = elements.indexOf(target) > -1;

            if (found) {
                return;
            }
        }

        eventHandler(e);
    };

    // Remove the event listeners
    const off = () => {
        document.removeEventListener('mousedown', handleClick);
        document.removeEventListener('touchstart', handleClick);
    };

    // If just one element is passed in instead of an array, convert it
    if (!elements.indexOf) {
        elements = [elements];
    }

    document.addEventListener('mousedown', handleClick);
    document.addEventListener('touchstart', handleClick);

    return {
        off
    };

};
