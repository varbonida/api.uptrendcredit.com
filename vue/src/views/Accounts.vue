<template>
  <PageBreadcrumb pageTitle="Accounts" />

  <div v-if="is_connected">
    <div class="d-flex flex-column flex-md-row py-4 my-4 align-items-center justify-content-left border-bottom title-part-padding">
      <div>
        <h4 class="mb-0">{{ institutions.length }} Bank{{ institutions.length > 1 ? 's':'' }} Linked</h4>
      </div>
      <button class="btn btn-custom py-2 ms-auto no-border-radius" type="submit" @click="connectAccount">Add Another Bank</button>
    </div>
    <div class="accounts-container">
      <div v-for="(institution, ind) in institutions" class="row">
        <div class="col-md-6">
          <div class="card no-border-radius">
            <div class="card-header bg-main d-flex align-items-center no-border-radius py-3">
              <h4 class="card-title text-white">{{ institution.institution_name }}</h4>
              <div class="card-actions ms-auto d-flex button-group">
                <a class="link text-white d-flex align-items-center me-3" @click="accountCollapsed"
                  data-bs-toggle="collapse" 
                  :data-bs-target="`#panelsStayOpen-collapse-${ind}`" 
                  aria-expanded="true" 
                  :aria-controls="`panelsStayOpen-collapse-${ind}`">
                  <i class="fa-solid fa-minus"></i>
                </a>
                <a class=" mb-0 link d-flex text-white align-items-center pe-0" data-action="close" data-bs-toggle="tooltip" data-bs-title="Remove Bank">
                  <i class="fa-solid fa-xmark"></i>
                </a>
              </div>
            </div>
            <div class="card-body px-0 accordion-collapse collapse show"
              :id="`panelsStayOpen-collapse-${ind}`" 
              :aria-labelledby="`panelsStayOpen-heading-${ind}`">
              <div class="list-group list-group-flush">
                <a v-for="account in institution.accounts" href="#" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                  <img src="/src/assets/Uptrend-Credit_landscape_white.png" alt="twbs" width="32" height="32" class="rounded-circle flex-shrink-0">
                  <div class="d-flex gap-2 w-100 justify-content-between">
                    <div class=" flex-fill">
                      <h6 class="mb-0">{{ account.name }}</h6>
                    </div>
                    <router-link :to="`/accounts/${institution.id}/${account.account_id}`" class="btn btn-custom btn-sm no-border-radius">Details</router-link>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex flex-column flex-md-row p-4 gap-4 py-md-5 align-items-start justify-content-left" v-else>
    <div class="list-group">
      <button class="btn btn-lg btn-custom py-2 ms-auto no-border-radius" type="submit" @click="connectAccount">Connect to a Bank</button>
    </div>
  </div>
</template>

<style scoped>
</style>

<script setup>
  import { ref, onMounted } from 'vue';
  import PageBreadcrumb from '../components/_PageBreadcrumb.vue';
  import { Tooltip } from 'bootstrap';

  const api_base_url = import.meta.env.VITE_API_BASE_URL;
  let is_connected = ref();
  let linkToken = ref(null);
  let institutions = ref([]);

  onMounted(() => {
    checkConnected();
    new Tooltip(document.body, {
      selector: "[data-bs-toggle='tooltip']",
    });
  });

  function accountCollapsed(e) {
    let is_collapsed = false;
    is_collapsed = !is_collapsed;
    if(is_collapsed) {
      e.currentTarget.querySelector('i').classList.remove('fa-minus');
      e.currentTarget.querySelector('i').classList.add('fa-plus');
    } else {
      e.currentTarget.querySelector('i').classList.remove('fa-plus');
      e.currentTarget.querySelector('i').classList.add('fa-minus');
    }
  }
  function checkConnected() {
    fetchConnectedBanks();
  }

  async function fetchConnectedBanks() {
    try {
      const response = await fetch(`${api_base_url}/api/up/get-connected-banks`);
      const data = await response.json();
      
      if(data.length > 0) {
        institutions.value = data;
        is_connected.value = true;
      }
    } catch (error) {
      console.error('Error:', error);
    }
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