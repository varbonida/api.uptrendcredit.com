<template>
  <h1>Accounts</h1>
  <div v-if="is_connected">
    <div class="d-flex flex-column flex-md-row p-4 gap-4 py-md-5 align-items-start justify-content-left">
      <div>
        <h2 class="item__header-heading">{{ institutions.length }} Bank Linked</h2>
        <p class="item__header-subheading">Below is a list of all your connected banks. Click on a bank to view its associated accounts.</p>
      </div>
      <button class="btn btn-primary py-2 mt-4" type="submit" @click="connectAccount">Add Another Bank</button>
    </div>
    
    <div class="d-flex flex-column flex-md-row p-4 gap-4 py-md-5 align-items-start justify-content-left">
      <div class="p-2 flex-fill accordion" id="accordionPanelsStayOpenExample">
        <div v-for="(institution, ind) in institutions" class="accordion-item">
          <h2 class="accordion-header" :id="`panelsStayOpen-heading-${ind}`">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" :data-bs-target="`#panelsStayOpen-collapse-${ind}`" aria-expanded="true" :aria-controls="`panelsStayOpen-collapse-${ind}`">
              {{ institution.institution_name }}
            </button>
          </h2>
          <div :id="`panelsStayOpen-collapse-${ind}`" class="accordion-collapse collapse show" :aria-labelledby="`panelsStayOpen-heading-${ind}`">
            <div class="accordion-body">
              <div class="list-group">
                <a v-for="account in institution.accounts" href="#" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                  <img src="https://www.staging13.uptrendcredit.com/wp-content/uploads/2022/04/Uptrend-Credit_landscape-e1650562398898.png" alt="twbs" width="32" height="32" class="rounded-circle flex-shrink-0">
                  <div class="d-flex gap-2 w-100 justify-content-between">
                    <div @click="getIdentity(account.account_id, institution.access_token)" class=" flex-fill">
                      <h6 class="mb-0">{{ account.name }}</h6>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" @click="getTransactionThisMonth(account.account_id, institution.access_token)">Monthly Transactions</button>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex-fill" v-if="is_identity">
        <h5>{{ owners[0].names.join(' ') }}</h5>
        <div class="card mt-4" style="width: 18rem;">
          <div class="card-body">
            <h4 class="card-title">Emails</h4>
            <ul class="list-group list-group-flush">
              <li class="list-group-item" v-for="email in owners[0].emails">
                <strong v-if="email.primary">{{ email.data }}</strong>
                <span v-else>{{ email.data }}</span>
              </li>
            </ul>
          </div>
        </div>
        <div class="card mt-4" style="width: 18rem;">
          <div class="card-body">
            <h4 class="card-title">Phone Numbers</h4>
            <ul class="list-group list-group-flush">
              <li class="list-group-item" v-for="phone in owners[0].phone_numbers">
                <strong v-if="phone.primary">{{ phone.data }}<small>({{phone.type }})</small></strong>
                <span v-else>{{ phone.data }}</span><small>({{phone.type }})</small>
              </li>
            </ul>
          </div>
        </div>
        <div class="card mt-4" style="width: 18rem;">
          <div class="card-body">
            <h4 class="card-title">Addresses</h4>
            <ul class="list-group list-group-flush">
              <li class="list-group-item" v-for="address in owners[0].addresses">
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

      <div class="flex-fill" v-if="is_transactions">
        <div class="table-responsive small">
          <DataTable
            :columns="transaction_columns"
            :data = "transaction_data"
            class="table table-sm"
            id="transactions-table">

          </DataTable>
        </div>
      </div>
    </div>
  </div>




  <div class="d-flex flex-column flex-md-row p-4 gap-4 py-md-5 align-items-start justify-content-left" v-else>
    <div class="list-group">

      <button class="btn btn-primary w-100 py-2 mt-4" type="submit" @click="connectAccount">Connect a Bank</button>
    </div>
  </div>
</template>

<style scoped>
</style>

