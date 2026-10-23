<template>
  <div ref="rootElement" class="relative">
    <button
      type="button"
      :disabled="disabled"
      :aria-expanded="isOpen"
      :aria-required="required"
      aria-haspopup="listbox"
      class="flex h-11 w-full items-center rounded-xl border border-slate-200/90 bg-slate-50/70 pl-10 pr-9 text-left text-sm font-medium text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:bg-white disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
      :class="isOpen ? toneClasses.trigger : ''"
      @click="togglePopover"
      @keydown.esc="closePopover"
    >
      <i :class="icon" class="pointer-events-none absolute left-3.5 text-xs text-slate-400"></i>
      <span :class="selectedOption ? 'text-slate-700' : 'text-slate-400'" class="block min-w-0 flex-1 truncate">
        {{ selectedOption?.name || placeholder }}
      </span>
      <i
        class="fa-solid fa-chevron-down pointer-events-none absolute right-3.5 text-[10px] text-slate-400 transition-transform duration-200"
        :class="isOpen ? ['rotate-180', toneClasses.accent] : ''"
      ></i>
    </button>

    <Transition name="select-popover">
      <div
        v-if="isOpen"
        class="absolute left-0 top-full z-[300] mt-1.5 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-xl"
      >
        <div v-if="searchable" class="relative mb-2">
          <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
          <input
            ref="searchInput"
            v-model="searchQuery"
            type="search"
            :placeholder="searchPlaceholder"
            class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 text-sm text-slate-700 outline-none transition focus:bg-white focus:ring-2"
            :class="toneClasses.search"
            @keydown.esc.prevent="closePopover"
          />
        </div>

        <div class="max-h-60 space-y-1 overflow-y-auto overscroll-contain pr-0.5" role="listbox">
          <button
            v-for="option in filteredOptions"
            :key="String(option.id)"
            type="button"
            role="option"
            :aria-selected="isSelected(option)"
            class="flex w-full items-center gap-2 rounded-xl px-3 py-2.5 text-left text-sm transition"
            :class="isSelected(option) ? toneClasses.selected : 'font-medium text-slate-700 hover:bg-slate-50'"
            @click="selectOption(option)"
          >
            <span class="min-w-0 flex-1 leading-snug">{{ option.name }}</span>
            <i v-if="isSelected(option)" class="fa-solid fa-check shrink-0 text-xs" :class="toneClasses.accent"></i>
          </button>

          <p v-if="filteredOptions.length === 0" class="px-3 py-5 text-center text-xs text-slate-500">
            Không tìm thấy lựa chọn phù hợp.
          </p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  options: {
    type: Array,
    default: () => [],
  },
  placeholder: {
    type: String,
    default: 'Chọn một tùy chọn',
  },
  searchPlaceholder: {
    type: String,
    default: 'Tìm kiếm...',
  },
  icon: {
    type: String,
    default: 'fa-solid fa-list',
  },
  searchable: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  required: {
    type: Boolean,
    default: false,
  },
  tone: {
    type: String,
    default: 'emerald',
    validator: (value) => ['emerald', 'blue', 'purple'].includes(value),
  },
})

const emit = defineEmits(['update:modelValue'])

const rootElement = ref(null)
const searchInput = ref(null)
const isOpen = ref(false)
const searchQuery = ref('')

const toneClasses = computed(() => {
  if (props.tone === 'blue') {
    return {
      trigger: 'border-blue-500 bg-white ring-2 ring-blue-100',
      accent: 'text-blue-600',
      search: 'focus:border-blue-500 focus:ring-blue-100',
      selected: 'bg-blue-50 font-bold text-blue-700',
    }
  }

  if (props.tone === 'purple') {
    return {
      trigger: 'border-purple-500 bg-white ring-2 ring-purple-100',
      accent: 'text-purple-600',
      search: 'focus:border-purple-500 focus:ring-purple-100',
      selected: 'bg-purple-50 font-bold text-purple-700',
    }
  }

  return {
    trigger: 'border-emerald-500 bg-white ring-2 ring-emerald-100',
    accent: 'text-emerald-600',
    search: 'focus:border-emerald-500 focus:ring-emerald-100',
    selected: 'bg-emerald-50 font-bold text-emerald-700',
  }
})

const selectedOption = computed(() => props.options.find((option) => isSelected(option)) || null)

const filteredOptions = computed(() => {
  const query = normalizeText(searchQuery.value)
  if (!query) return props.options
  return props.options.filter((option) => normalizeText(option?.name).includes(query))
})

function normalizeText(value) {
  return String(value || '')
    .toLocaleLowerCase('vi')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd')
}

function isSelected(option) {
  return String(option?.id) === String(props.modelValue)
}

async function togglePopover() {
  if (props.disabled) return
  isOpen.value = !isOpen.value
  if (isOpen.value && props.searchable) {
    await nextTick()
    searchInput.value?.focus()
  }
}

function closePopover() {
  isOpen.value = false
  searchQuery.value = ''
}

function selectOption(option) {
  emit('update:modelValue', String(option.id))
  closePopover()
}

function handleDocumentPointerDown(event) {
  if (isOpen.value && !rootElement.value?.contains(event.target)) closePopover()
}

watch(() => props.disabled, (disabled) => {
  if (disabled) closePopover()
})

onMounted(() => document.addEventListener('pointerdown', handleDocumentPointerDown))
onBeforeUnmount(() => document.removeEventListener('pointerdown', handleDocumentPointerDown))
</script>

<style scoped>
.select-popover-enter-active,
.select-popover-leave-active {
  transition: opacity 160ms ease, transform 160ms ease;
  transform-origin: top;
}

.select-popover-enter-from,
.select-popover-leave-to {
  opacity: 0;
  transform: translateY(-4px) scale(0.985);
}
</style>
