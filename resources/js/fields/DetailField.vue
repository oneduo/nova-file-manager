<template>
  <PanelItem :field="field" :index="index">
    <template v-if="field.value?.files" v-slot:value>
      <div class="nova-file-manager">
        <div :class="darkMode && 'dark'">
          <div
            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 mb-2"
          >
            <template v-for="file in field.value?.files" :key="file.id">
              <FieldCard :field="field" :file="file"/>
            </template>
          </div>
        </div>
      </div>
    </template>
  </PanelItem>
</template>

<script>
import { CopiesToClipboard } from 'laravel-nova'
import { mapMutations, mapState } from 'vuex'
import FieldCard from '@/components/Cards/FieldCard'

export default {
    mixins: [CopiesToClipboard],

    components: {
        FieldCard,
    },

    props: ['field', 'index'],

    computed: {
        ...mapState('nova-file-manager', ['darkMode']),
    },

    mounted() {
        this.detectDarkMode()
    },

    data: () => ({
        selected: null,
    }),

    methods: {
        ...mapMutations('nova-file-manager', [
            'init',
            'detectDarkMode',
            'previewFile',
        ]),

        copy(file) {
            this.selected = file
            this.copyValueToClipboard(file.url)

            setTimeout(() => {
                this.selected = null
            }, 1000)
        },

        openPreview(file) {
            this.previewFile(file)
        },
    },
}
</script>
<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
