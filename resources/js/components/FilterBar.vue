<template>
  <Card class="mb-6 p-6">
    <button
      type="button"
      class="flex w-full items-center justify-between text-left focus:outline-none"
      :class="{ 'mb-4': !collapsed }"
      @click="collapsed = !collapsed"
    >
      <h3 class="font-bold">
        Фильтры
        <span v-if="activeCount" class="ml-1 font-normal text-gray-500">({{ activeCount }})</span>
      </h3>
      <IconArrow class="transition-transform" :class="{ 'rotate-180': !collapsed }" />
    </button>

    <div v-show="!collapsed" class="grid gap-4 md:grid-cols-4 lg:grid-cols-8">
      <div>
        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500">Период</label>
        <SelectControl class="w-full block" :options="periodOptions" :selected="filters.period" @change="update('period', $event)" />
      </div>

      <div>
        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500">UTM Source</label>
        <SelectControl class="w-full block" :options="withAll(options.utm_sources)" :selected="filters.utm_source" @change="update('utm_source', $event)" />
      </div>

      <div>
        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500">UTM Medium</label>
        <SelectControl class="w-full block" :options="withAll(options.utm_mediums)" :selected="filters.utm_medium" @change="update('utm_medium', $event)" />
      </div>

      <div>
        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500">UTM Campaign</label>
        <SelectControl class="w-full block" :options="withAll(options.utm_campaigns)" :selected="filters.utm_campaign" @change="update('utm_campaign', $event)" />
      </div>

      <div>
        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500">Устройство</label>
        <SelectControl class="w-full block" :options="withAll(options.device_types)" :selected="filters.device_type" @change="update('device_type', $event)" />
      </div>

      <div>
        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500">Браузер</label>
        <SelectControl class="w-full block" :options="withAll(options.browsers)" :selected="filters.browser" @change="update('browser', $event)" />
      </div>

      <div>
        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500">ОС</label>
        <SelectControl class="w-full block" :options="withAll(options.os)" :selected="filters.os" @change="update('os', $event)" />
      </div>

      <div class="flex items-end">
        <CheckboxWithLabel :checked="filters.include_bots" @input="update('include_bots', $event.target.checked)">
          Включая ботов
        </CheckboxWithLabel>
      </div>
    </div>
  </Card>
</template>

<script>
export default {
  props: {
    filters: { type: Object, required: true },
    options: { type: Object, required: true },
  },

  emits: ['change'],

  data() {
    return { collapsed: true }
  },

  computed: {
    // options.periods arrives as a { "Сегодня": "today", ... } label->value map
    // (see AnalyticsFilters::periodOptions on the backend); SelectControl wants
    // an array of { value, label }.
    periodOptions() {
      return Object.entries(this.options.periods || {}).map(([label, value]) => ({ value, label }))
    },

    // Shown next to the "Фильтры" header so a collapsed panel still hints at what's
    // active. The period is excluded — it's always set to something, never "off".
    activeCount() {
      return ['utm_source', 'utm_medium', 'utm_campaign', 'device_type', 'browser', 'os']
        .filter((key) => this.filters[key])
        .length + (this.filters.include_bots ? 1 : 0)
    },
  },

  methods: {
    // The dimension endpoints return plain distinct-value arrays (e.g. ["yandex", "google"]);
    // prepend an empty "Все" (All) option so the filter can be cleared.
    withAll(values) {
      return [{ value: '', label: 'Все' }, ...(values || []).map((value) => ({ value, label: value }))]
    },

    update(key, value) {
      this.$emit('change', { key, value })
    },
  },
}
</script>
