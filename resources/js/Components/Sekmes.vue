<template>
    <div class="flex flex-col md:flex-row p-4">
      <!-- Left Box -->
      <div class="flex-1 border border-gray-400 p-4 mr-4 rounded-lg shadow-md">
        <h2 class="text-lg font-bold">Sekmju izraksts:</h2>
        <table class="w-full mt-4 border border-collapse">
          <thead>
            <tr>
              <th class="border px-4 py-2">Stunda</th>
              <th class="border px-4 py-2">Mēnesis</th>
              <th class="border px-4 py-2">Atestācija</th>
              <th class="border px-4 py-2">N/v skaits</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="border px-4 py-2" colspan="4">
                Uzrādās atzīmes no d-bāzes
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Right Box -->
      <div class="flex-1 border border-gray-400 p-4 rounded-lg shadow-md">
        <h2 class="text-lg font-bold">Stipendijas Kalkulators:</h2>
        <table class="w-full mt-4 border border-collapse">
          <thead>
            <tr>
              <th class="border px-4 py-2">Stunda</th>
              <th class="border px-4 py-2">Projektētā atzīme</th>
              <th class="border px-4 py-2">Atzīmi izmantoja</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="border px-4 py-2" colspan="3">
                (Pats drīkst ievietot atzīmes aizvietojot projektēto)
              </td>
            </tr>
          </tbody>
        </table>
        <div class="mt-4">
          <p>(No datubāzes)</p>
          <p class="mt-2">Vidējā atzīme: Stipendijas diapazons:</p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  // Here you can import any necessary components or define props if needed
  </script>
  
  <style scoped>
  /* Add any additional styles here if needed */
  </style>
  