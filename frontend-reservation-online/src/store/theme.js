import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  // Récupérer la préférence stockée ou celle du système
  const savedTheme = localStorage.getItem('admin_theme')
  const prefersDark = typeof window !== 'undefined' && window.matchMedia('(prefers-color-scheme: dark)').matches
  const initialDark = savedTheme ? savedTheme === 'dark' : prefersDark

  const isDark = ref(initialDark)

  const applyTheme = (dark) => {
    isDark.value = dark
    if (typeof document !== 'undefined') {
      if (dark) {
        document.documentElement.classList.add('dark')
      } else {
        document.documentElement.classList.remove('dark')
      }
      localStorage.setItem('admin_theme', dark ? 'dark' : 'light')
    }
  }

  // Appliquer immédiatement au chargement
  if (typeof document !== 'undefined') {
    if (initialDark) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }

  const toggleTheme = () => {
    applyTheme(!isDark.value)
  }

  const initTheme = () => {
    const saved = localStorage.getItem('admin_theme')
    if (saved) {
      applyTheme(saved === 'dark')
    } else {
      applyTheme(prefersDark)
    }
  }

  return {
    isDark,
    toggleTheme,
    initTheme,
  }
})
