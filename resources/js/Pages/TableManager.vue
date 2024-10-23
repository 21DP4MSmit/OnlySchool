<template>
    <div class="container mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold text-center mb-6">Table Manager</h1>

        <!-- Select Table -->
        <div class="max-w-xl mx-auto mb-8">
            <label for="tableSelect" class="block text-lg font-medium text-gray-700 mb-2">Select a Table:</label>
            <select v-model="selectedTable" @change="fetchTableData" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
                <option value="" disabled>Select a table</option>
                <option v-for="table in tables" :key="table" :value="table">
                    {{ table }}
                </option>
            </select>
        </div>

        <!-- Show Table Data -->
        <div v-if="selectedTable" class="mt-8">
            <h2 class="text-2xl font-semibold mb-6 text-center">Table Data: {{ selectedTable }}</h2>

            <!-- Table Container with Scroll -->
            <div v-if="tableData.length" class="overflow-x-auto relative">
                <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th v-for="(value, key) in tableData[0]" :key="key" class="border p-4 text-left text-sm font-medium text-gray-600">
                                {{ key }}
                            </th>
                            <th class="border p-4 text-left text-sm font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in tableData" :key="row[getPrimaryKey()]" class="hover:bg-gray-50">
                            <td v-for="(value, key) in row" :key="key" class="border p-4 text-sm text-gray-700">
                                <input
                                    v-if="editRow === row[getPrimaryKey()]"
                                    v-model="editedRow[key]"
                                    type="text"
                                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                <span v-else>{{ value }}</span>
                            </td>
                            <td class="border p-4">
                                <button v-if="editRow !== row[getPrimaryKey()]" @click="enableEditing(row)" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded-md">
                                    Edit
                                </button>
                                <button v-if="editRow === row[getPrimaryKey()]" @click="updateRow(row[getPrimaryKey()])" class="bg-green-500 hover:bg-green-600 text-white py-1 px-3 rounded-md">
                                    OK
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Insert Data Form for Other Tables -->
            <div v-if="selectedTable !== 'users'" class="mt-12 max-w-2xl mx-auto">
                <h2 class="text-xl font-semibold mb-6 text-center">Insert Data into {{ selectedTable }}</h2>
                <form @submit.prevent="insertData" class="space-y-4">
                    <div v-for="field in formFields" :key="field">
                        <label :for="field" class="block text-sm font-medium text-gray-700">{{ field }}:</label>
                        <input v-model="formData[field]" type="text" :id="field" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="text-center">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md shadow-sm">
                            Insert Data
                        </button>
                    </div>
                </form>
            </div>
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
const editRow = ref(null);  // For tracking which row is being edited
const editedRow = reactive({});  // Store the updated row values

const fetchTableData = async () => {
    if (selectedTable.value) {
        const response = await axios.get(`/table-manager/data/${selectedTable.value}`);
        tableData.value = response.data;

        if (response.data.length) {
            formFields.value = Object.keys(response.data[0]);
            formFields.value = formFields.value.filter(field => !['created_at', 'updated_at'].includes(field));
        }
    }
};

// Insert data into the selected table
const insertData = async () => {
    try {
        await axios.post("/table-manager/insert", {
            table: selectedTable.value,
            ...formData,
        });
        fetchTableData();  // Refresh table data after insertion
        Object.keys(formData).forEach(key => formData[key] = '');  // Clear form data
    } catch (error) {
        console.error('Error inserting data:', error);
        alert('Failed to insert data: ' + (error.response?.data?.message || error.message));
    }
};

// Enable editing for a row
const enableEditing = (row) => {
    editRow.value = row[getPrimaryKey()];
    Object.assign(editedRow, row);  // Populate the editedRow with the current row values
};

// Update a row based on the primary key
const updateRow = async (id) => {
    try {
        await axios.post(`/table-manager/update/${selectedTable.value}/${id}`, editedRow);
        editRow.value = null;  // Stop editing
        fetchTableData();  // Reload the table data
    } catch (error) {
        console.error('Error updating data:', error);
        alert('Failed to update data: ' + (error.response?.data?.message || error.message));
    }
};

// Helper method to get the primary key dynamically
const getPrimaryKey = () => {
    const primaryKeys = {
        absences: 'AbsenceID',
        marks: 'MarkID',
        classrooms: 'id',
        users: 'id',
        subjects: 'SubjectID',
        klase:'ClassID',
        subject_lists:'ListID',
    };
    return primaryKeys[selectedTable.value] || 'id'; // Default to 'id' if not specified
};

watch(selectedTable, () => {
    // Clear the current editing state and reset form data when the table is changed
    editRow.value = null;
    Object.keys(editedRow).forEach(key => delete editedRow[key]);
    formFields.value = [];
    tableData.value = [];
    fetchTableData();
});
</script>
