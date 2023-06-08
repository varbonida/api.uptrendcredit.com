<template>
  <PageBreadcrumb pageTitle="Account Details" />

  <div class="page-loader d-flex justify-content-center bg-opacity-25 bg-dark align-items-center" v-if="is_loading">
    <div class="spinner-grow text-light" role="status" v-for="index in 6">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
  <div class="account-details mb-5">
    <div v-if="details_loaded" class="row g-2">
      <div class="col-sm-6 col-md-8 p-3">
        
      </div>
      <div class="col-sm-6 col-md-4 px-3">
        <div class="card no-border-radius mt border-0">
          <div class="card-body ps-0">
            <h5 class="card-title">{{ owners[0].names.join(' ') }}</h5>
          </div>
        </div>
        <div class="card no-border-radius mt-3">
          <div class="card-body">
            <h5 class="card-title">Emails</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item ps-0" v-for="email in owners[0].emails">
                <strong v-if="email.primary">{{ email.data }}</strong>
                <span v-else>{{ email.data }}</span>
              </li>
            </ul>
          </div>
        </div>
        <div class="card no-border-radius mt-3">
          <div class="card-body">
            <h5 class="card-title">Phone Numbers</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item ps-0" v-for="phone in owners[0].phone_numbers">
                <strong v-if="phone.primary">{{ phone.data }}<small>({{phone.type }})</small></strong>
                <span v-else>{{ phone.data }}</span><small>({{phone.type }})</small>
              </li>
            </ul>
          </div>
        </div>
        <div class="card no-border-radius mt-3">
          <div class="card-body">
            <h5 class="card-title">Addresses</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item ps-0" v-for="address in owners[0].addresses">
                <strong v-if="address.primary">
                  <div>{{ address.data.street }}</div>
                  <div>{{ address.data.city }}</div>
                  <div>{{ address.data.region }}</div>
                  <div>{{ address.data.country }} {{ address.data.postal_code }}</div>
                </strong>
                <span v-else>
                  <div>{{ address.data.street }}</div>
                  <div>{{ address.data.city }}</div>
                  <div>{{ address.data.region }}</div>
                  <div>{{ address.data.country }} {{ address.data.postal_code }}</div>
                </span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="account-transactions mt-3">
    <div v-if="transactions_loaded">
      <h1>Transaction History</h1>
      <DataTable
        :columns="transaction_columns"
        :data = "transaction_data"
        class="table table-hover table-striped"
        width="100%"
        :options="dtt_options"
        id="transactions-table">
      </DataTable>
    </div>
  </div>
</template>

<style scoped>
</style>

<script setup>  
  import { ref, onMounted } from 'vue';
  import PageBreadcrumb from '../components/_PageBreadcrumb.vue';
  import moment from 'moment';
  import DataTable from 'datatables.net-vue3';
  import DataTablesCore from 'datatables.net-bs5';
  import 'datatables.net-select-bs5';
  import 'datatables.net-responsive-bs5';

  DataTable.use(DataTablesCore);

  const api_base_url = import.meta.env.VITE_API_BASE_URL;
  const props = defineProps({
    bank_id: {
      type: String,
      required: true,
    },
    account_id: {
      type: String,
      required: true,
    }
  });
  let is_loading = ref(true);
  let access_token = '';
  let owners = ref([]);
  const details_loaded = ref(false);
  const transactions_loaded = ref(false);
  const transaction_data = ref([]);
  const dtt_options = {
    responsive: true,
  };
  const transaction_columns = [
    { data: 'date', title: 'Date', },
    { data: 'name', title: 'Name', },
    { data: 'amount', title: 'Amount', },
    { data: 'category', title: 'Categories', },
  ];

  onMounted(() => {
    checkConnected();
  });

  function checkConnected() {
    fetchConnectedBank();
  }

  async function getIdentity() {
    try {
      const response = await fetch(`${api_base_url}/api/plaid/get-account-identity?access_token=${access_token}&account_id=${props.account_id}`);
      const data = await response.json();
      owners.value = data.accounts[0].owners;
      console.log(data.accounts[0].owners);
      details_loaded.value = true;
    } catch (error) {
      console.error('Error fetching identity: ', error);
    }
  }

  async function loadMonthlyTransactions() {
    const start_date = moment().startOf('month').format('YYYY-MM-DD');
    const end_date   = moment().endOf('month').format('YYYY-MM-DD');
    
    fetch(`${api_base_url}/api/plaid/monthly-transactions`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ 
        account_id: props.account_id,
        access_token: access_token,
        start_date: start_date,
        end_date: end_date,
      }),
    })
    .then(response => response.json())
    .then(data => {
      transaction_data.value = data.transactions;
      transactions_loaded.value = true;
      is_loading.value = false;
    })
    .catch(error => {
      console.error('Error sending public token:', error);
    });
    
  }

  function fetchConnectedBank() {
    fetch(`${api_base_url}/api/up/get-connected-account`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ 
        account_id: props.account_id,
        bank_id: props.bank_id,
      })
    })
    .then(response => response.json())
    .then(data => {
      if(data.access_token) {
        access_token = data.access_token;
        getIdentity();
        loadMonthlyTransactions();
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
  }
</script>