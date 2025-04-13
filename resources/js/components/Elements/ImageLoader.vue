<script setup lang="ts">
import { onMounted, ref } from 'vue';
import Spinner from './Spinner.vue';

interface Props {
  src: string;
  alt: string;
  isThumbnail: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  isThumbnail: true,
});

const emit = defineEmits(['missing']);

const loading = ref<boolean>(true);
const missing = ref<boolean>(false);

const card = ref<HTMLDivElement>();

onMounted(() => {
  new Promise<HTMLImageElement>((resolve, reject) => {
    let image = new Image();

    image.addEventListener('load', () => resolve(image));
    image.addEventListener('error', (event) => reject(event));

    image.src = props.src;
  })
    .then((image) => {
      image.className = 'pointer-events-none w-full h-full';
      image.classList.add(props.isThumbnail ? 'object-cover' : 'object-contain');
      image.draggable = false;

      card.value?.appendChild(image);
    })
    .catch(() => {
      missing.value = true;

      emit('missing', true);
    })
    .finally(() => {
      loading.value = false;
    });
});
</script>

<template>
  <div class="flex justify-center items-center h-full max-h-[80vh]" ref="card">
    <Spinner v-if="loading" class="w-6 h-6" />
  </div>
</template>
