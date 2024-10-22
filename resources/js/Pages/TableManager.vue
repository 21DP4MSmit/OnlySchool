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
                        <tr v-for="row in tableData" :key="row.id" class="hover:bg-gray-50">
                            <td v-for="(value, key) in row" :key="key" class="border p-4 text-sm text-gray-700">
                                <input
                                    v-if="editRow === row.id"
                                    v-model="editedRow[key]"
                                    type="text"
                                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                <span v-else>{{ key === 'password' ? value : value }}</span> <!-- Display password as plaintext -->
                            </td>
                            <td class="border p-4">
                                <button v-if="editRow !== row.id" @click="enableEditing(row)" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded-md">
                                    Edit
                                </button>
                                <button v-if="editRow === row.id" @click="updateRow(row.id)" class="bg-green-500 hover:bg-green-600 text-white py-1 px-3 rounded-md">
                                    OK
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- If no data in the table, show this message -->
            <div v-if="!tableData.length">
                <p class="mt-4 text-gray-500">No data available in this table.</p>
            </div>
        </div>

        <!-- Insert Data Form for Users Table -->
        <div v-if="selectedTable === 'users'" class="mt-12 max-w-2xl mx-auto">
            <h2 class="text-xl font-semibold mb-6 text-center">Insert User into {{ selectedTable }}</h2>
            <form @submit.prevent="insertUserData" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
                    <input v-model="userFormData.name" type="text" id="name" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                    <input v-model="userFormData.email" type="email" id="email" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                    <input v-model="userFormData.password" type="text" id="password" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" /> <!-- Show password as plaintext -->
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role:</label>
                    <select v-model="userFormData.role" id="role" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label for="phoneNumber" class="block text-sm font-medium text-gray-700">Phone Number:</label>
                    <input v-model="userFormData.phoneNumber" type="text" id="phoneNumber" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <span v-if="userFormData.phoneNumber.length > 8" class="text-red-500 text-sm">Phone number must be no more than 8 characters.</span> <!-- Phone number validation -->
                </div>
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700">Class ID:</label>
                    <input v-model="userFormData.class_id" type="number" id="class_id" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div class="text-center">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md shadow-sm" :disabled="userFormData.phoneNumber.length > 8">
                        Insert User
                    </button>
                </div>
            </form>
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
const userFormData = reactive({ name: "", email: "", password: "", role: "user", phoneNumber: "", class_id: null });
const formFields = ref([]);
const editRow = ref(null);  // For tracking which row is being edited
const editedRow = reactive({});  // Store the updated row values

const fetchTableData = async () => {
    if (selectedTable.value) {
        const response = await axios.get(`/table-manager/data/${selectedTable.value}`);
        tableData.value = response.data;

        if (response.data.length && selectedTable.value !== 'users') {
            formFields.value = Object.keys(response.data[0]);
            formFields.value = formFields.value.filter(field => !['id', 'created_at', 'updated_at'].includes(field));
        }
    }
};

const insertUserData = async () => {
    try {
        const response = await axios.post("/table-manager/insert-user", userFormData);
        fetchTableData();
        console.log(response.data);
    } catch (error) {
        console.error('Error inserting user:', error);
        alert('Failed to insert user: ' + (error.response?.data?.message || error.message));
    }
};

const insertData = async () => {
    try {
        const response = await axios.post("/table-manager/insert", {
            table: selectedTable.value,
            ...formData,
        });
        fetchTableData();
        console.log(response.data);
    } catch (error) {
        console.error('Error inserting data:', error);
        alert('Failed to insert data: ' + (error.response?.data?.message || error.message));
    }
};

const enableEditing = (row) => {
    editRow.value = row.id;
    Object.assign(editedRow, row);  // Populate the editedRow with the current row values
};

const updateRow = async (id) => {
    try {
        const response = await axios.post(`/table-manager/update/${selectedTable.value}/${id}`, editedRow);
        editRow.value = null;  // Stop editing
        fetchTableData();  // Reload the table data
        console.log(response.data);
    } catch (error) {
        console.error('Error updating data:', error);
        alert('Failed to update data: ' + (error.response?.data?.message || error.message));
    }
};

watch(selectedTable, () => {
    Object.keys(formData).forEach(key => delete formData[key]);
});
</script>
