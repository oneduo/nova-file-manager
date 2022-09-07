<template>
  <div class="flex justify-center items-center h-full max-h-[80vh]" ref="card" :class="cardClasses">
    <Spinner v-if="loading" class="w-6 h-6" />
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Spinner from '@/components/Elements/Spinner'

const props = defineProps({
    src: String,

    fullWidth: {
        type: Boolean,
        default: true,
    },

    isThumbnail: {
        type: Boolean,
        default: true,
    },

    rounded: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['missing'])

const loading = ref(true)
const missing = ref(false)

const card = ref(null)

const cardClasses = computed(() => {
    return {
        'rounded-full': props.rounded,
        'w-full': props.fullWidth,
    }
})

onMounted(() => {
    console.log('onMounted')
    new Promise((resolve, reject) => {
        let image = new Image()

        image.addEventListener('load', () => resolve(image))
        image.addEventListener('error', () => reject())

        image.src = props.src
    })
        .then(image => {
            console.log('then', image)
            image.className = 'pointer-events-none w-full h-full'
            if (!props.isThumbnail) {
                image.classList.add('object-contain')
            }
            image.draggable = false

            card.value.appendChild(image)
        })
        .catch(e => {
            console.log('catch', e)
            missing.value = true

            emit('missing', true)
        })
        .finally(() => {
            console.log('finally')
            loading.value = false
        })
})
</script>

<style scoped>
.card {
  padding: 0 !important;
}
</style>
