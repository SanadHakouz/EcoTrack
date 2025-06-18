import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useCounterStore = defineStore('counter', () => {
  // State
  const count = ref(0);

  // Actions
  function increment() {
    count.value++;
  }

  function decrement() {
    count.value--;
  }

  function reset() {
    count.value = 0;
  }

  // Return state and actions
  return {
    count,
    increment,
    decrement,
    reset
  };
});
