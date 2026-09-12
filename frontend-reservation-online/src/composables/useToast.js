import { reactive } from "vue";

const toasts = reactive([]);
let idCounter = 0;

export function useToast() {
  function push(message, type = "success") {
    const id = ++idCounter;
    toasts.push({ id, message, type });
    setTimeout(() => {
      const index = toasts.findIndex((t) => t.id === id);
      if (index !== -1) toasts.splice(index, 1);
    }, 3000);
  }

  return {
    toasts,
    success: (message) => push(message, "success"),
    error: (message) => push(message, "error"),
  };
}
