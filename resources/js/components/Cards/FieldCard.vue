<template>
  <div
      class="border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 rounded-md p-2 w-full flex flex-col justify-between gap-y-2 h-md">
    <div
        class="flex flex-row p-2 w-full justify-between items-center bg-gray-50/80 dark:bg-gray-600/10 rounded-md">
      <span class="truncate text-xs">{{ file.name }}</span>
    </div>
    <div class="aspect-w-1 aspect-h-1 block w-full overflow-hidden rounded-lg"
         :class="mode !== 'form' && 'cursor-pointer'"
         @click="setPreviewFile(file)">
      <div
          class="w-full h-full"
          v-if="file.type === 'image'"
      >
        <img
            :src="file.url"
            alt=""
            class="object-cover rounded-md w-full"
        />
      </div>
      <div v-else-if="file.type ==='video'" class="w-full h-full">
        <video class="w-full">
          <source :src="file.url"/>
          Sorry, your browser doesn't support embedded videos.
        </video>
      </div>
      <div
          v-else
          class="m-auto flex items-center justify-center bg-gray-200 dark:bg-gray-900 group-hover:opacity-75 text-gray-500"
      >
        <DocumentIcon class="h-16 w-16 text-gray-600"/>
      </div>
    </div>
    <div class="flex flex-row justify-between text-xs">
                    <span
                        class="inline-flex items-center text-xs p-1 rounded font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-400"
                    >{{ file.size }}
          </span
          >

      <button class="bg-red-400/20 dark:bg-red-800/30 rounded-md p-1 text-red-500" @click.prevent="remove(file)"
              v-if="mode==='form'">
        <TrashIcon class="w-3 h-3"/>
      </button>

      <Transition mode="out-in" name="fade" v-else>
        <button class="rounded-md p-1 transition-all bg-green-400/20 dark:bg-green-800/30 text-green-500"
                v-if="selected"
        >
          <CheckIcon class="w-4 h-4"/>
        </button>
        <button class="rounded-md p-1 transition-all bg-blue-400/20 dark:bg-blue-800/30 text-blue-500"
                @click.prevent="copy(file)"
                v-else
        >
          <ClipboardCopyIcon class="w-4 h-4"/>
        </button>

      </Transition>
    </div>

    <preview-modal :file="file" :without-actions="true" v-if="mode !== 'form'"/>

  </div>

</template>

<script>
import {CopiesToClipboard} from 'laravel-nova'
import PreviewModal from "@/components/Modals/PreviewModal";
import {CheckIcon, ClipboardCopyIcon, DocumentIcon, TrashIcon} from "@heroicons/vue/outline";
import {mapMutations} from "vuex";

export default {
  name: "FieldCard",
  mixins: [CopiesToClipboard],

  components: {DocumentIcon, ClipboardCopyIcon, CheckIcon, TrashIcon, PreviewModal},

  data() {
    return {
      selected: false,
    }
  },

  props: {
    file: {
      type: Object,
      required: true
    },
    mode: {
      type: String,
      required: true,
      default: () => 'detail',
    },
  },

  methods: {
    ...mapMutations('nova-file-manager', ['previewFile', 'deselectFile']),

    copy(file) {
      this.selected = true
      this.copyValueToClipboard(file.url)

      setTimeout(() => {
        this.selected = false
      }, 1000)
    },

    setPreviewFile(file) {
      if (this.mode === 'form') {
        return
      }

      this.previewFile(file)
    },

    remove(file) {
      this.deselectFile(file)
    },
  }
}
</script>

<style scoped>

</style>