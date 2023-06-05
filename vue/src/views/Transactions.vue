<template>
  <h1>Transactions</h1>
  <div class="table-responsive small">
    <DataTable
      :columns="transaction_columns"
      :data = "transaction_data"
      class="table table-sm"
      id="transactions-table">

    </DataTable>
  </div>
</template>

<style>
  @import 'datatables.net-dt';
</style>

<script setup>
  import { ref, onMounted } from 'vue';
  import DataTable from 'datatables.net-vue3';
  import DataTablesCore from 'datatables.net';

  DataTable.use(DataTablesCore);

  const transaction_data = ref([]);
  
  const transaction_columns = [
    { data: 'transaction_id', title: 'ID', },
    { data: 'date', title: 'Date', },
    { data: 'name', title: 'Name', },
    { data: 'amount', title: 'Amount', },
    { data: 'category', title: 'Categories', },
  ];

  onMounted(fetchTransactions);

  async function fetchTransactions() {
    const accessToken = 'access-sandbox-a646e96d-9e28-44d8-9bee-165104571db5';
    try {
      const response = await fetch('https://api.uptrendcredit.local/api/plaid/transactions?access_token='+accessToken);
      const data = await response.json();
      const transactions = data.transactions;
      transaction_data.value = transactions;
      
    } catch (error) {
      console.error('Error fetching transactions:', error);
    }
  }
</script>