<script setup>
  import { ref, onMounted } from 'vue';
  import moment from 'moment';
  import DataTable from 'datatables.net-vue3';
  import DataTablesCore from 'datatables.net';

  DataTable.use(DataTablesCore);

  const api_base_url = "https://api.uptrendcredit.local";
  let is_connected = ref();
  let is_identity = ref();
  let owners = ref([]);
  let is_transactions = ref();
  let linkToken = ref(null);
  let institutions = ref([]);
  const transaction_data = ref([]);
  
  const transaction_columns = [
    { data: 'date', title: 'Date', },
    { data: 'name', title: 'Name', },
    { data: 'amount', title: 'Amount', },
    { data: 'category', title: 'Categories', },
  ];

  onMounted(checkConnected);

  function checkConnected() {
    const cb = fetchConnectedBanks();
    console.log('ay', import.meta.env.VITE_API_BASE_URL);
    // is_connected.value = false;
    // 'Access-Control-Allow-Origin' '*'
  }

  async function fetchConnectedBanks() {
    try {
      const response = await fetch(`${api_base_url}/api/up/get-connected-banks`);
      const data = await response.json();
      // accounts.value = data.accounts;
      if(data.length > 0) {
        institutions.value = data;
        is_connected.value = true;
      }

      // return fetch(`${api_base_url}/api/up/get-connected-banks`, {
      //   method: 'GET',
      //   headers: {
      //     'Content-Type': 'application/json',
      //     'Access-Control-Allow-Origin': '*',
      //   },
      // })
      // .then(response => response.json())
      // .then(data => {
      //   if(data.length > 0) {
      //     institutions.value = data;
      //     is_connected.value = true;
      //   }
      // })
      // .catch(error => {
      //   console.error('Error:', error);
      // });
    } catch (error) {
      console.error('Error:', error);
    }
  }

  async function getIdentity(account_id, access_token) {
    try {
      const response = await fetch(`${api_base_url}/api/plaid/get-account-identity?access_token=${access_token}&account_id=${account_id}`);
      const data = await response.json();
      owners.value = data.accounts[0].owners;
      console.log(data.accounts[0].owners)
    } catch (error) {
      console.error('Error fetching identity: ', error);
    }
    // getBalances(account_id);
    is_identity.value = true;
    is_transactions.value = false;
  }

  async function getTransactionThisMonth(account_id, access_token) {
    const start_date = moment().startOf('month').format('YYYY-MM-DD');
    const end_date   = moment().endOf('month').format('YYYY-MM-DD');
    fetch(`${api_base_url}/api/plaid/monthly-transactions`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ 
        account_id: account_id,
        access_token: access_token,
        start_date: start_date,
        end_date: end_date,
      }),
    })
    .then(response => response.json())
    .then(data => {
      transaction_data.value = data.transactions;
    })
    .catch(error => {
      console.error('Error sending public token:', error);
    });
    is_transactions.value = true;
    is_identity.value = false;

  }

  function connectAccount(e) {
    e.preventDefault();

    if (typeof Plaid === 'undefined') {
      console.error('Plaid library is not loaded');
      return;
    }

    if (typeof Plaid.create === 'undefined') {
      console.error('Plaid.create is not available');
      return;
    }

    if (linkToken) {
      generateLinkToken().then(() => {
        initializeLink();
      });
    } else {
      initializeLink();
    }
  }

  function generateLinkToken() {
    return fetch(`${api_base_url}/api/plaid/create-link-token`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
    })
    .then(response => response.json())
    .then(data => {
      linkToken.value = data.link_token;
      console.log(linkToken.value)
    })
    .catch(error => {
      console.error('Error generating link token:', error);
    });
  }

  function initializeLink() {
    const linkHandler = Plaid.create({
      token: linkToken.value,
      onSuccess: (publicToken, metadata) => {
        // Handle successful Plaid Link integration
        console.log('Public Token:', publicToken);
        console.log('Account ID:', metadata.account_id);
        
        exchangePublicToken(publicToken, metadata.institution, metadata.accounts);
      },
      onExit: () => {
        // Handle user exiting the Plaid Link integration
        console.log('Plaid Link closed');
      },
    });

    linkHandler.open();
  }

  function exchangePublicToken(publicToken, institution, accounts) {
    fetch(`${api_base_url}/api/plaid/exchange-public-token`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ 
        public_token: publicToken,
        institution_id: institution.institution_id,
        institution_name: institution.name,
        accounts: accounts,
      }),
    })
    .then(response => response.json())
    .then(data => {
      console.log(data);
    })
    .catch(error => {
      console.error('Error sending public token:', error);
    });
  }

</script>