import useBrowserStore from '@/stores/browser';
import { defineStore } from 'pinia';
import Shepherd from 'shepherd.js';

const useTourStore = defineStore('nova-file-manager/tour', {
  state: () => ({
    tour: undefined as Shepherd.Tour | undefined,
  }),

  actions: {
    init() {
      const store = useBrowserStore();

      if (!store.tour) {
        return;
      }

      if (this.alreadyDismissed()) {
        return;
      }

      this.tour = new Shepherd.Tour({
        useModalOverlay: true,
        stepsContainer: document.getElementById('tour-container') ?? undefined,
      });

      const self = this;

      this.steps().forEach((step) => {
        if (!document.querySelector(`[data-tour='${step.key}']`)) {
          return;
        }

        const tourStep = self.tour?.addStep({
          id: step.key,
          text: `<div class="gap-2 flex flex-row items-center"><span class="mr-2 shrink-0 rounded-lg bg-indigo-900/60 p-2">💡</span>${step.label}</div>`,
          attachTo: {
            element: `[data-tour="${step.key}"]`,
            on: (step.position ?? 'bottom-start') as Shepherd.Step.PopperPlacement,
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
        });

        if (step.preloadConfetti) {
          tourStep?.on('before-show', () => this.loadConfetti());
        }
      });

      this.tour.on('complete', async () => {
        const canvas = await self.showConfetti();
        canvas.remove();
      });

      this.tour.start();
    },

    alreadyDismissed() {
      return !!window.localStorage.getItem('nova-file-manager/tour-dismissed');
    },

    dismiss() {
      window.localStorage.setItem('nova-file-manager/tour-dismissed', 'true');
    },

    loadConfetti() {
      const confettijs = document.createElement('script');

      confettijs.setAttribute('src', 'https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js');

      document.head.appendChild(confettijs);
    },

    async showConfetti() {
      return new Promise<HTMLCanvasElement & { confetti: any }>((resolve) => {
        const canvas = document.createElement('canvas') as HTMLCanvasElement & { confetti: any };
        canvas.id = 'confetti-canvas';
        canvas.className = 'absolute bottom-0 left-0 w-full h-full pointer-events-none';
        document.body.appendChild(canvas);

        canvas.confetti = canvas.confetti || window.confetti.create(canvas, { resize: true });

        canvas.confetti({
          particleCount: 250,
          spread: 150,
          origin: { y: 1 },
        });

        setTimeout(() => {
          resolve(canvas);
        }, 5000);
      });
    },

    steps() {
      const self = this;

      return [
        {
          key: 'nfm-disk-selector',
          label: 'You can use this to change the current storage disk',
          buttons: [
            {
              label: 'Skip tour',
              text: 'Skip tour',
              secondary: true,
              action: () => {
                self.dismiss();

                self.tour?.complete();
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
          preloadConfetti: true,
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
          label: 'Here are your folders in the current path, you can go inside, rename or delete them',
        },
        {
          key: '',
          label: 'Here are your files, a single click to select them, and double click to open a preview',
          position: 'bottom',
          scrollTo: false,
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
                self.tour?.complete();
                self.dismiss();
              },
            },
          ],
        },
      ] as ({
        key: string;
        label: string;
        position: Shepherd.Step.PopperPlacement;
        extraClasses?: string;
        preloadConfetti?: boolean;
      } & Shepherd.Step.StepOptions)[];
    },
  },
});

export default useTourStore;
