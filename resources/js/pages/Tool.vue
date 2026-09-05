<template>
  <div>
    <Head title="Аналитика" />

    <Heading class="mb-6">Аналитика</Heading>

    <FilterBar :filters="filters" :options="filterOptions" @change="onFilterChange" />

    <Card v-if="loading" class="p-6">
      <p class="text-gray-500">Загрузка…</p>
    </Card>

    <template v-else>
      <div class="mb-6">
        <TrendChart :series="trend" />
      </div>

      <div class="mb-6">
        <TopPagesTable :rows="topPages" />
      </div>

      <div class="mb-6 grid gap-6 md:grid-cols-3">
        <BreakdownChart title="Источники (UTM Source)" :rows="sources.by_source || []" />
        <BreakdownChart title="Каналы (UTM Medium)" :rows="sources.by_medium || []" />
        <BreakdownChart title="Кампании (UTM Campaign)" :rows="sources.by_campaign || []" />
      </div>

      <div class="grid gap-6 md:grid-cols-3">
        <BreakdownChart title="Тип устройства" :rows="devices.by_device_type || []" />
        <BreakdownChart title="Браузер" :rows="devices.by_browser || []" />
        <BreakdownChart title="ОС" :rows="devices.by_os || []" />
      </div>
    </template>
  </div>
</template>

<script>
import FilterBar from '../components/FilterBar'
import TrendChart from '../components/TrendChart'
import TopPagesTable from '../components/TopPagesTable'
import BreakdownChart from '../components/BreakdownChart'

export default {
  components: { FilterBar, TrendChart, TopPagesTable, BreakdownChart },

  data() {
    return {
      loading: true,
      filterOptions: {},
      filters: {
        period: 'last_30_days',
        utm_source: '',
        utm_medium: '',
        utm_campaign: '',
        device_type: '',
        browser: '',
        os: '',
        include_bots: false,
      },
      trend: [],
      topPages: [],
      sources: {},
      devices: {},
    }
  },

  async mounted() {
    await this.loadFilterOptions()
    await this.loadAll()
    this.loading = false
  },

  methods: {
    url(path) {
      return `/nova-vendor/nova-analytics${path}`
    },

    // Only the date range affects the distinct-value lists the filter bar draws its
    // dropdowns from (see FilterOptionsQuery) — the other dimension filters don't need
    // to trigger a refetch of the options themselves, only of the four datasets.
    params() {
      return {
        period: this.filters.period,
        utm_source: this.filters.utm_source || undefined,
        utm_medium: this.filters.utm_medium || undefined,
        utm_campaign: this.filters.utm_campaign || undefined,
        device_type: this.filters.device_type || undefined,
        browser: this.filters.browser || undefined,
        os: this.filters.os || undefined,
        include_bots: this.filters.include_bots ? 1 : 0,
      }
    },

    async onFilterChange({ key, value }) {
      this.filters = { ...this.filters, [key]: value }

      if (key === 'period') {
        await this.loadFilterOptions()
      }

      await this.loadAll()
    },

    async loadFilterOptions() {
      const { data } = await Nova.request().get(this.url('/filter-options'), { params: this.params() })
      this.filterOptions = data
    },

    async loadAll() {
      const [trend, topPages, sources, devices] = await Promise.all([
        Nova.request().get(this.url('/trend'), { params: this.params() }),
        Nova.request().get(this.url('/top-pages'), { params: this.params() }),
        Nova.request().get(this.url('/traffic-sources'), { params: this.params() }),
        Nova.request().get(this.url('/devices'), { params: this.params() }),
      ])

      this.trend = trend.data.series
      this.topPages = topPages.data.rows
      this.sources = sources.data
      this.devices = devices.data
    },
  },
}
</script>
