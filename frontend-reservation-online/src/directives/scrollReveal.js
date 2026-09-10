/**
 * Directive v-scroll-reveal
 * ─────────────────────────────────────────────────────────────────────────────
 * Animation de révélation au scroll (one-time, via IntersectionObserver).
 *
 * Usage :
 *   <div v-scroll-reveal>...</div>
 *   <div v-scroll-reveal="{ delay: 200, direction: 'up' }">...</div>
 *
 * Options :
 *   delay     {number}  Délai en ms avant l'animation  (défaut : 0)
 *   direction {string}  'up' | 'down' | 'left' | 'right' | 'fade'  (défaut : 'up')
 *   distance  {string}  Distance de translation CSS  (défaut : '30px')
 *   duration  {number}  Durée de la transition en ms  (défaut : 600)
 *   threshold {number}  Pourcentage visible pour déclencher (0-1)  (défaut : 0.15)
 */

const DEFAULTS = {
  delay: 0,
  direction: 'up',
  distance: '30px',
  duration: 600,
  threshold: 0.15,
}

function getTranslate(direction, distance) {
  switch (direction) {
    case 'up':    return `translateY(${distance})`
    case 'down':  return `translateY(-${distance})`
    case 'left':  return `translateX(${distance})`
    case 'right': return `translateX(-${distance})`
    case 'fade':  return 'none'
    default:      return `translateY(${distance})`
  }
}

export const scrollReveal = {
  mounted(el, binding) {
    const opts = { ...DEFAULTS, ...(binding.value || {}) }

    // État initial — invisible + décalé
    Object.assign(el.style, {
      opacity: '0',
      transform: getTranslate(opts.direction, opts.distance),
      transition: `opacity ${opts.duration}ms cubic-bezier(0.25, 0.46, 0.45, 0.94) ${opts.delay}ms,
                   transform ${opts.duration}ms cubic-bezier(0.25, 0.46, 0.45, 0.94) ${opts.delay}ms`,
      willChange: 'opacity, transform',
    })

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            // Déclenche l'animation
            Object.assign(el.style, {
              opacity: '1',
              transform: 'none',
            })
            // One-time : on arrête d'observer après la première apparition
            observer.unobserve(el)
          }
        })
      },
      { threshold: opts.threshold }
    )

    observer.observe(el)

    // Stocke l'observer pour le cleanup
    el._scrollRevealObserver = observer
  },

  unmounted(el) {
    if (el._scrollRevealObserver) {
      el._scrollRevealObserver.disconnect()
      delete el._scrollRevealObserver
    }
  },
}
