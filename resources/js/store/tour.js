import { defineStore } from 'pinia'
import Shepherd from 'shepherd.js'
import { useStore } from '@/store/index'

const useTourStore = defineStore('nova-file-manager/tour', {
  state: () => ({
    tour: undefined,
  }),

  actions: {
    init() {
      const store = useStore()

      if (!store.tour) {
        return
      }

      if (this.alreadyDismissed()) {
        return
      }

      this.tour = new Shepherd.Tour({
        useModalOverlay: true,
        stepsContainer: document.getElementById('tour-container'),
      })

      const self = this

      this.steps().map(step => {
        const _step = self.tour.addStep({
          id: step.key,
          text: `<div class="gap-2 flex flex-row items-center"><span class="mr-2 flex-shrink-0 rounded-lg bg-indigo-900/60 p-2">💡</span>${step.label}</div>`,
          attachTo: {
            element: `[data-tour="${step.key}"]`,
            on: step.position ?? 'bottom-start',
          },
          arrow: false,
          scrollTo: step.scrollTo ?? true,
          classes: step.extraClasses,
          buttons: step.buttons ?? [
            {
              text: 'Previous',
              secondary: true,
              action: self.tour.back,
            },
            {
              text: 'Next',
              action: self.tour.next,
            },
          ],
        })

        if (step.preloadConfetti) {
          _step.on('before-show', () => this.loadConfetti())
        }
      })

      this.tour.start()
    },

    alreadyDismissed() {
      return !!window.localStorage.getItem('nova-file-manager/tour-dismissed')
    },

    dismiss() {
      window.localStorage.setItem('nova-file-manager/tour-dismissed', true)
    },

    loadConfetti() {
      const confettijs = document.createElement('script')

      confettijs.setAttribute(
        'src',
        'https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js'
      )

      document.head.appendChild(confettijs)
    },

    steps() {
      const self = this

      return [
        {
          key: 'nfm-tool-title',
          label: 'Thank you for using Nova File Manager, let us take a look around !',
          position: 'bottom',
          buttons: [
            {
              label: 'Skip tour',
              text: 'Skip tour',
              secondary: true,
              action: () => {
                self.dismiss()

                self.tour?.complete()
              },
            },
            {
              label: 'Next',
              text: 'Next',
              action: self.tour?.next,
            },
          ],
        },
        {
          key: 'nfm-disk-selector',
          label: 'You can use this to change the current storage disk',
          preloadConfetti: true,
        },
        {
          key: 'nfm-pagination-selector',
          label: 'Use this to change the number of files shown per page',
        },
        {
          key: 'nfm-view-toggle',
          label: 'This allows you to toggle between grid and list views',
        },
        {
          key: 'nfm-spotlight-search-button',
          label: 'This opens a spotlight search modal, you can use ⌘+k',
        },
        {
          key: 'nfm-create-folder-button',
          label: 'Opens a new modal to create a new folder',
        },
        {
          key: 'nfm-upload-file-button',
          label: 'Open a new modal through which you can upload new files and even entire folders',
        },
        {
          key: 'nfm-breadcrumbs',
          label: 'Breadcrumbs to allow for quick access to parent folders, each item is clickable',
        },
        {
          key: 'nfm-directory-grid',
          label:
            'Here are your folders in the current path, you can go inside, rename or delete them',
        },
        {
          key: 'nfm-file-grid',
          label:
            'Here are your files, a single click to select them, and double click to open a preview',
          position: 'bottom',
          buttons: [
            {
              label: 'Previous',
              text: 'Previous',
              secondary: true,
              action: self.tour?.back,
            },
            {
              label: 'Finish',
              text: '🎉 Done',
              action: () => {
                new Promise(() => {
                  const canvas = document.createElement('canvas')
                  canvas.id = 'confetti-canvas'
                  canvas.className = 'absolute bottom-0 left-0 w-full h-full pointer-events-none'
                  document.body.appendChild(canvas)

                  canvas.confetti =
                    canvas.confetti || window.confetti.create(canvas, { resize: true })

                  canvas.confetti({
                    particleCount: 250,
                    spread: 150,
                    origin: { y: 1 },
                  })
                }).then(() => document.getElementById('confetti-canvas').remove())

                self.tour?.complete()

                self.dismiss()
              },
            },
          ],
        },
      ]
    },
  },
})

export { useTourStore }
