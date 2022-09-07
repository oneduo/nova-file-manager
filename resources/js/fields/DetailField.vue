<template>
  <PanelItem :field="field" :index="index">
    <template v-if="field.value" v-slot:value>
      <div class="nova-file-manager">
        <div :class="darkMode && 'dark'">
          <ul class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-2 w-full" role="group">
            <template v-for="file in field.value" :key="file.id">
              <FieldCard
                :field="field"
                :file="mapEntity(file)"
                :attribute="field.attribute"
                :detail="true"
                :on-copy="copy"
              />
              <PreviewModal
                :file="mapEntity(file)"
                v-if="!!preview && preview?.id === mapEntity(file)?.id"
                :read-only="true"
              />
            </template>
          </ul>
        </div>
      </div>
    </template>
  </PanelItem>
</template>

<script>
import {CopiesToClipboard} from 'laravel-nova'
import {mapMutations, mapState} from 'vuex'
import FieldCard from '@/components/Cards/FieldCard'
import Entity from '@/types/Entity'
import PreviewModal from '@/components/Modals/PreviewModal'

export default {
  mixins: [CopiesToClipboard],

  components: {
    PreviewModal,
    FieldCard,
  },

  props: ['field', 'index'],

  computed: {
    ...mapState('nova-file-manager', ['darkMode', 'preview']),
  },

  mounted() {
    this.detectDarkMode()
  },

  data: () => ({
    selected: null,
  }),

  methods: {
    ...mapMutations('nova-file-manager', ['init', 'detectDarkMode', 'previewFile']),

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

    mapEntity: file =>
      new Entity(
        file.id,
        file.name,
        file.path,
        file.size,
        file.extension,
        file.mime,
        file.url,
        file.lastModifiedAt,
        file.type,
        file.exists,
        file.disk,
      ),
  },
}
</script>
