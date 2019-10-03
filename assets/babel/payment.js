import $ from 'jquery';
import Timer from 'easytimer';

function initTimerCountdown() {
  const element = document.getElementById('timer-countdown');
  if (!element) {
    return;
  }

  const timer = new Timer();

  let elementDisplay = element.querySelector('strong');
  if (!elementDisplay) {
    elementDisplay = element;
  }

  timer.start({
    countdown: true,
    precision: 'seconds',
    startValues: { seconds: parseInt(element.getAttribute('data-seconds'), 10) },
  });

  timer.addEventListener('secondsUpdated', function(e) {
    elementDisplay.innerHTML = timer.getTimeValues().toString();
  });

  timer.addEventListener('targetAchieved', function(e) {
    setTimeout(() => { window.location.reload(); }, 100);
  });
}

$(function() {
  initTimerCountdown();

  const root = $('#payment-methods');
  const input = root.find('input[type="radio"]')[0];

  setTimeout(() => {
    const event = $.Event('selected.awebooking.gateway', {
      relatedTarget: input,
    });

    root.trigger(event, input.value);
  }, 50);
});
