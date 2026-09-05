<template>
  <Card class="p-6">
    <h3 class="mb-4 font-bold">Популярные страницы</h3>
    <p v-if="!rows.length" class="text-sm text-gray-500">Нет данных за выбранный период.</p>
    <table v-else class="w-full table-auto text-sm">
      <thead>
        <tr class="text-left text-gray-500">
          <th class="py-2">Страница</th>
          <th class="text-right">Просмотры</th>
          <th class="text-right">Посетители</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in rows" :key="row.path" class="border-t border-gray-200 dark:border-gray-700">
          <td class="max-w-md truncate py-2" :title="row.path">{{ row.path }}</td>
          <td class="text-right">{{ row.views }}</td>
          <td class="text-right">{{ row.visitors }}</td>
        </tr>
      </tbody>
    </table>
  </Card>
</template>

<script>
export default {
  props: {
    rows: { type: Array, required: true },
  },
}
</script>
