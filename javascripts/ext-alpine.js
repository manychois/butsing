document.addEventListener('alpine:init', () => {
  Alpine.directive('class', (el, { value, expression }, { evaluateLater, effect }) => {
    const evalIsOn = evaluateLater(expression);
    effect(() => {
      evalIsOn((isOn) => {
        if (isOn) {
          el.classList.add(value);
        } else {
          el.classList.remove(value);
        }
      })
    });
  });
});
