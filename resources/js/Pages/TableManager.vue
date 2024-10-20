<template>
    <div class="container mx-auto py-6">
        <h1 class="text-2xl font-bold mb-6">Table Manager</h1>

        <!-- Select Table -->
        <label for="tableSelect" class="block mb-2">Select a Table:</label>
        <select v-model="selectedTable" @change="fetchTableData" class="p-2 border rounded mb-4">
            <option v-for="table in tables" :key="table" :value="table">
                {{ table }}
            </option>
        </select>

        <!-- Show Table Data -->
        <div v-if="tableData.length">
            <h2 class="text-xl font-semibold mt-6">Table Data ({{ selectedTable }})</h2>
            <table class="table-auto w-full mt-4 border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th v-for="(value, key) in tableData[0]" :key="key" class="border p-2">
                            {{ key }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in tableData" :key="row.id">
                        <td v-for="(value, key) in row" :key="key" class="border p-2">
                            {{ value }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Insert Data -->
        <div v-if="selectedTable" class="mt-8">
            <h2 class="text-xl font-semibold">Insert Data into {{ selectedTable }}</h2>
            <form @submit.prevent="insertData">
                <div class="mb-4" v-for="field in formFields" :key="field">
                    <label :for="field" class="block">{{ field }}:</label>
                    <input v-model="formData[field]" type="text" :id="field" class="p-2 border rounded w-full" />
                </div>
                <button type="submit" class="p-2 bg-blue-600 text-white rounded">
                    Insert Data
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch } from "vue";
import axios from "axios";

const props = defineProps({
    tables: Array,
});

const selectedTable = ref("");
const tableData = ref([]);
const formData = reactive({});
const formFields = ref([]);

const fetchTableData = async () => {
    if (selectedTable.value) {
        const response = await axios.get(`/table-manager/data/${selectedTable.value}`);
        tableData.value = response.data;

        if (response.data.length) {
            // Dynamically set form fields based on table data
            formFields.value = Object.keys(response.data[0]);
            formFields.value = formFields.value.filter(field => !['id', 'created_at', 'updated_at'].includes(field));
        }
    }
};

const insertData = async () => {
    await axios.post("/table-manager/insert", {
        table: selectedTable.value,
        ...formData,
    });
    fetchTableData(); // Refresh table data
};

watch(selectedTable, () => {
    formData.value = {};
});
</script>
