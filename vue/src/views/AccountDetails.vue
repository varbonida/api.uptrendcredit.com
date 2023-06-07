<template>
  <PageBreadcrumb pageTitle="Account Details" />

  <div class="account-details">
    <h1>card</h1>
  </div>

  <div class="account-transactions">
    <h1>transaction history</h1>
    <div class="table-responsive small">
      <DataTable
        :columns="transaction_columns"
        :data = "transaction_data"
        class="table table-sm"
        id="transactions-table">

      </DataTable>
    </div>
  </div>
</template>

<style scoped>

</style>

<script setup>  
  import { ref, onMounted, defineProps } from 'vue';
  import PageBreadcrumb from '../components/_PageBreadcrumb.vue';
  import moment from 'moment';
  import DataTable from 'datatables.net-vue3';
  import DataTablesCore from 'datatables.net';

  DataTable.use(DataTablesCore);

  const api_base_url = import.meta.env.VITE_API_BASE_URL;
  const props = defineProps({
    bank_name: {
      type: String,
      required: true,
    },
    account_id: {
      type: String,
      required: true,
    }
  });
  let owners = ref([]);
  let linkToken = ref(null);
  const transaction_data = ref([]);

  const transaction_columns = [
    { data: 'date', title: 'Date', },
    { data: 'name', title: 'Name', },
    { data: 'amount', title: 'Amount', },
    { data: 'category', title: 'Categories', },
  ];

  onMounted(checkConnected);

  function checkConnected() {
    fetchConnectedBank();
  }

  function loadMonthlyTransactions() {
    console.log('monthly transactions');
  }

  function fetchConnectedBank() {
    console.log('ddd')
    const bank_detail = fetch(`${api_base_url}/api/up/get-connected-account`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ 
        account_id: props.account_id,
        bank_name: props.bank_name,
      })
    })
    .then(response => response.json())
    .then(data => {
      return data;
    })
    .catch(error => {
      console.error('Error:', error);
    });

    console.log('data: ', bank_detail);
  }
</script>