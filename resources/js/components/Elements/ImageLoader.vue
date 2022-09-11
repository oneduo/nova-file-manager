<template>
  <div class="flex justify-center items-center h-full max-h-[80vh]" ref="card" :class="cardClasses">
    <Spinner v-if="loading" class="w-6 h-6" />
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Spinner from '@/components/Elements/Spinner'

const props = defineProps({
  src: {
    type: String,
    required: true,
  },
  fullWidth: {
    type: Boolean,
    default: true,
  },
  isThumbnail: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['missing'])

const loading = ref(true)
const missing = ref(false)

const card = ref(null)

const cardClasses = computed(() => {
  return {
    'w-full': props.fullWidth,
  }
})

onMounted(() => {
  new Promise((resolve, reject) => {
    let image = new Image()

    image.addEventListener('load', () => resolve(image))
    image.addEventListener('error', () => reject())

    image.src = props.src
  })
    .then(image => {
      image.className = 'pointer-events-none w-full h-full'
      image.classList.add(props.isThumbnail ? 'object-cover' : 'object-contain')
      image.draggable = false

      card.value.appendChild(image)
    })
    .catch(() => {
      missing.value = true

      emit('missing', true)
    })
    .finally(() => {
      loading.value = false
    })
})
</script>