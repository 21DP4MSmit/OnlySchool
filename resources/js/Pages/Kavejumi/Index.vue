<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-4">Jūsu kavējumi</h1>
            <table class="min-w-full bg-white border-collapse">
                <thead>
                    <tr>
                        <th class="px-6 py-2 border-b">Datums</th>
                        <th class="px-6 py-2 border-b">Priekšmets</th>
                        <th class="px-6 py-2 border-b">Klase</th>
                        <th class="px-6 py-2 border-b">Kavējums</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="absence in absences" :key="absence.id">
                        <td class="px-6 py-2 border-b">{{ absence.date }}</td>
                        <td class="px-6 py-2 border-b">{{ absence.subject?.Name || 'Nav pieejams' }}</td>
                        <td class="px-6 py-2 border-b">{{ absence.classroom?.Classroom || 'Nav pieejams' }}</td>
                        <td class="px-6 py-2 border-b">
                            <span v-if="absence.Absence === 1" class="text-green-500">✔</span>
                            <span v-if="absence.Absence === 2" class="text-red-500">✘</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    props: {
        absences: Array
    },
    components: {
        AuthenticatedLayout
    }
};
</script>
